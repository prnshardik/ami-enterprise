<?php    

    namespace App\Http\Controllers;

    use Illuminate\Http\Request;
    use App\Models\Notice;
    use Illuminate\Support\Str;
    use App\Http\Requests\NoticeRequest;
    use Auth, Validator, DB, Mail, DataTables;

    class NoticesController extends Controller{
        /** index */
            public function index(Request $request){
                if($request->ajax()){
                    $data = Notice::select('id', 'title', DB::Raw("SUBSTRING(".'description'.", 1, 30) as description"), 'status')->get();

                    return Datatables::of($data)
                            ->addIndexColumn()
                            ->addColumn('action', function($data){
                                return ' <div class="btn-group">
                                                <a href="'.route('notices.edit', ['id' => base64_encode($data->id)]).'" class="btn btn-default btn-xs">
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

                return view('notices.index');
            }
        /** index */

        /** create */
            public function create(Request $request){
                return view('notices.create');
            }
        /** create */

        /** insert */
            public function insert(NoticeRequest $request){
                if($request->ajax()){ return true; }

                if(!empty($request->all())){
                    $crud = [
                        'title' => ucfirst($request->title),
                        'description' => $request->description ?? NULL,
                        'created_at' => date('Y-m-d H:i:s'),
                        'created_by' => auth()->user()->id,
                        'updated_at' => date('Y-m-d H:i:s'),
                        'updated_by' => auth()->user()->id
                    ];

                    $last_id = Notice::insertGetId($crud);
                    
                    if($last_id)
                        return redirect()->route('notices')->with('success', 'Notice created successfully.');
                    else
                        return redirect()->back()->with('error', 'Faild to create notice!')->withInput();
                }else{
                    return redirect()->back()->with('error', 'Something went wrong')->withInput();
                }
            }
        /** insert */

        /** edit */
            public function edit(Request $request, $id=''){
                if($id == '')
                    return redirect()->route('notices')->with('error', 'Something went wrong');

                $id = base64_decode($id);

                $data = Notice::select('id', 'title', 'description')->where(['id' => $id])->first();
                
                if($data)
                    return view('notices.edit')->with('data', $data);
                else
                    return redirect()->route('notices')->with('error', 'No notice found');
            }
        /** edit */ 

        /** update */
            public function update(NoticeRequest $request){
                if($request->ajax()){ return true; }

                if(!empty($request->all())){
                    $crud = [
                        'title' => ucfirst($request->title),
                        'description' => $request->description ?? NULL,
                        'updated_at' => date('Y-m-d H:i:s'),
                        'updated_by' => auth()->user()->id
                    ];

                    $update = Notice::where(['id' => $request->id])->update($crud);

                    if($update)
                        return redirect()->route('notices')->with('success', 'Notice updated successfully.');
                    else
                        return redirect()->route('notices')->with('error', 'Faild to update notice!');
                }else{
                    return redirect()->route('notices')->with('error', 'Something went wrong');
                }
            }
        /** update */

        /** change-status */
            public function change_status(Request $request){
                if(!$request->ajax()){ exit('No direct script access allowed'); }

                if(!empty($request->all())){
                    $id = base64_decode($request->id);
                    $status = $request->status;

                    $data = Notice::where(['id' => $id])->first();

                    if(!empty($data)){
                        if($status == 'deleted')
                            $update = Notice::where('id',$id)->delete();
                        else
                            $update = Notice::where(['id' => $id])->update(['status' => $status, 'updated_at' => date('Y-m-d H:i:s'), 'updated_by' => auth()->user()->id]);
                        
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

        /** board */
            public function notices_board(){
                $data = Notice::where(['status' => 'active'])->where(['created_by' => auth()->user()->id])->get();
                
                return view('notices.board', ['data' => $data]);
            } 
        /** board */
    }