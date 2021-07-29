<?php    

    namespace App\Http\Controllers;

    use Illuminate\Http\Request;
    use App\Models\Reminder;
    use Illuminate\Support\Str;
    use App\Http\Requests\ReminderRequest;
    use Auth, Validator, DB, Mail, DataTables;

    class ReminderController extends Controller{
        /** index */
            public function index(Request $request){
                if($request->ajax()){
                    $data = Reminder::select('id', 'title', 'date_time', DB::Raw("SUBSTRING(".'note'.", 1, 30) as note"), 'status')->where(['created_by' => auth()->user()->id])->get();

                    return Datatables::of($data)
                            ->addIndexColumn()
                            ->addColumn('action', function($data){
                                return ' <div class="btn-group">
                                                <a href="'.route('reminders.view', ['id' => base64_encode($data->id)]).'" class="btn btn-default btn-xs">
                                                    <i class="fa fa-eye"></i>
                                                </a> &nbsp;
                                                <a href="'.route('reminders.edit', ['id' => base64_encode($data->id)]).'" class="btn btn-default btn-xs">
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
                                if($data->status == 'active')
                                    return '<span class="badge badge-pill badge-success">Active</span>';
                                else if($data->status == 'inactive')
                                    return '<span class="badge badge-pill badge-warning">Inactive</span>';
                                else if($data->status == 'deleted')
                                    return '<span class="badge badge-pill badge-danger">Delete</span>';
                                else
                                    return '-';
                            })

                            ->rawColumns(['action', 'status'])
                            ->make(true);
                }

                return view('reminder.index');
            }
        /** index */

        /** create */
            public function create(Request $request){
                return view('reminder.create');
            }
        /** create */

        /** insert */
            public function insert(ReminderRequest $request){
                if($request->ajax()){ return true; }

                if(!empty($request->all())){
                    $crud = [
                        'title' => ucfirst($request->title),
                        'date_time' => date('Y-m-d H:i:s', strtotime($request->date_time)) ?? NULL,
                        'note' => $request->note ?? NULL,
                        'status' => 'active',
                        'created_at' => date('Y-m-d H:i:s'),
                        'created_by' => auth()->user()->id,
                        'updated_at' => date('Y-m-d H:i:s'),
                        'updated_by' => auth()->user()->id
                    ];

                    $last_id = Reminder::insertGetId($crud);
                    
                    if($last_id)
                        return redirect()->route('reminders')->with('success', 'Record added successfully');
                    else
                        return redirect()->back()->with('error', 'Faild to add record')->withInput();
                }else{
                    return redirect()->back()->with('error', 'Something went wrong')->withInput();
                }
            }
        /** insert */

        /** View */
            public function view(Request $request, $id=''){
                if($id == '')
                    return redirect()->route('reminders')->with('error', 'Something went wrong');

                $id = base64_decode($id);

                $data = Reminder::select('id', 'title', 'date_time', 'note')->where(['id' => $id])->first();
                
                if($data)
                    return view('reminder.view', ['data' => $data]);
                else
                    return redirect()->route('reminders')->with('error', 'No data found');
            }
        /** View */ 


         /** edit */
            public function edit(Request $request, $id=''){
                if($id == '')
                    return redirect()->route('reminders')->with('error', 'Something went wrong');

                $id = base64_decode($id);

                $data = Reminder::select('id', 'title', 'date_time', 'note')->where(['id' => $id])->first();
                
                if($data)
                    return view('reminder.edit', ['data' => $data]);
                else
                    return redirect()->route('reminders')->with('error', 'No data found');
            }
        /** edit */ 

        /** update */
            public function update(ReminderRequest $request){
                if($request->ajax()){ return true; }

                if(!empty($request->all())){
                    $crud = [
                        'title' => ucfirst($request->title),
                        'date_time' => date('Y-m-d H:i:s', strtotime($request->date_time)) ?? NULL,
                        'note' => $request->note ?? NULL,
                        'updated_at' => date('Y-m-d H:i:s'),
                        'updated_by' => auth()->user()->id
                    ];

                    $update = Reminder::where(['id' => $request->id])->update($crud);

                    if($update)
                        return redirect()->route('reminders')->with('success', 'Record updated successfully');
                    else
                        return redirect()->route('reminders')->with('error', 'Faild to update record');
                }else{
                    return redirect()->route('reminders')->with('error', 'Something went wrong');
                }
            }
        /** update */

        /** change-status */
            public function change_status(Request $request){
                if(!$request->ajax()){ exit('No direct script access allowed'); }

                if(!empty($request->all())){
                    $id = base64_decode($request->id);
                    $status = $request->status;

                    $data = Reminder::where(['id' => $id])->first();

                    if(!empty($data)){
                        if($status == 'deleted')
                            $update = Reminder::where(['id' => $id])->delete();
                        else
                            $update = Reminder::where(['id' => $id])->update(['status' => $status, 'updated_at' => date('Y-m-d H:i:s'), 'updated_by' => auth()->user()->id]);
                        
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