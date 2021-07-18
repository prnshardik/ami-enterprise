<?php

    namespace App\Http\Controllers\API;

    use App\Http\Controllers\Controller;
    use Illuminate\Http\Request;
    use App\Models\Payment;
    use App\Models\PaymentAssign;
    use App\Models\PaymentReminder;
    use App\Models\User;

    use Auth, DB, Validator, File;

    class PaymentController extends Controller{
        /** index */
            public function index(Request $request){
                $type = $request->type ?? NULL;
                $start_date = $request->start_date ?? null;
                $end_date = $request->end_date ?? null;

                $collection = Payment::select('id', 'party_name', 'bill_date', 'balance_amount', 'mobile_no', DB::Raw("null as note"), DB::Raw("null as reminder"))
                                        ->whereRaw('id IN (select MAX(id) FROM payments GROUP BY party_name)');
                
                if($start_date && $end_date){
                    $collection->whereIn('party_name', function($query) use($start_date, $end_date){
                                            $query->select('party_name')
                                                ->from(with(new PaymentReminder)->getTable())
                                                ->whereBetween('next_date', [$start_date, $end_date]);
                                        });
                }

                if($type && $type == 'assigned'){
                    $collection->whereIn('party_name', function($query){
                                            $query->select('party_name')
                                                ->from(with(new PaymentAssign)->getTable());
                                        });
                }elseif($type && $type == 'not_assigned'){
                    $collection->whereNotIn('party_name', function($query){
                                            $query->select('party_name')
                                                ->from(with(new PaymentAssign)->getTable());
                                        });
                }elseif($type && $type == 'all'){
                    
                }else{
                    $collection->whereIn('party_name', function($query) use ($type){
                                            $query->select('party_name')
                                                ->from(with(new PaymentAssign)->getTable())
                                                ->where(['user_id' => $type]);
                                        });
                }

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
                        }else{
                            $row->note = '';
                            $row->reminder = '';
                        }

                        $remider = PaymentReminder::select('payment_reminder.note')
                                                    ->where(['payment_reminder.party_name' => $row->party_name])
                                                    ->orderBy('payment_reminder.next_date', 'desc')
                                                    ->first();

                        if($remider){
                            $row->note = $remider->note;
                        }

                        $assigned = [];

                        $assignedData = PaymentAssign::select('id', 'note', 'user_id', 'date')->where(['party_name' => $row->party_name])->orderBy('id', 'desc')->first();
                        
                        if($assignedData){
                            $assigned['user_id'] = $assignedData->user_id;
                            $assigned['note'] = $assignedData->note;
                            $assigned['date'] = $assignedData->date;
                            $assigned['assign_id'] = $assignedData->id;
                        }

                        $row->assigned = $assigned;
                    }
                }

                if($data->isNotEmpty())
                    return response()->json(['status' => 200, 'message' => 'Data found', 'data' => $data]);
                else
                    return response()->json(['status' => 201, 'message' => 'No records found']);
            }
        /** index */

        /** detail */
            public function detail(Request $request, $party_name = ''){
                if($party_name == '' || $party_name == null)
                    return response()->json(['status' => 422, 'message' => 'Please pass party name']);

                $data = Payment::select('bill_no', 'bill_date', 'bill_amount')->where(['party_name' => $party_name])->get();

                if($data->isNotEmpty())
                    return response()->json(['status' => 200, 'message' => 'Data found', 'data' => $data]);
                else
                    return response()->json(['status' => 201, 'message' => 'No records found']);
            }
        /** detail */

        /** users */
            public function users(Request $request){
                $data = User::select('id', 'name')->where(['is_admin' => 'n', 'status' => 'active'])->get();

                if($data->isNotEmpty())
                    return response()->json(['status' => 200, 'message' => 'Data found', 'data' => $data]);
                else
                    return response()->json(['status' => 201, 'message' => 'No records found']);
            }
        /** users */

        /** assign */
            public function assign(Request $request){
                $rules = [
                    'user' => 'required',
                    'party_name' => 'required',
                    'date' => 'required'
                ];

                $validator = Validator::make($request->all(), $rules);

                if($validator->fails())
                    return response()->json(['status' => 422, 'message' => $validator->errors()]);

                DB::beginTransaction();
                try {
                    if($request->assign_id != '' ||  $request->assign_id != null){
                        $crud = [
                            'user_id' => $request->user, 
                            'date' => $request->date, 
                            'note' => $request->note ?? NULL,
                            'updated_at' => date('Y-m-d H:i:s'),
                            'updated_by' => auth('sanctum')->user()->id
                        ];

                        $update = PaymentAssign::where(['id' => $request->assign_id])->update($crud);

                        if($update){
                            $payment_reminder = PaymentReminder::select('id')->where(['party_name' => $request->party_name])->orderBy('id', 'desc')->first();

                            $remider_crud = [
                                'user_id' => $request->user, 
                                'date' => date('Y-m-d H:i:s'), 
                                'next_date' => $request->date ?? date('Y-m-d H:i:s'), 
                                'next_time' => '00:00', 
                                'note' => $request->note ?? NULL,
                                'amount' => NULL,
                                'updated_at' => date('Y-m-d H:i:s'),
                                'updated_by' => auth('sanctum')->user()->id
                            ];

                            $remider_update = PaymentReminder::where(['id' => $payment_reminder->id])->update($remider_crud);

                            if($remider_update){
                                DB::commit();
                                return response()->json(['status' => 200, 'message' => 'User assigned updated successfully']);
                            }else{
                                DB::rollback();
                                return response()->json(['status' => 201, 'message' => 'Failed to update remider']);   
                            }
                        }else{
                            DB::rollback();
                            return response()->json(['code' => status, 'message' => 'Failed to update assign']);   
                        }
                    }else{
                        $crud = [
                            'user_id' => $request->user, 
                            'party_name' => $request->party_name,
                            'date' => $request->date, 
                            'note' => $request->note ?? NULL,
                            'created_at' => date('Y-m-d H:i:s'),
                            'created_by' => auth('sanctum')->user()->id,
                            'updated_at' => date('Y-m-d H:i:s'),
                            'updated_by' => auth('sanctum')->user()->id
                        ];

                        $last_id = PaymentAssign::insertGetId($crud);

                        if($last_id){
                            $payment = Payment::select('mobile_no')->where(['party_name' => $request->party_name])->where('mobile_no', '!=', NULL)->first();

                            $mobile_no = NULL;
                            if($payment)
                                $mobile_no = $payment->mobile_no;

                            $remider_crud = [
                                'user_id' => $request->user, 
                                'party_name' => $request->party_name,
                                'mobile_no' => $request->mobile_no,
                                'date' => date('Y-m-d H:i:s'), 
                                'next_date' => $request->date ?? date('Y-m-d H:i:s'), 
                                'next_time' => '00:00', 
                                'is_last' => 'y',
                                'note' => $request->note ?? NULL,
                                'amount' => NULL,
                                'created_at' => date('Y-m-d H:i:s'),
                                'created_by' => auth('sanctum')->user()->id,
                                'updated_at' => date('Y-m-d H:i:s'),
                                'updated_by' => auth('sanctum')->user()->id
                            ];

                            $remider_id = PaymentReminder::insertGetId($remider_crud);

                            if($remider_id){
                                DB::commit();
                                return response()->json(['status' => 200, 'message' => 'User assigned successfully']);
                            }else{
                                DB::rollback();
                                return response()->json(['status' => 201, 'message' => 'Failed to insert reminder']);
                            }
                        }else{
                            DB::rollback();
                            return response()->json(['status' => 201, 'message' => 'Failed to assign user']);
                        }
                    }
                } catch (\Exception $e) {
                    DB::rollback();
                    return response()->json(['status' => 201, 'message' => 'Something went wrong']);
                }
            }
        /** assign */
    }