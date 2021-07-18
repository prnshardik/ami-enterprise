<?php

    namespace App\Http\Controllers\API;

    use App\Http\Controllers\Controller;
    use Illuminate\Http\Request;
    use App\Models\Payment;
    use App\Models\PaymentAssign;
    use App\Models\PaymentReminder;
    use App\Models\User;

    use Auth, DB, Validator, File;

    class PaymentReminderController extends Controller{
        /** index */
            public function index(Request $request){
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
                }elseif($date == 'future'){
                    $collection->whereDate('payment_reminder.next_date', '>', date('Y-m-d'));
                }else{
                    $collection->whereDate('payment_reminder.next_date', '=', date('Y-m-d'));
                }
                
                $collection->where(['payment_reminder.is_last' => 'y']);
                
                $data = $collection->get();

                if($data->isNotEmpty())
                    return response()->json(['status' => 200, 'message' => 'Data found', 'data' => $data]);
                else
                    return response()->json(['status' => 201, 'message' => 'No records found']);
            }
        /** index */

        /** followup-detail */
            public function followup_detail(Request $request, $party_name = ''){
                if($party_name == '' || $party_name == null)
                    return response()->json(['status' => 422, 'message' => 'Please pass party name']);

                $data = PaymentReminder::select('payment_reminder.id', 'payment_reminder.date', 'payment_reminder.amount', 'payment_reminder.next_date', 
                                                                    'payment_reminder.next_time', 'payment_reminder.note', 'u.name as user_name')
                                                ->leftjoin('users as u', 'payment_reminder.user_id', 'u.id')
                                                ->where(['payment_reminder.party_name' => $party_name])
                                                ->get();

                if($data->isNotEmpty())
                    return response()->json(['status' => 200, 'message' => 'Data found', 'data' => $data]);
                else
                    return response()->json(['status' => 201, 'message' => 'No records found']);
            }
        /** followup-detail */

        /** payment-detail */
            public function payment_detail(Request $request, $party_name = ''){
                if($party_name == '' || $party_name == null)
                    return response()->json(['status' => 422, 'message' => 'Please pass party name']);
                    
                $data = Payment::select('bill_no', 'bill_date', 'bill_amount')->where(['party_name' => $party_name])->get();

                if($data->isNotEmpty())
                    return response()->json(['status' => 200, 'message' => 'Data found', 'data' => $data]);
                else
                    return response()->json(['status' => 201, 'message' => 'No records found']);
            }
        /** payment-detail */

        /** insert */
            public function insert(Request $request){
                $rules = [
                    'party_name' => 'required',
                    'next_date' => 'required',
                    'next_time' => 'required'
                ];

                $validator = Validator::make($request->all(), $rules);

                if($validator->fails())
                    return response()->json(['status' => 422, 'message' => $validator->errors()]);

                DB::beginTransaction();
                try {
                    $update = PaymentReminder::where(['party_name' => $request->party_name])->update(['is_last' => 'n']);

                    if($update){
                        $crud = [
                            'user_id' => auth()->user()->id,
                            'party_name' => $request->party_name,
                            'note' => $request->note ?? NULL,
                            'mobile_no' => $request->mobile_no ?? NULL,
                            'date' => date('Y-m-d'),
                            'next_date' => $request->next_date,
                            'next_time' => $request->next_time,
                            'is_last' => 'y',
                            'amount' => $request->amount ?? NULL,
                            'created_at' => date('Y-m-d H:i:s'),
                            'created_by' => auth()->user()->id,
                            'updated_at' => date('Y-m-d H:i:s'),
                            'updated_by' => auth()->user()->id
                        ];

                        $last_id = PaymentReminder::insertGetId($crud);
                        
                        if($last_id){
                            DB::commit();
                            return response()->json(['status' => 200, 'message' => 'Record added successfully']);
                        }else{
                            DB::rollback();
                            return response()->json(['status' => 201, 'message' => 'Failed to add record']);
                        }
                    }else{
                        DB::rollback();
                        return response()->json(['status' => 201, 'message' => 'Failed to add record']);
                    }
                }catch (\Exception $e) {
                    DB::rollback();
                    return response()->json(['status' => 201, 'message' => 'Something went wrong']);
                }
            }
        /** insert */
    }