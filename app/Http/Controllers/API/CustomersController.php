<?php

    namespace App\Http\Controllers\API;

    use App\Http\Controllers\Controller;
    use Illuminate\Http\Request;
    use App\Models\Customer;
    use Auth, DB, Validator, File;

    class CustomersController extends Controller{
        /** customers */
            public function customers(Request $request){
                $data = Customer::select('id', 'party_name', 'billing_name', 'contact_person', 'mobile_number', 'billing_address', 'delivery_address',
                                    'electrician', 'electrician_number', 'architect', 'architect_number', 'office_contact_person', 'status'
                                )
                                ->get();

                if($data->isNotEmpty())
                    return response()->json(['status' => 200, 'message' => 'Data founf', 'data' => $data]);
                else
                    return response()->json(['status' => 201, 'message' => 'No records found']);
            }
        /** customers */

        /** customer */
            public function customer(Request $request, $id){
                $data = Customer::select('id', 'party_name', 'billing_name', 'contact_person', 'mobile_number', 'billing_address', 'delivery_address',
                                        'electrician', 'electrician_number', 'architect', 'architect_number', 'office_contact_person', 'status'
                                    )
                                    ->where(['id' => $id])
                                    ->first();

                if(!empty($data))
                    return response()->json(['status' => 200, 'message' => 'Data founf', 'data' => $data]);
                else
                    return response()->json(['status' => 201, 'message' => 'No record found']);
            }
        /** customer */

        /** insert */
            public function insert(Request $request){
                $rules = [
                    'party_name' => 'required|unique:customers,party_name'
                ];

                $validator = Validator::make($request->all(), $rules);

                if($validator->fails())
                    return response()->json(['status' => 422, 'message' => $validator->errors()]);

                $crud = [
                    'party_name' => ucfirst($request->party_name),
                    'billing_name' => $request->billing_name ?? NULL,
                    'contact_person' => $request->contact_person ?? NULL,
                    'mobile_number' => $request->mobile_number ?? NULL,
                    'billing_address' => $request->billing_address ?? NULL,
                    'delivery_address' => $request->delivery_address ?? NULL,
                    'electrician' => $request->electrician ?? null,
                    'electrician_number' => $request->electrician_number ?? null,
                    'architect' => $request->architect ?? null,
                    'architect_number' => $request->architect_number ?? null,
                    'office_contact_person' => $request->office_contact_person ?? null,
                    'status' => 'active',
                    'created_at' => date('Y-m-d H:i:s'),
                    'created_by' => auth('sanctum')->user()->id,
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_by' => auth('sanctum')->user()->id
                ];

                $last_id = Customer::insertGetId($crud);

                if($last_id)
                    return response()->json(['status' => 200, 'message' => 'Records added successfully']);
                else
                    return response()->json(['status' => 201, 'message' => 'Something went wrong.']);
            }
        /** insert */

        /** update */
            public function update(Request $request){
                $rules = [
                    'id' => 'required',
                    'party_name' => 'required|unique:customers,party_name,'.$request->id
                ];

                $validator = Validator::make($request->all(), $rules);

                if($validator->fails())
                    return response()->json(['status' => 422, 'message' => $validator->errors()]);

                $exst_data = Customer::where(['id' => $request->id])->first();

                $crud = [
                    'party_name' => ucfirst($request->party_name),
                    'billing_name' => $request->billing_name ?? NULL,
                    'contact_person' => $request->contact_person ?? NULL,
                    'mobile_number' => $request->mobile_number ?? NULL,
                    'billing_address' => $request->billing_address ?? NULL,
                    'delivery_address' => $request->delivery_address ?? NULL,
                    'electrician' => $request->electrician ?? null,
                    'electrician_number' => $request->electrician_number ?? null,
                    'architect' => $request->architect ?? null,
                    'architect_number' => $request->architect_number ?? null,
                    'office_contact_person' => $request->office_contact_person ?? null,
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_by' => auth('sanctum')->user()->id
                ];

                $update = Customer::where(['id' => $request->id])->update($crud);

                if($update)
                    return response()->json(['status' => 200, 'message' => 'Record updated successfully']);
                else
                    return response()->json(['status' => 201, 'message' => 'Something went wrong']);
            }
        /** update */

        /** change-status */
            public function change_status(Request $request){
                $rules = [
                    'id' => 'required',
                    'status' => 'required'
                ];

                $validator = Validator::make($request->all(), $rules);

                if($validator->fails())
                    return response()->json(['status' => 422, 'message' => $validator->errors()]);

                $data = Customer::where(['id' => $request->id])->first();

                if(!empty($data)){
                    if($request->status == 'deleted'){
                        $update = Customer::where('id', $request->id)->delete();

                        if($update)
                            return response()->json(['status' => 200 ,'message' => 'Record deleted successfully']);
                        else
                            return response()->json(['status' => 201, 'message' => 'Faild to delete record']);
                    }else{
                        $update = Customer::where(['id' => $request->id])->update(['status' => $request->status, 'updated_at' => date('Y-m-d H:i:s'), 'updated_by' => auth('sanctum')->user()->id]);
                    
                        if($update)
                            return response()->json(['status' => 200 ,'message' => 'Record status change successfully']);
                        else
                            return response()->json(['status' => 201, 'message' => 'Faild to change status']);
                    }
                }else{
                    return response()->json(['status' => 201, 'message' => 'Somthing went wrong !']);
                }
            }
        /** change-status */


    }
