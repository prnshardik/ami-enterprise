<?php    

    namespace App\Http\Controllers;

    use Illuminate\Http\Request;
    use App\Models\Order;
    use App\Models\OrderDetails;
    use App\Models\Product;
    use App\Models\Customer;
    use Illuminate\Support\Str;
    use App\Http\Requests\OrderRequest;
    use Auth, Validator, DB, Mail, DataTables, File;

    class OrdersController extends Controller{
        /** index */
            public function index(Request $request){
                if($request->ajax()){
                    $data = Order::select('id', 'name', 'order_date', 'status')->orderByRaw("FIELD(status, 'delivery', 'pending', 'completed')")->get();

                    return Datatables::of($data)
                            ->addIndexColumn()
                            ->addColumn('action', function($data){
                                $return = '<div class="btn-group">
                                                <a href="'.route('orders.view', ['id' => base64_encode($data->id)]).'" class="btn btn-default btn-xs">
                                                    <i class="fa fa-eye"></i>
                                                </a> &nbsp;';
                                if($data->status != 'delivered'){
                                    $return .= '<a href="javascript:;" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown">
                                                                    <i class="fa fa-bars"></i>
                                                                </a> &nbsp;
                                                                <ul class="dropdown-menu">
                                                                    <li><a class="dropdown-item" href="javascript:;" onclick="change_status(this);" data-status="pending" data-old_status="'.$data->status.'" data-id="'.base64_encode($data->id).'">Pending</a></li>
                                                                    <li><a class="dropdown-item" href="javascript:;" onclick="change_status(this);" data-status="delivery" data-old_status="'.$data->status.'" data-id="'.base64_encode($data->id).'">Delivery</a></li>
                                                                    <li><a class="dropdown-item" href="javascript:;" onclick="change_status(this);" data-status="completed" data-old_status="'.$data->status.'" data-id="'.base64_encode($data->id).'">Completed</a></li>
                                                                    <li><a class="dropdown-item" href="javascript:;" onclick="change_status(this);" data-status="delete" data-old_status="'.$data->status.'" data-id="'.base64_encode($data->id).'">Delete</a></li>
                                                                </ul>
                                                            </div>';
                                }
                                return $return;
                            })

                            ->editColumn('status', function($data) {
                                if($data->status == 'pending')
                                    return '<span class="badge badge-pill badge-info">Pending</span>';
                                else if($data->status == 'completed')
                                    return '<span class="badge badge-pill badge-success">Completed</span>';
                                else if($data->status == 'delivery')
                                    return '<span class="badge badge-pill badge-warning">Out For Delivery</span>';
                                else
                                    return '-';
                            })

                            ->editColumn('name', function($data) {
                                return '<a href="'.route('orders.view', ['id' => base64_encode($data->id)]).'" class="text-dark">'.$data->name.'</a>';
                            })

                            ->editColumn('order_date', function($data) {
                                return '<a href="'.route('orders.view', ['id' => base64_encode($data->id)]).'" class="text-dark">'.$data->order_date.'</a>';
                            })

                            ->editColumn('status', function($data) {
                                if($data->status == 'pending')
                                    return '<a href="'.route('orders.view', ['id' => base64_encode($data->id)]).'"><span class="badge badge-pill badge-info">Pending</span></a>';
                                else if($data->status == 'completed')
                                    return '<a href="'.route('orders.view', ['id' => base64_encode($data->id)]).'"><span class="badge badge-pill badge-success">Completed</span></a>';
                                else if($data->status == 'delivery')
                                    return '<a href="'.route('orders.view', ['id' => base64_encode($data->id)]).'"><span class="badge badge-pill badge-warning">Out For Delivery</span></a>';
                                else
                                    return '-';
                            })

                            ->rawColumns(['action', 'status', 'name', 'order_date'])
                            ->make(true);
                }

                return view('orders.index');
            }
        /** index */

        /** select-customer */
            public function select_customer(Request $request){
                return view('orders.select_customer');
            }
        /** select-customer */

        /** customer-details */
            public function customer_details(Request $request){
                if(isset($request->name) && $request->name != null && $request->name != ''){
                    $data = Customer::where(['party_name' => $request->name])->first();

                    if($data)
                        return response()->json(['code' => 200, 'data' => $data]);
                    else
                        return response()->json(['code' => 201]);
                }else{
                    return response()->json(['code' => 201]);
                }
            }
        /** customer-details */

        /** product-price */
            public function product_price(Request $request){
                if(isset($request->id) && $request->id != null && $request->id != ''){
                    $data = Product::select('price')->where(['id' => $request->id])->first();

                    if($data)
                        return response()->json(['code' => 200, 'data' => $data]);
                    else
                        return response()->json(['code' => 201]);
                }else{
                    return response()->json(['code' => 201]);
                }
            }
        /** product-price */

        /** create */
            public function create(Request $request, $customer_id=''){
                $products = Product::select('id', 'name')->get();
                $customers = Customer::select('id', 'party_name', 'billing_name', 'contact_person', 'mobile_number', 'billing_address', 'delivery_address', 'office_contact_person')
                                        ->where(['status' => 'active'])
                                        ->get();
                                        
                return view('orders.create', ['products' => $products, 'customers' => $customers, 'customer_id' => $customer_id]);
            }
        /** create */

        /** insert */
            public function insert(OrderRequest $request){
                if($request->ajax()){ return true; }
                if(!empty($request->all())){
                    $crud = [
                        'name' => $request->name,
                        'order_date' => Date('Y-m-d', strtotime($request->order_date)) ?? NULL,
                        'status' => 'pending',
                        'remark' => $request->remark ?? NULL,
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

                        $folder_to_upload = public_path().'/uploads/orders/';

                        if (!File::exists($folder_to_upload))
                            File::makeDirectory($folder_to_upload, 0777, true, true);

                        $crud["file"] = $filenameToStore;
                    }

                    DB::beginTransaction();
                    try {
                        $last_id = Order::insertGetId($crud);
                        
                        if($last_id){
                            $product_id = $request->product_id ?? NULL;
                            $quantity = $request->quantity ?? NULL;
                            $price = $request->price ?? NULL;

                            if($product_id != null){
                                for($i=0; $i<count($product_id); $i++){
                                    if($product_id[$i] != null){
                                        $order_detail_crud = [
                                            'order_id' => $last_id,
                                            'product_id' => $product_id[$i] ?? NULL,
                                            'quantity' => $quantity[$i] ?? NULL,
                                            'price' => $price[$i] ?? NULL,
                                            'created_at' => date('Y-m-d H:i:s'),
                                            'created_by' => auth()->user()->id,
                                            'updated_at' => date('Y-m-d H:i:s'),
                                            'updated_by' => auth()->user()->id
                                        ];
                                     
                                        OrderDetails::insertGetId($order_detail_crud);
                                    }
                                }
                            }

                            if(!empty($request->file('file')))
                                $file->move($folder_to_upload, $filenameToStore);

                            DB::commit();
                            return redirect()->route('orders')->with('success', 'Order created successfully.');
                        }else{
                            DB::rollback();
                            return redirect()->back()->with('error', 'Faild to create order!')->withInput();
                        }
                    } catch (\Exception $e) {
                        DB::rollback();
                        return redirect()->back()->with('error', 'Faild to create order!')->withInput();
                    }
                }else{
                    return redirect()->back()->with('error', 'Something went wrong')->withInput();
                }
            }
        /** insert */

        /** view */
            public function view(Request $request, $id=''){
                if($id == '')
                    return redirect()->route('orders')->with('error', 'Something went wrong');

                $id = base64_decode($id);

                $products = Product::select('id', 'name')->get();
                $customers = Customer::select('id', 'party_name', 'billing_name', 'contact_person', 'mobile_number', 'billing_address', 'delivery_address', 'office_contact_person')
                                        ->where(['status' => 'active'])
                                        ->get();

                $data = Order::select('id', 'name', 'file', 'remark', 'order_date')->where(['id' => $id])->first();
                
                if($data){
                    $order_details = DB::table('orders_details as od')
                                        ->select('od.id', 'od.product_id', 'od.quantity', 'od.price', 'p.name as product_name')
                                        ->leftjoin('products as p', 'p.id', 'od.product_id')
                                        ->where(['od.order_id' => $data->id])
                                        ->get();

                    if($order_details->isNotEmpty())
                        $data->order_details = $order_details;
                    else
                        $data->order_details = collect();
                    
                       return view('orders.view', ['products' => $products, 'data' => $data, 'customers' => $customers]);
                }else{
                    return redirect()->route('orders')->with('error', 'No data found');
                }
            }
        /** view */ 

        /** edit */
            public function edit(Request $request, $id=''){
                if($id == '')
                    return redirect()->route('orders')->with('error', 'Something went wrong');

                $id = base64_decode($id);

                $products = Product::select('id', 'name')->get();
                $customers = Customer::select('id', 'party_name', 'billing_name', 'contact_person', 'mobile_number', 'billing_address', 'delivery_address', 'office_contact_person')
                                        ->where(['status' => 'active'])
                                        ->get();

                $data = Order::select('id', 'name', 'file', 'remark', 'order_date')->where(['id' => $id])->first();
                
                if($data){
                    $order_details = DB::table('orders_details as od')
                                        ->select('od.id', 'od.product_id', 'od.quantity', 'od.price', 'p.name as product_name')
                                        ->leftjoin('products as p', 'p.id', 'od.product_id')
                                        ->where(['od.order_id' => $data->id])
                                        ->get();

                    if($order_details->isNotEmpty())
                        $data->order_details = $order_details;
                    else
                        $data->order_details = collect();

                    return view('orders.edit', ['products' => $products, 'data' => $data, 'customers' => $customers]);
                }else{
                    return redirect()->route('orders')->with('error', 'No data found');
                }
            }
        /** edit */ 

        /** update */
            public function update(OrderRequest $request){
                if($request->ajax()){ return true; }

                if(!empty($request->all())){
                    $crud = [
                        'name' => $request->name,
                        'order_date' => Date('Y-m-d', strtotime($request->order_date)) ?? NULL,
                        'remark' => $request->remark ?? '',
                        'updated_at' => date('Y-m-d H:i:s'),
                        'updated_by' => auth()->user()->id
                    ];

                    if(!empty($request->file('file'))){
                        $file = $request->file('file');
                        $filenameWithExtension = $request->file('file')->getClientOriginalName();
                        $filename = pathinfo($filenameWithExtension, PATHINFO_FILENAME);
                        $extension = $request->file('file')->getClientOriginalExtension();
                        $filenameToStore = time()."_".$filename.'.'.$extension;

                        $folder_to_upload = public_path().'/uploads/orders/';

                        if (!File::exists($folder_to_upload))
                            File::makeDirectory($folder_to_upload, 0777, true, true);

                        $crud["file"] = $filenameToStore;
                    }

                    DB::beginTransaction();
                    try {
                        $update = Order::where(['id' => $request->id])->update($crud);
                       
                        if($update){
                            $product_id = $request->product_id ?? NULL;
                            $quantity = $request->quantity ?? NULL;
                            $price = $request->price ?? NULL;

                            if($product_id != null){
                                for($i=0; $i<count($product_id); $i++){
                                    if($product_id[$i] != null){
                                        $exst_detail = OrderDetails::select('id')->where(['order_id' => $request->id, 'product_id' => $product_id[$i]])->first();

                                        if(!empty($exst_detail)){
                                            $order_detail_crud = [
                                                'order_id' => $request->id,
                                                'product_id' => $product_id[$i] ?? NULL,
                                                'quantity' => $quantity[$i] ?? NULL,
                                                'price' => $price[$i] ?? NULL,
                                                'updated_at' => date('Y-m-d H:i:s'),
                                                'updated_by' => auth()->user()->id
                                            ];

                                            OrderDetails::where(['id' => $exst_detail->id])->update($order_detail_crud);
                                        }else{
                                            $order_detail_crud = [
                                                'order_id' => $request->id,
                                                'product_id' => $product_id[$i] ?? NULL,
                                                'quantity' => $quantity[$i] ?? NULL,
                                                'price' => $price[$i] ?? NULL,
                                                'created_at' => date('Y-m-d H:i:s'),
                                                'created_by' => auth()->user()->id,
                                                'updated_at' => date('Y-m-d H:i:s'),
                                                'updated_by' => auth()->user()->id
                                            ];

                                            OrderDetails::insertGetId($order_detail_crud);
                                        }
                                    }
                                }
                            }

                            if(!empty($request->file('file')))
                                $file->move($folder_to_upload, $filenameToStore);

                            DB::commit();
                            return redirect()->route('orders')->with('success', 'Order updated successfully.');
                        }else{
                            DB::rollback();
                            return redirect()->back()->with('error', 'Faild to update order!')->withInput();
                        }
                    } catch (\Exception $e) {
                        DB::rollback();
                        return redirect()->back()->with('error', 'Faild to update order!')->withInput();
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

                    $data = Order::where(['id' => $id])->first();

                    if(!empty($data)){
                        if($status == 'delete')
                            $update = Order::where('id',$id)->delete();
                        else
                            $update = Order::where(['id' => $id])->update(['status' => $status, 'updated_at' => date('Y-m-d H:i:s'), 'updated_by' => auth()->user()->id]);
                        
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

        /** delete-detail */
            public function delete_detail(Request $request){
                if(!$request->ajax()){ exit('No direct script access allowed'); }

                if(!empty($request->all())){
                    $id = $request->id;

                    $data = OrderDetails::where(['id' => $id])->first();

                    if(!empty($data)){
                        $update = OrderDetails::where(['id' => $id])->delete();                        
                        
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
        /** delete-detail */
    }