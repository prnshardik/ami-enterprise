<?php

    namespace App\Http\Controllers\admin;

    use App\Http\Controllers\Controller;
    use Illuminate\Http\Request;
    use App\Models\User;
    use App\Http\Requests\UsersRequest;
    use DataTables, DB;

    class UsersController extends Controller{

        /** index */
            public function index(Request $request){
                if($request->ajax()){
                    
                    $data = User::select('id', 'email', 'phone', 'status','name')
                                    ->where(['is_admin' => 'n'])
                                    ->get();

                    return Datatables::of($data)
                            ->addIndexColumn()
                            ->addColumn('action', function($data){
                                return ' <div class="btn-group">
                                                <a href="'.route('admin.users.view', ['id' => base64_encode($data->id)]).'" class="btn btn-default btn-xs">
                                                    <i class="fa fa-eye"></i>
                                                </a> &nbsp;

                                                <a href="'.route('admin.users.edit', ['id' => base64_encode($data->id)]).'" class="btn btn-default btn-xs">
                                                    <i class="fa fa-edit"></i>
                                                </a> &nbsp;
                                                
                                                <a href="javascript:;" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown">
                                                    <i class="fa fa-bars"></i>
                                                </a> &nbsp;
                                                <ul class="dropdown-menu">
                                                    <li><a class="dropdown-item" href="javascript:;" onclick="change_status(this);" data-status="active" data-old_status="'.$data->status.'" data-id="'.base64_encode($data->id).'">Active</a></li>
                                                    <li><a class="dropdown-item" href="javascript:;" onclick="change_status(this);" data-status="inactive" data-old_status="'.$data->status.'" data-id="'.base64_encode($data->id).'">Inactive</a></li>
                                                    <li><a class="dropdown-item" href="javascript:;" onclick="change_status(this);" data-status="deleted" data-old_status="'.$data->status.'" data-id="'.base64_encode($data->id).'">Delete</a></li>
                                                </ul>
                                            </div>';
                            })

                            ->editColumn('status', function($data) {
                                if($data->status == 'active'){
                                    return '<span class="badge badge-pill badge-success">Active</span>';
                                }else if($data->status == 'inactive'){
                                    return '<span class="badge badge-pill badge-warning">Inactive</span>';
                                }else if($data->status == 'deleted'){
                                    return '<span class="badge badge-pill badge-danger">Delete</span>';
                                }else{
                                    return '-';
                                }
                            })

                            ->rawColumns(['action', 'status'])
                            ->make(true);
                }
                return view('users.index');
            }
        /** index */

        /** create */
            public function create(Request $request){
                return view('users.create');
            }
        /** create */

        /** insert */
            public function insert(UsersRequest $request){
                if($request->ajax()){ return true; }

                if(!empty($request->all())){
                    $password = $request->password ?? 'abcd234';
                    
                    $crud = [
                            'name' => $request->name,
                            'email' => $request->email,
                            'phone' => $request->phone ?? NULL,
                            'password' => bcrypt($password),
                            'status' => 'active',
                            'is_admin' => 'n',
                            'created_at' => date('Y-m-d H:i:s'),
                            'created_by' => auth()->user()->id,
                            'updated_at' => date('Y-m-d H:i:s'),
                            'updated_by' => auth()->user()->id
                    ];

                    $user_last_id = User::insertGetId($crud);
                    
                    if($user_last_id){
                        return redirect()->route('admin.users')->with('success', 'User Created Successfully.');
                    }else{
                        return redirect()->route('admin.users')->with('error', 'Faild To Create User!');
                    }
                }else{
                    return redirect()->back('admin.users')->with('error', 'Something went wrong');
                }
            }
        /** insert */

        /** view */
            public function view(Request $request, $id=''){
                if($id == '')
                    return redirect()->route('admin.users')->with('error', 'Something went wrong Found');

                $id = base64_decode($id);

                $data = User::select('id', 'name', 'email', 'phone','password')
                        ->where(['id' => $id])
                        ->first();
                
                if($data)
                    return view('users.view')->with('data', $data);
                else
                    return redirect()->route('users')->with('error', 'No User Found');
            }
        /** view */

        /** edit */
            public function edit(Request $request, $id=''){
                if($id == '')
                    return redirect()->route('admin.users')->with('error', 'Something went wrong Found');

                $id = base64_decode($id);

                $data = User::select('id', 'name', 'email', 'status', 'phone','password')
                        ->where(['id' => $id])
                        ->first();
                
                if($data)
                    return view('users.edit')->with('data', $data);
                else
                    return redirect()->route('users')->with('error', 'No User Found');
            }
        /** edit */ 

        /** update */
            public function update(UsersRequest $request){
                if($request->ajax()){ return true; }

                if(!empty($request->all())){
                    $ext_user = User::where(['id' => $request->id])->first();

                    $crud = [
                            'name' => ucfirst($request->name),
                            'email' => $request->email,
                            'phone' => $request->phone ?? NULL,
                            'updated_at' => date('Y-m-d H:i:s'),
                            'updated_by' => auth()->user()->id
                    ];
                    if($ext_user->password != $request->password){
                        $crud['password'] = bcrypt($request->password);
                    }
                    
                    $update = User::where(['id' => $request->id])->update($crud);

                    if($update){
                        return redirect()->route('admin.users')->with('success', 'User Updated Successfully.');
                    }else{
                        return redirect()->route('admin.users')->with('error', 'Faild To Update User!');
                    }
                }else{
                    return redirect()->back('admin.users')->with('error', 'Something went wrong');
                }
            }
        /** update */

        /** change-status */
            public function change_status(Request $request){
                if(!$request->ajax()){ exit('No direct script access allowed'); }

                if(!empty($request->all())){
                    $id = base64_decode($request->id);
                    $status = $request->status;

                    $data = User::where(['id' => $id])->first();

                    if(!empty($data)){
                        if($status == 'deleted'){

                            $update = User::where('id',$id)->delete();

                        }else{

                            $update = User::where(['id' => $id])->update(['status' => $status, 'updated_at' => date('Y-m-d H:i:s'), 'updated_by' => auth()->user()->id]);

                        }
                        if($update)
                            return response()->json(['code' => 200]);
                        else
                            return response()->json(['code' => 201]);
                    }else{
                        return response()->json(['code' => 201]);
                    }
                }else{
                    return response()->json(['code' => 201]);
                }
            }
        /** change-status */
    }