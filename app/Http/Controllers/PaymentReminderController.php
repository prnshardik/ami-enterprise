<?php
    namespace App\Http\Controllers;

    use Illuminate\Http\Request;
    use App\Models\PaymentReminder;
    use App\Models\User;
    use App\Models\Payment;
    use Illuminate\Support\Str;
    use App\Http\Requests\PaymentReminderRequest;
    use Auth, Validator, DB, Mail, DataTables, File;

    class PaymentReminderController extends Controller{

        /** index */
            public function index(Request $request){
                if($request->ajax()){
                    $data = PaymentReminder::select('payment_reminder.id', 'payment_reminder.party_name' , 'payment_reminder.mobile_no', 'payment_reminder.date', 
                                                        'payment_reminder.amount', 'payment_reminder.next_date', 'payment_reminder.next_time', 'u.name as user_name'
                                                    )
                                                ->leftjoin('users as u', 'payment_reminder.user_id', 'u.id')
                                                ->get();

                    return Datatables::of($data)
                            ->addIndexColumn()
                            ->addColumn('action', function($data){
                                return ' <div class="btn-group">
                                                <a href="'.route('payments.reminders.view', ['id' => base64_encode($data->id)]).'" class="btn btn-default btn-xs">
                                                    <i class="fa fa-eye"></i>
                                                </a> &nbsp;
                                                <a href="'.route('payments.reminders.edit', ['id' => base64_encode($data->id)]).'" class="btn btn-default btn-xs">
                                                    <i class="fa fa-edit"></i>
                                                </a> &nbsp;
                                                <a class="dropdown-item" href="javascript:;" onclick="change_status(this);" data-status="deleted" data-id="'.base64_encode($data->id).'">Delete</a>
                                            </div>';
                            })

                            ->editColumn('next_date',function($data){
                                return date('d-m-Y' ,strtotime($data->next_date));
                            })
                            
                            ->editColumn('date',function($data){
                                return date('d-m-Y' ,strtotime($data->date));
                            })
                            
                            ->rawColumns(['action', 'next_date', 'date'])
                            ->make(true);
                }

                return view('payment_reminder.index');
            }
        /** index */

        /** create */
            public function create(Request $request){
                $users = User::select('id', 'name')->where(['status' => 'active', 'is_admin' => 'n'])->get();

                return view('payment_reminder.create')->with('data', $users);
            }
        /** create */

        /** insert */
            public function insert(TaskRequest $request){
                if($request->ajax()){ return true; }
                
                if(!empty($request->all())){
                    $crud = [
                            'title' => ucfirst($request->title),
                            'user_id' => implode(',', $request->users) ,
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
                
                if($data)
                    return view('tasks.view')->with(['users' => $users, 'data' => $data]);
                else
                    return redirect()->route('tasks')->with('error', 'No task found');
            }
        /** view */

        /** edit */
            public function edit(Request $request, $id=''){
                if($id == '')
                    return redirect()->route('tasks')->with('error', 'Something went wrong');

                $id = base64_decode($id);

                $data = Task::where(['id' => $id])->first();
                $users = User::select('id', 'name')->where(['status' => 'active', 'is_admin' => 'n'])->get();
                
                if($data)
                    return view('tasks.edit')->with(['data' => $data, 'users' => $users]);
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
                            'title' => ucfirst($request->title),
                            'user_id' => implode(',', $request->users) ,
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
    }