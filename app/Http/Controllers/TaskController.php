<?php
    namespace App\Http\Controllers;

    use Illuminate\Http\Request;
    use App\Models\User;
    use App\Models\Task;
    use Illuminate\Support\Str;
    use App\Http\Requests\TaskRequest;
    use Auth, Validator, DB, Mail, DataTables;

    class TaskController extends Controller{

        /** index */
            public function index(Request $request){
                if($request->ajax()){
                    
                    $data = Task::
                                select('task.id','u.name AS allocate_from' ,'task.title' ,'task.target_date', 'task.created_at' ,'task.status')
                                    ->leftjoin('users AS u' ,'task.created_by' ,'u.id')
                                    ->get();

                    return Datatables::of($data)
                            ->addIndexColumn()
                            ->addColumn('action', function($data){
                                return ' <div class="btn-group">
                                                <a href="'.route('task.view', ['id' => base64_encode($data->id)]).'" class="btn btn-default btn-xs">
                                                    <i class="fa fa-eye"></i>
                                                </a> &nbsp;

                                                <a href="'.route('task.edit', ['id' => base64_encode($data->id)]).'" class="btn btn-default btn-xs">
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
                            ->editColumn('task_date',function($data){
                                return date('d-m-Y' ,strtotime($data->created_at));
                            })
                            ->editColumn('target_date',function($data){
                                return date('d-m-Y' ,strtotime($data->target_date));
                            })
                            
                            ->editColumn('status', function($data) {
                                if($data->status == 'complate'){
                                    return '<span class="badge badge-pill badge-success">Complated</span>';
                                }else if($data->status == 'pending'){
                                    return '<span class="badge badge-pill badge-warning">Pending</span>';
                                }else if($data->status == 'deleted'){
                                    return '<span class="badge badge-pill badge-danger">Deleted</span>';
                                }else{
                                    return '-';
                                }
                            })

                            ->rawColumns(['action', 'status' ,'task_date' ,'target_date'])
                            ->make(true);
                }
                return view('task.index');
            }
        /** index */

        /** create */
            public function create(Request $request){
                $users = User::select('id','name')->where(['status' => 'active' ,'is_admin' => 'n'])->get();
                return view('task.create')->with('data',$users);
            }
        /** create */

        /** insert */
            public function insert(TaskRequest $request){
                if($request->ajax()){ return true; }
                
                if(!empty($request->all())){
                    $password = $request->password;
                    $crud = [
                            'title' => $request->title,
                            'user_id' => implode(', ', $request->users) ,
                            'description' => $request->description ?? NULL,
                            'target_date' => $request->t_date,
                            'created_at' => date('Y-m-d H:i:s'),
                            'created_by' => auth()->user()->id,
                            'updated_at' => date('Y-m-d H:i:s'),
                            'updated_by' => auth()->user()->id
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

                    $user_last_id = Task::insertGetId($crud);
                    
                    if($user_last_id){
                         if(!empty($request->file('file')))
                            $file->move($folder_to_upload, $filenameToStore);

                        return redirect()->route('task')->with('success', 'Task Created Successfully.');
                    }else{
                        return redirect()->route('task')->with('error', 'Faild To Create Task!');
                    }
                }else{
                    return redirect()->back('task')->with('error', 'Something went wrong');
                }
            }
        /** insert */

        /** view */
            public function view(Request $request, $id=''){
                if($id == '')
                    return redirect()->route('users')->with('error', 'Something went wrong Found');

                $id = base64_decode($id);

                $data = Task::where('id',$id)->first();
                $users = User::select('id', 'name')
                        ->where(['status' => 'active' ,'is_admin' => 'n'])
                        ->get();
                
                if($data)
                    return view('task.view')->with(['users' => $users , 'data' => $data]);
                else
                    return redirect()->route('task')->with('error', 'No Task Found');
            }
        /** view */

        /** edit */
            public function edit(Request $request, $id=''){
                if($id == '')
                    return redirect()->route('users')->with('error', 'Something went wrong Found');

                $id = base64_decode($id);

                $data = Task::where('id',$id)->first();
                $users = User::select('id', 'name')
                        ->where(['status' => 'active' ,'is_admin' => 'n'])
                        ->get();
                
                if($data)
                    return view('task.edit')->with(['data' => $data ,'users' => $users]);
                else
                    return redirect()->route('task')->with('error', 'No Task Found');
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
                    if(isset($request->password) && !empty($request->password)){
                        $crud['password'] = bcrypt($request->password);
                    }
                    
                    $update = User::where(['id' => $request->id])->update($crud);

                    if($update){
                        return redirect()->route('users')->with('success', 'User Updated Successfully.');
                    }else{
                        return redirect()->route('users')->with('error', 'Faild To Update User!');
                    }
                }else{
                    return redirect()->back('users')->with('error', 'Something went wrong');
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