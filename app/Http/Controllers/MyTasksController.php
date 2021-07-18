<?php
    namespace App\Http\Controllers;

    use Illuminate\Http\Request;
    use App\Models\User;
    use App\Models\Task;
    use App\Models\Customer;
    use Illuminate\Support\Str;
    use App\Http\Requests\MyTaskRequest;
    use Auth, Validator, DB, Mail, DataTables, File;

    class MyTasksController extends Controller{
        /** index */
            public function index(Request $request){
                if($request->ajax()){
                    $data = Task::select('task.id', 'task.user_id' , 'u.name as allocate_from', 'task.type', 'task.target_date', 'task.created_at', 'task.status')
                                    ->leftjoin('users as u', 'task.created_by', 'u.id')
                                    ->whereRaw("find_in_set(".auth()->user()->id.", task.user_id)")
                                    ->get();

                    if(isset($data) && $data->isNotEmpty()){
                        foreach($data as $row){
                            if($row->type != '' || $row->type != null)
                                $row->type = ucfirst(str_replace('_', ' ', $row->type));
                        }
                    }

                    return Datatables::of($data)
                            ->addIndexColumn()
                            ->addColumn('action', function($data){
                                return ' <div class="btn-group">
                                                <a href="'.route('mytasks.view', ['id' => base64_encode($data->id)]).'" class="btn btn-default btn-xs">
                                                    <i class="fa fa-eye"></i>
                                                </a> &nbsp;
                                                <a href="javascript:;" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown">
                                                    <i class="fa fa-bars"></i>
                                                </a> &nbsp;
                                                <ul class="dropdown-menu">
                                                    <li><a class="dropdown-item" href="javascript:;" onclick="change_status(this);" data-status="pending" data-old_status="'.$data->status.'" data-id="'.base64_encode($data->id).'">Pending</a></li>
                                                    <li><a class="dropdown-item" href="javascript:;" onclick="change_status(this);" data-status="complated" data-old_status="'.$data->status.'" data-id="'.base64_encode($data->id).'">Complated</a></li>
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
                                if($data->status == 'complated'){
                                    return '<span class="badge badge-pill badge-success">Complated</span>';
                                }else if($data->status == 'pending'){
                                    return '<span class="badge badge-pill badge-warning">Pending</span>';
                                }else if($data->status == 'deleted'){
                                    return '<span class="badge badge-pill badge-danger">Deleted</span>';
                                }else{
                                    return '-';
                                }
                            })

                            ->rawColumns(['action', 'status', 'task_date', 'target_date'])
                            ->make(true);
                }

                return view('mytasks.index');
            }
        /** index */

        /** view */
            public function view(Request $request, $id=''){
                if($id == '')
                    return redirect()->route('tasks')->with('error', 'Something went wrong');

                $id = base64_decode($id);

                $data = Task::where(['id' => $id])->first();
                $users = User::select('id', 'name')->where(['status' => 'active', 'is_admin' => 'n'])->get();
                $customers = Customer::select('id', 'party_name')->where(['status' => 'active'])->get();
                
                if($data)
                    return view('mytasks.view')->with(['users' => $users, 'customers' => $customers,'data' => $data]);
                else
                    return redirect()->route('tasks')->with('error', 'No data found');
            }
        /** view */

        /** change-status */
            public function change_status(Request $request){
                if(!$request->ajax()){ exit('No direct script access allowed'); }

                if(!empty($request->all())){
                    $id = base64_decode($request->id);
                    $status = $request->status;

                    $data = Task::where(['id' => $id])->first();

                    if(!empty($data)){
                        if($status == 'deleted')
                            $update = Task::where(['id' => $id])->delete();
                        else
                            $update = Task::where(['id' => $id])->update(['status' => $status, 'updated_at' => date('Y-m-d H:i:s'), 'updated_by' => auth()->user()->id]);
                        
                        if($update){
                            if($status == 'deleted'){
                                $exst_file = public_path().'/uploads/task/'.$data->attechment;

                                if(\File::exists($exst_file) && $exst_file != ''){
                                    @unlink($exst_file);
                                }
                            }
                            return response()->json(['code' => 200]);
                        }else{
                            return response()->json(['code' => 201]);
                        }
                    }else{
                        return response()->json(['code' => 201]);
                    }
                }else{
                    return response()->json(['code' => 201]);
                }
            }
        /** change-status */
    }