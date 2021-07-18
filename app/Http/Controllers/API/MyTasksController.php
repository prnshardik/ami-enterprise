<?php

    namespace App\Http\Controllers\API;

    use App\Http\Controllers\Controller;
    use Illuminate\Http\Request;
    use App\Models\Task;
    use Auth, DB, Validator, File;

    class MyTasksController extends Controller{
        /** tasks */
            public function tasks(Request $request){
                $path = URL('/uploads/task').'/';
                $data = Task::select('task.id', 'task.user_id', 'u.name as allocate_from', 'task.type', 'task.target_date', 'task.created_at', 'task.status',
                                        DB::Raw("CASE
                                            WHEN ".'attechment'." != '' THEN CONCAT("."'".$path."'".", ".'attechment'.")
                                            ELSE 'null'
                                        END as attechment"), 
                                        'task.description',
                                        'customers.id as customer_id', 'customers.party_name', 'customers.billing_name', 'customers.contact_person', 'customers.mobile_number',
                                        'customers.billing_address', 'customers.delivery_address', 'customers.electrician', 'customers.electrician_number', 'customers.office_contact_person'
                                    )
                                ->leftjoin('users', 'task.user_id', 'users.id')
                                ->leftjoin('customers', 'customers.party_name', 'task.party_name')
                                ->leftjoin('users as u', 'task.created_by', 'u.id')
                                ->whereRaw("find_in_set(".auth('sanctum')->user()->id.", task.user_id)")
                                ->get();

                if(isset($data) && $data->isNotEmpty()){
                    foreach($data as $data){
                        if($row->type != '' || $row->type != null)
                            $row->type = ucfirst(str_replace('_', ' ', $row->type));
                    }
                }                                

                if($data->isNotEmpty())
                    return response()->json(['status' => 200, 'message' => 'Data found', 'data' => $data]);
                else
                    return response()->json(['status' => 201, 'message' => 'No records found']);
            }
        /** tasks */

        /** task */
            public function task(Request $request, $id){
                $path = URL('/uploads/task').'/';
                $data = Task::select('task.id', 'task.user_id', 'u.name as allocate_from', 'task.type', 'task.target_date', 'task.created_at', 'task.status',
                                        DB::Raw("CASE
                                            WHEN ".'attechment'." != '' THEN CONCAT("."'".$path."'".", ".'attechment'.")
                                            ELSE 'null'
                                        END as attechment"), 
                                        'task.description',
                                        'customers.id as customer_id', 'customers.party_name', 'customers.billing_name', 'customers.contact_person', 'customers.mobile_number',
                                        'customers.billing_address', 'customers.delivery_address', 'customers.electrician', 'customers.electrician_number', 'customers.office_contact_person'
                                    )
                                ->leftjoin('users', 'task.user_id', 'users.id')
                                ->leftjoin('customers', 'customers.party_name', 'task.party_name')
                                ->leftjoin('users as u', 'task.created_by', 'u.id')
                                ->where(['task.id' => $id])
                                ->first();

                if(isset($data) && !empty($data)){
                    if($data->type != '' || $data->type != null)
                        $data->type = ucfirst(str_replace('_', ' ', $data->type));
                }  

                if(!empty($data))
                    return response()->json(['status' => 200, 'message' => 'Data found', 'data' => $data]);
                else
                    return response()->json(['status' => 201, 'message' => 'No records found']);
            }
        /** tasks */

        /** change-status */
            public function change_status(Request $request){
                $rules = [
                    'id' => 'required',
                    'status' => 'required'
                ];

                $validator = Validator::make($request->all(), $rules);

                if($validator->fails())
                    return response()->json(['status' => 422, 'message' => $validator->errors()]);

                $data = Task::where(['id' => $request->id])->first();

                if(!empty($data)){
                    if($request->status == 'deleted')
                        $update = Task::where(['id' => $request->id])->delete();
                    else
                        $update = Task::where(['id' => $request->id])->update(['status' => $request->status, 'updated_at' => date('Y-m-d H:i:s'), 'updated_by' => auth('sanctum')->user()->id]);
                    
                    if($update){
                        if($request->status == 'deleted'){
                            $exst_file = public_path().'/uploads/task/'.$data->attechment;

                            if(\File::exists($exst_file) && $exst_file != ''){
                                @unlink($exst_file);
                            }
                        }

                        return response()->json(['status' => 200, 'message' => 'Status changed successfully']);
                    }else{
                        return response()->json(['status' => 201, 'message' => 'Something went wrong']);
                    }
                }else{
                    return response()->json(['status' => 201, 'message' => 'Something went wrong']);
                }
            }
        /** change-status */
    }
