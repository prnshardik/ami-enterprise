<?php
    namespace App\Http\Controllers;

    use Illuminate\Http\Request;
    use App\Models\User;
    use App\Models\Payment;
    use App\Models\PaymentAssign;
    use App\Models\PaymentReminder;
    use Illuminate\Support\Str;
    use Auth, Validator, DB, Mail, DataTables, File;

    class PaymentReminderController extends Controller{
        /** index */
            public function index(Request $request){
                if($request->ajax()){
                    $date = $request->date ?? 'today';

                    $collection = PaymentReminder::select('payment_reminder.id', 'payment_reminder.party_name' , 'payment_reminder.mobile_no', 'payment_reminder.date', 'payment_reminder.next_date', 
                                                        'payment_reminder.amount', 'payment_reminder.note', 'u.name as user_name'
                                                    )
                                                    ->leftjoin('users as u', 'payment_reminder.user_id', 'u.id');
                                                    
                    $collection->whereIn('payment_reminder.party_name', function($query){
                        $query->select('party_name')
                            ->from(with(new Payment)->getTable());
                    });

                    if($date == 'past'){
                        $collection->whereDate('payment_reminder.next_date', '<', date('Y-m-d'));
                        $collection->whereRaw('payment_reminder.id IN (select MAX(id) FROM payment_reminder where DATE(next_date) < "'.date('Y-m-d').'" GROUP BY party_name)');
                    }elseif($date == 'future'){
                        $collection->whereDate('payment_reminder.next_date', '>', date('Y-m-d'));
                        $collection->whereRaw('payment_reminder.id IN (select MAX(id) FROM payment_reminder where DATE(next_date) > "'.date('Y-m-d').'" GROUP BY party_name)');
                    }else{
                        $collection->whereDate('payment_reminder.next_date', '=', date('Y-m-d'));
                        $collection->whereRaw('payment_reminder.id IN (select MAX(id) FROM payment_reminder where DATE(next_date) = "'.date('Y-m-d').'" GROUP BY party_name)');
                    }
                    
                    $data = $collection->get();

                    return Datatables::of($data)
                            ->addIndexColumn()
                            ->addColumn('action', function($data){
                                $rec = Payment::select('bill_no', 'bill_date', 'bill_amount')->where(['party_name' => $data->party_name])->get();

                                $info = "<table class='table table-bordered'>
                                            <thead class='thead-default'>
                                                <tr>
                                                    <th>Sr. No</th>
                                                    <th>Bill No</th>
                                                    <th>Bill Date</th>
                                                    <th>Bill Amount</th>
                                                </tr>
                                            </thead>
                                            <tbody>";

                                if($rec->isNotEmpty()){
                                    $i=1;
                                    foreach($rec as $r){
                                        $info .= "<tr>
                                                    <td>$i</td>
                                                    <td>$r->bill_no</td>
                                                    <td>$r->bill_date</td>
                                                    <td>$r->bill_amount</td>
                                                </tr>";
                                        $i++;
                                    }
                                }
                                $info .="</tbody></table>";

                                $reminders = PaymentReminder::select('payment_reminder.id', 'payment_reminder.date', 'payment_reminder.amount', 'payment_reminder.next_date', 
                                                                    'payment_reminder.next_time', 'payment_reminder.note', 'u.name as user_name')
                                                                    ->leftjoin('users as u', 'payment_reminder.user_id', 'u.id')
                                                                    ->where(['payment_reminder.party_name' => $data->party_name])
                                                                    ->get();

                                $details = '';
                                if($reminders->isNotEmpty()){
                                    $details .= "<ul class='media-list media-list-divider m-0'>";
                                        foreach($reminders as $row){
                                            $details .= "<li class='media followup_details'>
                                                            <div class='media-body'>
                                                                <div class='media-heading'>
                                                                    $row->user_name
                                                                    <span class='font-13 float-right'>$row->date</span>
                                                                </div>
                                                                <div class='font-13'>$row->note</div>
                                                                <div class='font-13 text-danger'>Next Follow-up On $row->next_date $row->next_time</div>
                                                            </div>
                                                        </li>
                                                        <br/>";
                                        }
                                    $details .= "</ul>"; 
                                }else{
                                    $details = '<div class="row"><div class="col-sm-12 text-center"><h1>No Reminders Yet</h1></div></div>';
                                }

                                $form = "<div class='row'>
                                            <input type='hidden' value='$data->party_name' id='party_name$data->id' />
                                            <div class='form-group col-sm-12'>
                                                <label for='note'>Note </label>
                                                <textarea type='note' name='note$data->id' id='note$data->id' class='form-control' style='max-width: 90%;'/></textarea>
                                                <span class='kt-form__help error note$data->id'></span>
                                            </div>
                                            <div class='form-group col-sm-5'>
                                                <label for='next_date$data->id'>Next date <span class='text-danger'>*</span></label>
                                                <input type='date' name='next_date$data->id' id='next_date$data->id' class='form-control' style='max-width: 90%;'>
                                                <span class='kt-form__help error next_date$data->id'></span>
                                            </div>
                                            <div class='form-group col-sm-5'>
                                                <label for='next_time$data->id'>Next time <span class='text-danger'>*</span></label>
                                                <input type='time' name='next_time$data->id' id='next_time$data->id' class='form-control' style='max-width: 90%;'>
                                                <span class='kt-form__help error next_time$data->id'></span>
                                            </div>
                                            <div class='form-group col-sm-12'>
                                                <label for='mobile_no$data->id'>Mobile no </label>
                                                <input type='text' name='mobile_no$data->id' id='mobile_no$data->id' class='form-control digit' style='max-width: 90%;'/>
                                                <span class='kt-form__help error mobile_no$data->id'></span>
                                            </div>
                                            <div class='form-group col-sm-12'>
                                                <label for='amount$data->id'>Amount </label>
                                                <input type='text' name='amount$data->id' id='amount$data->id' class='form-control digit' style='max-width: 90%;'/>
                                                <span class='kt-form__help error amount$data->id'></span>
                                            </div>
                                        </div>";

                                return ' <div class="btn-group">
                                                <button type="button" class="btn btn-default btn-xs" data-toggle="modal" data-target="#followup'.$data->id.'">
                                                    <i class="fa fa-plus"></i>
                                                </button> &nbsp;

                                                <button type="button" class="btn btn-default btn-xs" data-toggle="modal" data-target="#details'.$data->id.'">
                                                    <i class="fa fa-exclamation-circle"></i>
                                                </button> &nbsp;

                                                <button type="button" class="btn btn-default btn-xs" data-toggle="modal" data-target="#infoModal'.$data->id.'">
                                                    <i class="fa fa-file-text"></i>
                                                </button> &nbsp;

                                                <a href="javascript:;" class="btn btn-default btn-xs" onclick="change_status(this);" data-name="'.$data->party_name.'" data-status="deleted" data-id="'.base64_encode($data->id).'">
                                                    <i class="fa fa-trash"></i>
                                                </a> &nbsp;

                                                <div class="modal fade" id="followup'.$data->id.'" tabindex="-1" role="dialog" aria-labelledby="examplefollowup'.$data->id.'" aria-hidden="true">
                                                    <div class="modal-dialog" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="examplefollowup'.$data->id.'">New Followup - '.$data->party_name.'</h5>
                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <form class="form" id='.$data->id.'>
                                                                <div class="modal-body">'.$form.'</div>
                                                                <div class="modal-footer">
                                                                    <button type="submit" class="btn btn-primary">Save</button>
                                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="modal fade" id="details'.$data->id.'" tabindex="-1" role="dialog" aria-labelledby="exampledetails'.$data->id.'" aria-hidden="true">
                                                    <div class="modal-dialog" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="exampledetails'.$data->id.'">Followup Details - '.$data->party_name.'</h5>
                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <form class="form" id='.$data->id.'>
                                                                <div class="modal-body">'.$details.'</div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="modal fade" id="infoModal'.$data->id.'" tabindex="-1" role="dialog" aria-labelledby="infoModalLabel'.$data->id.'" aria-hidden="true">
                                                    <div class="modal-dialog" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="infoModalLabel'.$data->id.'">'.$data->party_name.'</h5>
                                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">'.$info.'</div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>';
                                            })

                            ->editColumn('next_date',function($data){
                                return date('d-m-Y' ,strtotime($data->next_date));
                            })
                            
                            ->editColumn('date',function($data){
                                return date('d-m-Y' ,strtotime($data->date));
                            })
                            
                            ->rawColumns(['action', 'next_date', 'date'])
                            ->make(true);
                }

                return view('payment_reminder.index');
            }
        /** index */

        /** insert */
            public function insert(Request $request){
                if(!$request->ajax()){ return true; }
                
                $validator = Validator::make(
                                            ['party_name' => $request->party_name, 'next_date' => $request->next_date, 'next_time' => $request->next_time],
                                            ['party_name' => 'required', 'next_date' => 'required', 'next_time' => 'required']
                                        );

                if($validator->fails()){
                    return response()->json($validator->errors(), 422);
                }else{
                    if(!empty($request->all())){
                        $crud = [
                            'user_id' => auth()->user()->id,
                            'party_name' => $request->party_name,
                            'note' => $request->note ?? NULL,
                            'mobile_no' => $request->mobile_no ?? NULL,
                            'date' => date('Y-m-d'),
                            'next_date' => $request->next_date,
                            'next_time' => $request->next_time,
                            'amount' => $request->amount ?? NULL,
                            'created_at' => date('Y-m-d H:i:s'),
                            'created_by' => auth()->user()->id,
                            'updated_at' => date('Y-m-d H:i:s'),
                            'updated_by' => auth()->user()->id
                        ];

                        $last_id = PaymentReminder::insertGetId($crud);
                        
                        if($last_id)
                            return response()->json(['code' => 200, 'message' => 'Record added successfully']);
                        else
                            return response()->json(['code' => 201, 'message' => 'Failed to add record']);
                    }else{
                        return response()->json(['code' => 201, 'message' => 'Something went wrong']);
                    }
                }
            }
        /** insert */

        /** change-status */
            public function change_status(Request $request){
                if(!$request->ajax()){ exit('No direct script access allowed'); }
                
                if(!empty($request->all())){
                    $id = base64_decode($request->id);
                    $status = $request->status;
                    $name = $request->name;

                    DB::beginTransaction();
                    try {
                        $paymentDelete = Payment::where(['party_name' => $name])->delete();
                        
                        if($paymentDelete){
                            $assignDelete = PaymentAssign::where(['party_name' => $name])->delete();

                            if($assignDelete){
                                $reminderDelete = PaymentReminder::where(['party_name' => $name])->delete();

                                if($reminderDelete){
                                    DB::commit();
                                    return response()->json(['code' => 200]);
                                }else{
                                    DB::rollback();
                                    return response()->json(['code' => 201]);
                                }
                            }else{
                                DB::rollback();
                                return response()->json(['code' => 201]);
                            }
                        }else{
                            DB::rollback();
                            return response()->json(['code' => 201]);
                        }
                    } catch (\Exception $e) {
                        DB::rollback();
                        return response()->json(['code' => 201]);
                    }
                }else{
                    return response()->json(['code' => 201]);
                }
            }
        /** change-status */
    }