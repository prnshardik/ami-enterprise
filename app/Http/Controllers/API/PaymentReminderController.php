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
                    $collection->whereRaw('payment_reminder.id IN (select MAX(id) FROM payment_reminder where DATE(next_date) < "'.date('Y-m-d').'" GROUP BY party_name)');
                }elseif($date == 'future'){
                    $collection->whereDate('payment_reminder.next_date', '>', date('Y-m-d'));
                    $collection->whereRaw('payment_reminder.id IN (select MAX(id) FROM payment_reminder where DATE(next_date) > "'.date('Y-m-d').'" GROUP BY party_name)');
                }else{
                    $collection->whereDate('payment_reminder.next_date', '=', date('Y-m-d'));
                    $collection->whereRaw('payment_reminder.id IN (select MAX(id) FROM payment_reminder where DATE(next_date) = "'.date('Y-m-d').'" GROUP BY party_name)');
                }
                
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

                if(!empty($request->all())){
                    $crud = [
                        'user_id' => auth('sanctum')->user()->id,
                        'party_name' => $request->party_name,
                        'note' => $request->note ?? NULL,
                        'mobile_no' => $request->mobile_no ?? NULL,
                        'date' => date('Y-m-d'),
                        'next_date' => $request->next_date,
                        'next_time' => $request->next_time,
                        'amount' => $request->amount ?? NULL,
                        'created_at' => date('Y-m-d H:i:s'),
                        'created_by' => auth('sanctum')->user()->id,
                        'updated_at' => date('Y-m-d H:i:s'),
                        'updated_by' => auth('sanctum')->user()->id
                    ];

                    $last_id = PaymentReminder::insertGetId($crud);

                    if($last_id)
                        return response()->json(['status' => 200, 'message' => 'Record added successfully']);
                    else
                        return response()->json(['status' => 201, 'message' => 'Failed to add record']);
                }else{
                    return response()->json(['status' => 201, 'message' => 'Something went wrong']);
                }
            }
        /** insert */
    }