<?php    
    namespace App\Http\Controllers\API;

    use App\Http\Controllers\Controller;
    use Illuminate\Http\Request;
    use App\Models\User;
    use App\Http\Requests\UsersRequest;
    use Auth, Validator, DB, Mail,;

    class UsersController extends Controller{

        /** index */
            public function users(Request $request){
                $data = User::select('id', 'name', 'email', 'phone', 'status')->where(['is_admin' => 'n'])->get();
                
                if($data->isNotEmpty())
                    return response()->json(['status' => 200, 'message' => 'success', 'data' => $data]);
                else
                    return response()->json(['status' => 201, 'message' => 'No Users Found']);
            }
        /** index */

        /** insert */
            public function insert(Request $request){
                $rules = [
                    'name' => 'required',
                    'email' => 'required|email|unique:users,email',
                    'phone' => 'required|numeric|unique:users,phone',
                    'password' => 'required|min:7'
                ];

                $validator = Validator::make($request->all(), $rules);

                if($validator->fails()){
                    return response()->json(['status' => 422, 'message' => $validator->errors()]);
                }

                    $password = $request->password;
                    
                    $crud = [
                            'name' => $request->name,
                            'email' => $request->email,
                            'phone' => $request->phone,
                            'password' => bcrypt($password),
                            'status' => 'active',
                            'is_admin' => 'n',
                            'created_at' => date('Y-m-d H:i:s'),
                            'created_by' => auth('sanctum')->user()->id,
                            'updated_at' => date('Y-m-d H:i:s'),
                            'updated_by' => auth('sanctum')->user()->id
                    ];

                    $user_last_id = User::insertGetId($crud);
                    
                    if($user_last_id)
                        return response()->json(['status' => 200, 'message' => 'User Created Successfully.' ,'id' => $user_last_id]);
                    else
                        return response()->json(['status' => 201, 'message' => 'Faild To Create User !']);
                
            }
        /** insert */

        /** view */
            public function view(Request $request, $id=''){
                $id = $request->id;
                $data = User::where(['id' => $id])->first();
                
                if($data)
                    return response()->json(['status' => 200, 'message' => 'User Found' ,'data' => $data]);
                else
                    return response()->json(['status' => 404, 'message' => 'User Not Found !']);
            }
        /** view */

        /** update */
            public function update(Request $request){
                $rules = [
                    'name' => 'required',
                    'email' => 'required|email|unique:users,email,'.$request->id,
                    'phone' => 'required|numeric|unique:users,phone,'.$request->id
                ];

                $validator = Validator::make($request->all(), $rules);

                if($validator->fails()){
                    return response()->json(['status' => 422, 'message' => $validator->errors()]);
                }

                $crud = [
                        'name' => ucfirst($request->name),
                        'email' => $request->email,
                        'phone' => $request->phone ?? NULL,
                        'updated_at' => date('Y-m-d H:i:s'),
                        'updated_by' => auth('sanctum')->user()->id
                ];
                    
                if(isset($request->password) && !empty($request->password))
                    $crud['password'] = bcrypt($request->password);

                $update = User::where(['id' => $request->id])->update($crud);
                if($update)
                    return response()->json(['status' => 200, 'message' => 'User Updated Successfully.']);
                else
                    return response()->json(['status' => 404, 'message' => 'Faild To Update User !']);
                
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

                $data = User::where(['id' => $request->id])->first();

                if(!empty($data)){
                    if($request->status == 'deleted'){
                        $update = User::where('id', $request->id)->delete();
                        
                        if($update)
                            return response()->json(['status' => 200 ,'message' => 'Record deleted successfully.']);
                        else
                            return response()->json(['status' => 201, 'message' => 'Faild to delete record !']);
                    }else{
                        $update = User::where(['id' => $request->id])->update(['status' => $request->status, 'updated_at' => date('Y-m-d H:i:s'), 'updated_by' => auth('sanctum')->user()->id]);
                    
                        if($update)
                            return response()->json(['status' => 200 ,'message' => 'Status change successfully.']);
                        else
                            return response()->json(['status' => 201, 'message' => 'Faild to update status']);
                    }
                }else{
                    return response()->json(['status' => 201, 'message' => 'Somthing went wrong !']);
                }
            }
        /** change-status */
    }