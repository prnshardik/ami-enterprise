<?php    

    namespace App\Http\Controllers;

    use Illuminate\Http\Request;
    use App\Models\Customer;
    use App\Models\User;
    use Illuminate\Support\Str;
    use App\Http\Requests\CustomerRequest;
    use Auth, Validator, DB, Mail, DataTables;

    class CustomerController extends Controller{
        /** index */
            public function index(Request $request){
                if($request->ajax()){
                    $data = Customer::select('id', 'party_name', 'billing_name', 'contact_person', 'mobile_number', 'status')->get();

                    return Datatables::of($data)
                            ->addIndexColumn()
                            ->addColumn('action', function($data){
                                return ' <div class="btn-group">
                                                <a href="'.route('customers.view', ['id' => base64_encode($data->id)]).'" class="btn btn-default btn-xs">
                                                    <i class="fa fa-eye"></i>
                                                </a> &nbsp;
                                                <a href="'.route('customers.edit', ['id' => base64_encode($data->id)]).'" class="btn btn-default btn-xs">
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

                return view('customers.index');
            }
        /** index */

        /** create */
            public function create(Request $request){
                $previous = str_replace(url('/'), '', url()->previous());
                return view('customers.create', ['previous' => $previous]);
            }
        /** create */

        /** insert */
            public function insert(CustomerRequest $request){
                if($request->ajax()){ return true; }

                if(!empty($request->all())){
                    $crud = [
                        'party_name' => ucfirst($request->party_name),
                        'billing_name' => $request->billing_name ?? NULL,
                        'contact_person' => $request->contact_person ?? NULL,
                        'mobile_number' => $request->mobile_number ?? NULL,
                        'billing_address' => $request->billing_address ?? NULL,
                        'delivery_address' => $request->delivery_address ?? NULL,
                        'electrician' => $request->electrician ?? null,
                        'electrician_number' => $request->electrician_number ?? null,
                        'architect' => $request->architect ?? null,
                        'architect_number' => $request->architect_number ?? null,
                        'office_contact_person' => $request->office_contact_person ?? null,
                        'status' => 'active',
                        'created_at' => date('Y-m-d H:i:s'),
                        'created_by' => auth()->user()->id,
                        'updated_at' => date('Y-m-d H:i:s'),
                        'updated_by' => auth()->user()->id
                    ];

                    $last_id = Customer::insertGetId($crud);
                    
                    if($last_id){
                        if($request->previous == '/orders/select-customer')
                            return redirect()->route('orders.create', ['customer_id' => $last_id])->with('success', 'Record added successfully');
                        else
                            return redirect()->route('customers')->with('success', 'Record added successfully');
                    }else{
                        return redirect()->back()->with('error', 'Faild to add record')->withInput();
                    }
                }else{
                    return redirect()->back()->with('error', 'Something went wrong')->withInput();
                }
            }
        /** insert */

        /** insert-ajax */
            public function insert_ajax(CustomerRequest $request){
                if(!$request->ajax()){ return true; }

                if(!empty($request->all())){
                    $crud = [
                            'party_name' => ucfirst($request->party_name),
                            'billing_name' => $request->billing_name ?? NULL,
                            'contact_person' => $request->contact_person ?? NULL,
                            'mobile_number' => $request->mobile_number ?? NULL,
                            'billing_address' => $request->billing_address ?? NULL,
                            'delivery_address' => $request->delivery_address ?? NULL,
                            'electrician' => $request->electrician ?? null,
                            'electrician_number' => $request->electrician_number ?? null,
                            'architect' => $request->architect ?? null,
                            'architect_number' => $request->architect_number ?? null,
                            'office_contact_person' => $request->office_contact_person ?? null,
                            'status' => 'active',
                            'created_at' => date('Y-m-d H:i:s'),
                            'created_by' => auth()->user()->id,
                            'updated_at' => date('Y-m-d H:i:s'),
                            'updated_by' => auth()->user()->id
                    ];

                    $last_id = Customer::insertGetId($crud);
                    
                    if($last_id)
                        return response()->json(['code' => 200]);
                    else
                        return response()->json(['code' => 201]);
                }else{
                    return response()->json(['code' => 201]);
                }
            }
        /** insert-ajax */

        /** edit */
            public function edit(Request $request, $id=''){
                if($id == '')
                    return redirect()->route('customers')->with('error', 'Something went wrong');

                $id = base64_decode($id);

                $data = Customer::select('id', 'party_name', 'billing_name', 'contact_person', 'mobile_number', 'billing_address', 'delivery_address',
                                        'electrician', 'electrician_number', 'architect', 'architect_number', 'office_contact_person')
                                    ->where(['id' => $id])
                                    ->first();
                
                if($data)
                    return view('customers.edit')->with('data', $data);
                else
                    return redirect()->route('customers')->with('error', 'No customer found');
            }
        /** edit */ 

        /** update */
            public function update(CustomerRequest $request){
                if($request->ajax()){ return true; }

                if(!empty($request->all())){
                    $crud = [
                            'party_name' => ucfirst($request->party_name),
                            'billing_name' => $request->billing_name ?? NULL,
                            'contact_person' => $request->contact_person ?? NULL,
                            'mobile_number' => $request->mobile_number ?? NULL,
                            'billing_address' => $request->billing_address ?? NULL,
                            'delivery_address' => $request->delivery_address ?? NULL,
                            'electrician' => $request->electrician ?? null,
                            'electrician_number' => $request->electrician_number ?? null,
                            'architect' => $request->architect ?? null,
                            'architect_number' => $request->architect_number ?? null,
                            'office_contact_person' => $request->office_contact_person ?? null,
                            'updated_at' => date('Y-m-d H:i:s'),
                            'updated_by' => auth()->user()->id
                    ];

                    $update = Customer::where(['id' => $request->id])->update($crud);

                    if($update)
                        return redirect()->route('customers')->with('success', 'Customer updated successfully.');
                    else
                        return redirect()->back()->with('error', 'Faild to update customer!')->withInput();
                }else{
                    return redirect()->back()->with('error', 'Something went wrong')->withInput();
                }
            }
        /** update */

        /** view */
            public function view(Request $request, $id=''){
                if($id == '')
                    return redirect()->route('customers')->with('error', 'Something went wrong');

                $id = base64_decode($id);

                $data = Customer::select('id', 'party_name', 'billing_name', 'contact_person', 'mobile_number', 'billing_address', 'delivery_address',
                                        'electrician', 'electrician_number', 'architect', 'architect_number', 'office_contact_person')
                                    ->where(['id' => $id])
                                    ->first();
                
                if($data)
                    return view('customers.view')->with('data', $data);
                else
                    return redirect()->route('customers')->with('error', 'No customer found');
            }
        /** view */ 

        /** change-status */
            public function change_status(Request $request){
                if(!$request->ajax()){ exit('No direct script access allowed'); }

                if(!empty($request->all())){
                    $id = base64_decode($request->id);
                    $status = $request->status;

                    $data = Customer::where(['id' => $id])->first();

                    if(!empty($data)){
                        if($status == 'deleted')
                            $update = Customer::where('id',$id)->delete();
                        else
                            $update = Customer::where(['id' => $id])->update(['status' => $status, 'updated_at' => date('Y-m-d H:i:s'), 'updated_by' => auth()->user()->id]);
                        
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