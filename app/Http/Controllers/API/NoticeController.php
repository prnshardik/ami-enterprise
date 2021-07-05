<?php    
    namespace App\Http\Controllers\API;

    use App\Http\Controllers\Controller;
    use Illuminate\Http\Request;
    use App\Models\Notice;
    use Auth, Validator, DB, Mail;

    class NoticeController extends Controller{
        /** index */
            public function notice(Request $request){
                $data = Notice::all();
                
                if($data->isNotEmpty())
                    return response()->json(['status' => 200, 'message' => 'success', 'data' => $data]);
                else
                    return response()->json(['status' => 201, 'message' => 'No records found']);
            }
        /** index */

        /** insert */
            public function insert(Request $request){
                $rules = [
                    'title' => 'required'
                ];

                $validator = Validator::make($request->all(), $rules);

                if($validator->fails())
                    return response()->json(['status' => 422, 'message' => $validator->errors()]);
                
                $crud = [
                    'title' => ucfirst($request->title),
                    'description' => $request->description ?? NULL,
                    'status' => 'active',
                    'created_at' => date('Y-m-d H:i:s'),
                    'created_by' => auth('sanctum')->user()->id,
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_by' => auth('sanctum')->user()->id
                ];

                $last_id = Notice::insertGetId($crud);
                
                if($last_id)
                    return response()->json(['status' => 200, 'message' => 'Record added successfully' ,'id' => $notice_last_id]);
                else
                    return response()->json(['status' => 201, 'message' => 'Faild to add record']);
                
            }
        /** insert */

        /** view */
            public function view(Request $request){
                $id = $request->id;
                $data = Notice::where(['id' => $id])->first();
                
                if($data)
                    return response()->json(['status' => 200, 'message' => 'Data found' ,'data' => $data]);
                else
                    return response()->json(['status' => 404, 'message' => 'No record found']);
            }
        /** view */

        /** update */
            public function update(Request $request){
                $rules = [
                    'id' => 'required',
                    'title' => 'required'
                ];

                $validator = Validator::make($request->all(), $rules);

                if($validator->fails())
                    return response()->json(['status' => 422, 'message' => $validator->errors()]);

                $crud = [
                    'title' => ucfirst($request->title),
                    'description' => $request->description ?? NULL,
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_by' => auth('sanctum')->user()->id
                ];
                    
                $update = Notice::where(['id' => $request->id])->update($crud);

                if($update)
                    return response()->json(['status' => 200, 'message' => 'Record updated successfully']);
                else
                    return response()->json(['status' => 404, 'message' => 'Faild to update record']);
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

                $data = Notice::where(['id' => $id])->first();

                if(!empty($data)){
                    if($status == 'deleted'){
                        $update = Notice::where('id',$id)->delete();
                        if($update){
                            return response()->json(['status' => 200 ,'message' => 'Record deleted successfully']);
                        }
                        else{
                            return response()->json(['status' => 201, 'message' => 'Faild to delete record']);
                        }


                    }else{
                        $update = Notice::where(['id' => $id])->update(['status' => $status, 'updated_at' => date('Y-m-d H:i:s'), 'updated_by' => auth('sanctum')->user()->id]);
                    
                        if($update){
                            return response()->json(['status' => 200 ,'message' => 'Record status change successfully']);
                        }else{
                            return response()->json(['status' => 201, 'message' => 'Faild to update status']);
                        }
                    }
                }else{
                    return response()->json(['status' => 201, 'message' => 'Somthing went wrong']);
                }
            }
        /** change-status */
    }