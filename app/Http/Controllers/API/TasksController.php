<?php

    namespace App\Http\Controllers\API;

    use App\Http\Controllers\Controller;
    use Illuminate\Http\Request;
    use App\Models\Task;
    use Auth, DB, Validator, File;

    class TasksController extends Controller{
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
                                ->get();

                if(isset($data) && $data->isNotEmpty()){
                    foreach($data as $row){
                        $u_data = DB::select("SELECT GROUP_CONCAT(u.name SEPARATOR ', ') as names
                                                FROM users as u
                                                WHERE u.id IN($row->user_id)
                                                GROUP BY 'All'");

                        if(!empty($u_data[0]))
                            $row->allocate_to = $u_data[0]->names;
                        else
                            $row->allocate_to = '';                 
                    
                        if($row->type != '' || $row->type != null)
                            $row->type = ucfirst(str_replace('_', ' ', $row->type));
                    }
                }

                if($data->isNotEmpty())
                    return response()->json(['status' => 200, 'message' => 'success', 'data' => $data]);
                else
                    return response()->json(['status' => 201, 'message' => 'No tasks found']);
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

                if(!empty($data)){
                    $u_data = DB::select("SELECT GROUP_CONCAT(u.name SEPARATOR ', ') as names
                                                FROM users as u
                                                WHERE u.id IN($data->user_id)
                                                GROUP BY 'All'");

                    if(!empty($u_data[0]))
                        $data->allocate_to = $u_data[0]->names;
                    else
                        $data->allocate_to = '';                 
                
                    if($data->type != '' || $data->type != null)
                        $data->type = ucfirst(str_replace('_', ' ', $data->type));
                }

                if(!empty($data))
                    return response()->json(['status' => 200, 'message' => 'success', 'data' => $data]);
                else
                    return response()->json(['status' => 201, 'message' => 'No task found']);
            }
        /** tasks */

        /** insert */
            public function insert(Request $request){
                $rules = [
                    'type' => 'required',
                    'user_id' => 'required',
                    'target_date' => 'required',
                ];

                $validator = Validator::make($request->all(), $rules);

                if($validator->fails())
                    return response()->json(['status' => 422, 'message' => $validator->errors()]);

                $crud = [
                    'type' => $request->type,
                    'user_id' => $request->user_id,
                    'party_name' => $request->party_name ?? NULL,
                    'description' => $request->description ?? NULL,
                    'target_date' => $request->target_date,
                    'created_at' => date('Y-m-d H:i:s'),
                    'created_by' => auth('sanctum')->user()->id,
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_by' => auth('sanctum')->user()->id
                ];

                if(!empty($request->file('file'))){
                    $file = $request->file('file');
                    $filenameWithExtension = $request->file('file')->getClientOriginalName();
                    $filename = pathinfo($filenameWithExtension, PATHINFO_FILENAME);
                    $extension = $request->file('file')->getClientOriginalExtension();
                    $filenameToStore = time()."_".$filename.'.'.$extension;

                    $folder_to_upload = public_path().'/uploads/task/';

                    if (!\File::exists($folder_to_upload)) {
                        \File::makeDirectory($folder_to_upload, 0777, true, true);
                    }

                    $crud["attechment"] = $filenameToStore;
                }

                $last_id = Task::insertGetId($crud);

                if($last_id){
                    if(!empty($request->file('file')))
                        $file->move($folder_to_upload, $filenameToStore);
                    return response()->json(['status' => 200, 'message' => 'Task added successfully']);
                }else{
                    return response()->json(['status' => 201, 'message' => 'Something went wrong.']);
                }
            }
        /** insert */

        /** update */
            public function update(Request $request){
                $rules = [
                    'id' => 'required',
                    'type' => 'required',
                    'user_id' => 'required',
                    'target_date' => 'required',
                ];

                $validator = Validator::make($request->all(), $rules);

                if($validator->fails())
                    return response()->json(['status' => 422, 'message' => $validator->errors()]);

                $exst_data = Task::where(['id' => $request->id])->first();

                $crud = [
                    'type' => $request->type,
                    'user_id' => $request->user_id,
                    'description' => $request->description ?? NULL,
                    'party_name' => $request->party_name ?? NULL,
                    'target_date' => $request->target_date,
                    'created_at' => date('Y-m-d H:i:s'),
                    'created_by' => auth('sanctum')->user()->id,
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_by' => auth('sanctum')->user()->id
                ];

                if(!empty($request->file('file'))){
                    $exst_file = public_path().'/uploads/task/'.$exst_data->attechment;

                    $file = $request->file('file');
                    $filenameWithExtension = $request->file('file')->getClientOriginalName();
                    $filename = pathinfo($filenameWithExtension, PATHINFO_FILENAME);
                    $extension = $request->file('file')->getClientOriginalExtension();
                    $filenameToStore = time()."_".$filename.'.'.$extension;

                    $folder_to_upload = public_path().'/uploads/task/';

                    if (!\File::exists($folder_to_upload)) {
                        \File::makeDirectory($folder_to_upload, 0777, true, true);
                    }

                    $crud["attechment"] = $filenameToStore;
                }

                $update = Task::where(['id' => $request->id])->update($crud);

                if($update){
                    if(!empty($request->file('file'))){
                        $file->move($folder_to_upload, $filenameToStore);

                        if(\File::exists($exst_file) && $exst_file != ''){
                            @unlink($exst_file);
                        }
                    }
                    return response()->json(['status' => 200, 'message' => 'Task updated successfully']);
                }else{
                    return response()->json(['status' => 201, 'message' => 'Something went wrong.']);
                }
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
