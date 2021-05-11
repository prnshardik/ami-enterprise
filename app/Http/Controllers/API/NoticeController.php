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
                    return response()->json(['status' => 201, 'message' => 'No Notice Found']);
            }
        /** index */

        /** insert */
            public function insert(Request $request){
                $rules = [
                    'title' => 'required',
                    'description' => 'required'
                ];

                $validator = Validator::make($request->all(), $rules);

                if($validator->fails()){
                    return response()->json(['status' => 422, 'message' => $validator->errors()]);
                }                    
                
                $crud = [
                        'title' => ucfirst($request->title),
                        'description' => $request->description,
                        'status' => 'active',
                        'created_at' => date('Y-m-d H:i:s'),
                        'created_by' => auth('sanctum')->user()->id,
                        'updated_at' => date('Y-m-d H:i:s'),
                        'updated_by' => auth('sanctum')->user()->id
                ];

                    $notice_last_id = Notice::insertGetId($crud);
                    
                    if($notice_last_id)
                        return response()->json(['status' => 200, 'message' => 'Notice Created Successfully.' ,'id' => $notice_last_id]);
                    else
                        return response()->json(['status' => 201, 'message' => 'Faild To Create Notice !']);
                
            }
        /** insert */

        /** view */
            public function view(Request $request){
                $id = $request->id;
                $data = Notice::where(['id' => $id])->first();
                
                if($data)
                    return response()->json(['status' => 200, 'message' => 'Notice Found' ,'data' => $data]);
                else
                    return response()->json(['status' => 404, 'message' => 'Notice Not Found !']);
            }
        /** view */

        /** update */
            public function update(Request $request){
                $rules = [
                    'id' => 'required',
                    'title' => 'required',
                    'description' => 'required'
                ];

                $validator = Validator::make($request->all(), $rules);

                if($validator->fails()){
                    return response()->json(['status' => 422, 'message' => $validator->errors()]);
                }

                $crud = [
                        'title' => ucfirst($request->title),
                        'description' => $request->description,
                        'updated_at' => date('Y-m-d H:i:s'),
                        'updated_by' => auth('sanctum')->user()->id
                ];
                    
                $update = Notice::where(['id' => $request->id])->update($crud);
                if($update)
                    return response()->json(['status' => 200, 'message' => 'Notice Updated Successfully.']);
                else
                    return response()->json(['status' => 404, 'message' => 'Faild To Update Notice !']);
                
            }
        /** update */

        /** change-status */
            public function change_status(Request $request){
                     /**
                            Status = [
                                active, 
                                inactive,
                                deleted
                            ] 
                     **/
                    $rules = [
                        'id' => 'required',
                        'status' => 'required'
                    ];

                    $validator = Validator::make($request->all(), $rules);

                    if($validator->fails()){
                        return response()->json(['status' => 422, 'message' => $validator->errors()]);
                    }

                    $id = $request->id;
                    $status = $request->status;

                    $data = Notice::where(['id' => $id])->first();

                    if(!empty($data)){
                        if($status == 'deleted'){
                            $update = Notice::where('id',$id)->delete();
                            if($update){
                                return response()->json(['status' => 200 ,'message' => 'Record Deleted Successfully.']);
                            }
                            else{
                                return response()->json(['status' => 201, 'message' => 'Faild To Delete Record !']);
                            }


                        }else{
                            $update = Notice::where(['id' => $id])->update(['status' => $status, 'updated_at' => date('Y-m-d H:i:s'), 'updated_by' => auth('sanctum')->user()->id]);
                        
                            if($update){
                                return response()->json(['status' => 200 ,'message' => 'Status Change Successfully.']);
                            }else{
                                return response()->json(['status' => 201, 'message' => 'Faild To Update User Status']);
                            }
                        }
                    }else{
                        return response()->json(['status' => 201, 'message' => 'Somthing Went Wrong !']);
                    }
            }
        /** change-status */
    }