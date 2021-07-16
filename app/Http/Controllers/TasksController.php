<?php
    namespace App\Http\Controllers;

    use Illuminate\Http\Request;
    use App\Models\User;
    use App\Models\Task;
    use App\Models\Customer;
    use App\Models\Payment;
    use Illuminate\Support\Str;
    use App\Http\Requests\TaskRequest;
    use Auth, Validator, DB, Mail, DataTables, File;

    class TasksController extends Controller{
        /** index */
            public function index(Request $request){
                if($request->ajax()){
                    $data = Task::select('task.id', 'task.user_id' , 'u.name as allocate_from', 'task.type', 'task.target_date', 'task.created_at', 'task.status')
                                    ->leftjoin('users', 'task.user_id', 'users.id')
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

                    return Datatables::of($data)
                            ->addIndexColumn()
                            ->addColumn('action', function($data){
                                return ' <div class="btn-group">
                                                <a href="'.route('tasks.view', ['id' => base64_encode($data->id)]).'" class="btn btn-default btn-xs">
                                                    <i class="fa fa-eye"></i>
                                                </a> &nbsp;
                                                <a href="'.route('tasks.edit', ['id' => base64_encode($data->id)]).'" class="btn btn-default btn-xs">
                                                    <i class="fa fa-edit"></i>
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

                return view('tasks.index');
            }
        /** index */

        /** create */
            public function create(Request $request){
                $users = User::select('id', 'name')->where(['status' => 'active', 'is_admin' => 'n'])->get();
                $customers = Customer::select('id', 'party_name')->where(['status' => 'active'])->get();

                return view('tasks.create', ['users' => $users, 'customers' => $customers]);
            }
        /** create */

        /** insert */
            public function insert(TaskRequest $request){
                if($request->ajax()){ return true; }
                
                if(!empty($request->all())){
                    $crud = [
                        'type' => $request->type,
                        'user_id' => implode(',', $request->users),
                        'party_name' => $request->customer_id ?? NULL,
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

                    $last_id = Task::insertGetId($crud);
                    
                    if($last_id){
                        if(!empty($request->file('file')))
                            $file->move($folder_to_upload, $filenameToStore);

                        return redirect()->route('tasks')->with('success', 'Task created successfully.');
                    }else{
                        return redirect()->back()->with('error', 'Faild to create task!')->withInput();
                    }
                }else{
                    return redirect()->back()->with('error', 'Something went wrong')->withInput();
                }
            }
        /** insert */

        /** view */
            public function view(Request $request, $id=''){
                if($id == '')
                    return redirect()->route('tasks')->with('error', 'Something went wrong');

                $id = base64_decode($id);

                $data = Task::where(['id' => $id])->first();
                $users = User::select('id', 'name')->where(['status' => 'active', 'is_admin' => 'n'])->get();
                $customers = Customer::select('id', 'party_name')->where(['status' => 'active'])->get();
                
                if($data)
                    return view('tasks.view')->with(['users' => $users, 'customers' => $customers,'data' => $data]);
                else
                    return redirect()->route('tasks')->with('error', 'No data found');
            }
        /** view */

        /** edit */
            public function edit(Request $request, $id=''){
                if($id == '')
                    return redirect()->route('tasks')->with('error', 'Something went wrong');

                $id = base64_decode($id);

                $data = Task::where(['id' => $id])->first();
                $users = User::select('id', 'name')->where(['status' => 'active', 'is_admin' => 'n'])->get();
                $customers = Customer::select('id', 'party_name')->where(['status' => 'active'])->get();
                
                if($data)
                    return view('tasks.edit')->with(['data' => $data, 'users' => $users, 'customers' => $customers]);
                else
                    return redirect()->route('tasks')->with('error', 'No task found');
            }
        /** edit */ 

        /** update */
            public function update(TaskRequest $request){
                if($request->ajax()){ return true; }

                if(!empty($request->all())){
                    $exst_data = Task::where(['id' => $request->id])->first();

                    $crud = [
                        'type' => $request->type,
                        'user_id' => implode(',', $request->users) ,
                        'party_name' => $request->customer_id ?? NULL,
                        'description' => $request->description ?? NULL,
                        'target_date' => $request->t_date,
                        'updated_at' => date('Y-m-d H:i:s'),
                        'updated_by' => auth()->user()->id
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

                        return redirect()->route('tasks')->with('success', 'Task updated successfully.');
                    }else{
                        return redirect()->back()->with('error', 'Faild to update task!')->withInput();
                    }
                }else{
                    return redirect()->back()->with('error', 'Something went wrong')->withInput();
                }
            }
        /** update */

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

        /** customer-details */ 
            public function customer_details(Request $request){
                $type = $request->type;
                $name = $request->name;

                $data = '';
                if($type == 'payment'){
                    $collection = Payment::select('id', 'party_name', 'bill_date', 'balance_amount', 'mobile_no', DB::Raw("null as note"), DB::Raw("null as reminder"))
                                        ->whereRaw('id IN (select MAX(id) FROM payments GROUP BY party_name)')
                                        ->where(['party_name' => $name])
                                        ->first();

                    if($collection){
                        $data .= "<div class='form-group col-md-6'><span style='font-weight: bold; padding-left:16px;'>Name: </span><span> $collection->party_name</span></div>
                                    <div class='form-group col-md-6'><span style='font-weight: bold; padding-left:16px;'>Bill date: </span><span> $collection->bill_date</span></div>
                                    <div class='form-group col-md-6'><span style='font-weight: bold; padding-left:16px;'>Balance amount: </span><span> $collection->balance_amount</span></div>
                                    <div class='form-group col-md-6'><span style='font-weight: bold; padding-left:16px;'>Mobile number: </span><span> $collection->mobile_no</span></div>
                                    <div class='form-group col-md-6'><span style='font-weight: bold; padding-left:16px;'>Note: </span><span> $collection->note</span></div>
                                    <div class='form-group col-md-6'><span style='font-weight: bold; padding-left:16px;'>Reminder: </span><span> $collection->reminder</span></div>";
                    }
                }else{
                    $collection = Customer::where(['party_name' => $name])->first();

                    if($collection){
                        $data .= "<div class='form-group col-md-6'><span style='font-weight: bold; padding-left:16px;'>Name: </span><span> $collection->party_name</span></div>
                                    <div class='form-group col-md-6'><span style='font-weight: bold; padding-left:16px;'>Billing name: </span><span> $collection->billing_name</span></div>
                                    <div class='form-group col-md-6'><span style='font-weight: bold; padding-left:16px;'>Contact person: </span><span> $collection->contact_person</span></div>
                                    <div class='form-group col-md-6'><span style='font-weight: bold; padding-left:16px;'>Mobile number: </span><span> $collection->mobile_number</span></div>
                                    <div class='form-group col-md-6'><span style='font-weight: bold; padding-left:16px;'>Billing address: </span><span> $collection->billing_address</span></div>
                                    <div class='form-group col-md-6'><span style='font-weight: bold; padding-left:16px;'>Delivery address: </span><span> $collection->delivery_address</span></div>
                                    <div class='form-group col-md-6'><span style='font-weight: bold; padding-left:16px;'>Electrician: </span><span> $collection->electrician</span></div>
                                    <div class='form-group col-md-6'><span style='font-weight: bold; padding-left:16px;'>Electrician number: </span><span> $collection->electrician_number</span></div>";
                    }
                }

                return response()->json(['code' => 200, 'data' => $data]);
            }
        /** customer-details */
    }