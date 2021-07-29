<?php    

    namespace App\Http\Controllers\API;

    use App\Http\Controllers\Controller;
    use Illuminate\Http\Request;
    use App\Models\Reminder;
    use Illuminate\Support\Str;
    use App\Http\Requests\ReminderRequest;
    use Auth, Validator, DB, Mail, DataTables;

    class ReminderController extends Controller{
        /** index */
            public function index(Request $request){
                
                $data = Reminder::select('id', 'title', 'date_time','note', 'status')->where(['created_by' => auth()->user()->id])->get();

                if($data->isNotEmpty())
                    return response()->json(['status' => 200, 'message' => 'Data found', 'data' => $data]);
                else
                    return response()->json(['status' => 201, 'message' => 'No records found']);
            }
        /** index */


        /** insert */
            public function insert(ReminderRequest $request){
               $rules = [
                    'title' => 'required',
                    'date_time' => 'required',
                    'note' => 'required',
                ];

                $validator = Validator::make($request->all(), $rules);

                if($validator->fails())
                    return response()->json(['status' => 422, 'message' => $validator->errors()]);

                
                $crud = [
                    'title' => ucfirst($request->title),
                    'date_time' => date('Y-m-d H:i:s', strtotime($request->date_time)) ?? NULL,
                    'note' => $request->note ?? NULL,
                    'status' => 'active',
                    'created_at' => date('Y-m-d H:i:s'),
                    'created_by' => auth('sanctum')->user()->id,
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_by' => auth('sanctum')->user()->id
                ];

                $last_id = Reminder::insertGetId($crud);
                
                if($last_id)
                    return response()->json(['code' => 200 , 'message' => 'Record added successfully']);
                else
                    return response()->json(['code' => 201 , 'message' => 'Faild To Add Record']);
            }
        /** insert */

        /** View */
            public function view(Request $request){
                 $rules = [
                    'id' => 'required'
                ];

                $validator = Validator::make($request->all(), $rules);

                if($validator->fails())
                    return response()->json(['status' => 422, 'message' => $validator->errors()]);

                $id = $request->id;

                $data = Reminder::select('id', 'title', 'date_time', 'note')->where(['id' => $id])->first();
                
                if($data)
                    return response()->json(['code' => 200 , 'message' => 'Record found' ,'data' => $data]);
                else
                    return response()->json(['code' => 201 , 'message' => 'No record found']);
            }
        /** View */ 


      

        /** update */
            public function update(ReminderRequest $request){
                $rules = [
                    'id' => 'required',
                    'title' => 'required',
                    'date_time' => 'required',
                    'note' => 'required',
                ];

                $validator = Validator::make($request->all(), $rules);

                if($validator->fails())
                    return response()->json(['status' => 422, 'message' => $validator->errors()]);

              
                    $crud = [
                        'title' => ucfirst($request->title),
                        'date_time' => date('Y-m-d H:i:s', strtotime($request->date_time)) ?? NULL,
                        'note' => $request->note ?? NULL,
                        'updated_at' => date('Y-m-d H:i:s'),
                        'updated_by' => auth()->user()->id
                    ];

                    $update = Reminder::where(['id' => $request->id])->update($crud);

                    if($update)
                        return response()->json(['code' => 200 ,'message' => 'Record updated successfully']);
                    else
                        return response()->json(['code' => 201 ,'message' => 'Faild to update Record']);
               
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

                    $id = $request->id;
                    $status = $request->status;

                    $data = Reminder::where(['id' => $id])->first();

                    if(!empty($data)){
                        if($status == 'deleted')
                            $update = Reminder::where(['id' => $id])->delete();
                        else
                            $update = Reminder::where(['id' => $id])->update(['status' => $status, 'updated_at' => date('Y-m-d H:i:s'), 'updated_by' => auth()->user()->id]);
                        
                        if($update)
                            return response()->json(['code' => 200 , 'message' => 'Record status change successfully']);
                        else
                            return response()->json(['code' => 201, 'message' => 'Faild to change record status']);
                    }else{
                        return response()->json(['code' => 201, 'message' => 'Faild to change record status']);
                    }
                
            }
        /** change-status */

       
    }