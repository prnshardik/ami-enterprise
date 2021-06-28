<?php    

    namespace App\Http\Controllers;

    use Illuminate\Http\Request;
    use App\Models\Payment;
    use App\Models\PaymentAssign;
    use App\Models\PaymentReminder;
    use App\Models\User;
    use Illuminate\Support\Str;
    use App\Http\Requests\PaymentRequest;
    use App\Imports\PaymentImport;
    use Auth, Validator, DB, Mail, DataTables, Excel;

    class PaymentController extends Controller{
        /** index */
            public function index(Request $request){
                if($request->ajax()){
                    $type = $request->type;
                    $start_date = $request->start_date;
                    $end_date = $request->end_date;

                    $collection = Payment::select('id', 'party_name', 'bill_date', 'balance_amount', 'mobile_no', DB::Raw("null as note"), DB::Raw("null as reminder"))
                                    ->whereRaw('id IN (select MAX(id) FROM payments GROUP BY party_name)');
                    
                    if($start_date && $end_date)
                        $collection->whereBetween('bill_date', [$start_date, $end_date]);

                    $data = $collection->get();

                    if($data->isNotEmpty()){
                        foreach($data as $row){
                            $assigned = PaymentAssign::select('payment_assign.note', 'u.name as reminder')
                                                    ->leftjoin('users as u', 'u.id', 'payment_assign.user_id')
                                                    ->where(['payment_assign.party_name' => $row->party_name])
                                                    ->orderBy('payment_assign.id', 'desc')
                                                    ->first();
                                    
                            if($assigned){
                                $row->note = $assigned->note;
                                $row->reminder = $assigned->reminder;
                            }
                        }
                    }

                    return Datatables::of($data)
                            ->addIndexColumn()
                            ->addColumn('action', function($data){
                                $rec = Payment::select('bill_no', 'bill_date', 'bill_amount')->where(['party_name' => $data->party_name])->get();
                                $assigned = PaymentAssign::select('id', 'note', 'user_id', 'date')->where(['party_name' => $data->party_name])->orderBy('id', 'desc')->first();

                                $user_id = null;
                                $note = null;
                                $date = null;
                                $assign_id = null;
                                if($assigned){
                                    $user_id = $assigned->user_id;
                                    $note = $assigned->note;
                                    $date = $assigned->date;
                                    $assign_id = $assigned->id;
                                }

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

                                $users = User::select('id', 'name')->where(['is_admin' => 'n', 'status' => 'active'])->get();
                                
                                $usersList = '<option value="">Selet user</option>';
                                if($users->isNotEmpty()){
                                    foreach($users as $u){
                                        $select = '';
                                        if($u->id == $user_id)
                                            $select = 'selected';                                        

                                        $usersList .= "<option value='$u->id' $select>$u->name</option>";
                                    }
                                }

                                $form = "<div class='row'>
                                            <input type='hidden' value='$data->party_name' id='party_name$data->id' />
                                            <input type='hidden' value='$assign_id' id='assign_id' />
                                            <div class='form-group col-sm-12'>
                                                <label for='date$data->id'>Date <span class='text-danger'>*</span></label>
                                                <input type='date' name='date$data->id' id='date$data->id' class='form-control' value='$date' style='max-width: 90%;'/>
                                                <span class='kt-form__help error date$data->id'></span>
                                            </div>
                                            <div class='form-group col-sm-12'>
                                                <label for='user$data->id'>User <span class='text-danger'>*</span></label>
                                                <select name='user$data->id' id='user$data->id' class='form-control' style='max-width: 90%;'>
                                                    $usersList
                                                </select>
                                                <span class='kt-form__help error user$data->id'></span>
                                            </div>
                                            <div class='form-group col-sm-12'>
                                                <label for='note'>Note </label>
                                                <textarea type='note' name='note$data->id' id='note$data->id' class='form-control' style='max-width: 90%;'/>$note</textarea>
                                                <span class='kt-form__help error note$data->id'></span>
                                            </div>
                                        </div>";

                                return  '<div class="btn-group">
                                            <button type="button" class="btn btn-default btn-xs" data-toggle="modal" data-target="#infoModal'.$data->id.'">
                                                <i class="fa fa-exclamation-circle"></i>
                                            </button> &nbsp;
                                            <button type="button" class="btn btn-default btn-xs" data-toggle="modal" data-target="#assignModal'.$data->id.'">
                                                <i class="fa fa-legal"></i>
                                            </button> &nbsp;
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

                                        <div class="modal fade" id="assignModal'.$data->id.'" tabindex="-1" role="dialog" aria-labelledby="assignModalLabel'.$data->id.'" aria-hidden="true">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="assignModalLabel'.$data->id.'">'.$data->party_name.'</h5>
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
                                        </div>';
                            })
                            ->rawColumns(['action'])
                            ->make(true);
                }

                return view('payment.index');
            }
        /** index */

        /** assign */
            public function assign(Request $request){
                if(!$request->ajax()){ return redirect()->back()->with(['error', 'something went wrong.']); }

                $validator = Validator::make(
                                                ['user' => $request->user, 'party_name' => $request->party_name, 'date' => $request->date, 'note' => $request->note],
                                                ['user' => 'required', 'party_name' => 'required', 'date' => 'required']
                                            );

                if($validator->fails()){
                    return response()->json($validator->errors(), 422);
                }else{
                    DB::beginTransaction();
                    try {
                        if($request->assign_id != '' ||  $request->assign_id != null){
                            $crud = [
                                'user_id' => $request->user, 
                                'date' => $request->date, 
                                'note' => $request->note ?? NULL,
                                'updated_at' => date('Y-m-d H:i:s'),
                                'updated_by' => auth()->user()->id
                            ];

                            $update = PaymentAssign::where(['id' => $request->assign_id])->update($crud);

                            if($update){
                                $payment_reminder = PaymentReminder::select('id')->where(['party_name' => $request->party_name])->orderBy('id', 'desc')->first();

                                $remoder_crud = [
                                    'user_id' => $request->user, 
                                    'date' => date('Y-m-d H:i:s'), 
                                    'next_date' => $request->date, 
                                    'next_time' => '00:00', 
                                    'note' => $request->note ?? NULL,
                                    'amount' => NULL,
                                    'updated_at' => date('Y-m-d H:i:s'),
                                    'updated_by' => auth()->user()->id
                                ];

                                $remider_update = PaymentReminder::where(['id' => $payment_reminder->id])->update($crud);

                                if($remider_update){
                                    DB::commit();
                                    return response()->json(['code' => 200, 'message' => 'User assigned updated successfully']);
                                }else{
                                    DB::rollback();
                                    return response()->json(['code' => 201, 'message' => 'Failed to update remider']);   
                                }
                            }else{
                                DB::rollback();
                                return response()->json(['code' => 201, 'message' => 'Failed to update assign']);   
                            }
                        }else{
                            $crud = [
                                'user_id' => $request->user, 
                                'party_name' => $request->party_name,
                                'date' => $request->date, 
                                'note' => $request->note ?? NULL,
                                'created_at' => date('Y-m-d H:i:s'),
                                'created_by' => auth()->user()->id,
                                'updated_at' => date('Y-m-d H:i:s'),
                                'updated_by' => auth()->user()->id
                            ];

                            $last_id = PaymentAssign::insertGetId($crud);

                            if($last_id){
                                $payment = Payment::select('mobile_no')->where(['party_name' => $request->party_name])->where('mobile_no', '!=', NULL)->first();

                                $mobile_no = NULL;
                                if($payment)
                                    $mobile_no = $payment->mobile_no;

                                $remoder_crud = [
                                    'user_id' => $request->user, 
                                    'party_name' => $request->party_name,
                                    'mobile_no' => $request->mobile_no,
                                    'date' => date('Y-m-d H:i:s'), 
                                    'next_date' => $request->date, 
                                    'next_time' => '00:00', 
                                    'note' => $request->note ?? NULL,
                                    'amount' => NULL,
                                    'created_at' => date('Y-m-d H:i:s'),
                                    'created_by' => auth()->user()->id,
                                    'updated_at' => date('Y-m-d H:i:s'),
                                    'updated_by' => auth()->user()->id
                                ];

                                $remider_id = PaymentReminder::insertGetId($crud);

                                if($remider_id){
                                    DB::commit();
                                    return response()->json(['code' => 200, 'message' => 'User assigned successfully']);
                                }else{
                                    DB::rollback();
                                    return response()->json(['code' => 201, 'message' => 'Failed to insert reminder']);
                                }
                            }else{
                                DB::rollback();
                                return response()->json(['code' => 201, 'message' => 'Failed to assign user']);
                            }
                        }
                    } catch (\Exception $e) {
                        DB::rollback();
                        return response()->json(['code' => 201, 'message' => 'Something went wrong']);
                    }
                }
            }
        /** assign */

        /** import-view */
            public function file_import(){
                return view('payment.import');
            }
        /** import-view */

        /** import */
            public function import(PaymentRequest $request){
                DB::table('payments')->truncate();
                DB::statement("ALTER TABLE payments AUTO_INCREMENT = 1");

                Excel::import(new PaymentImport, $request->file('file'));

                return redirect()->route('payment');
            }
        /** import */
    }