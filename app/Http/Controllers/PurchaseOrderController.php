<?php    

    namespace App\Http\Controllers;

    use Illuminate\Http\Request;
    use App\Models\PurchaseOrder;
    use App\Models\PurchaseOrderDetails;
    use App\Models\Product;
    use App\Models\Customer;
    use App\Models\Order;
    use App\Models\OrderDetails;
    use Illuminate\Support\Str;
    use App\Http\Requests\PurchaseOrderRequest;
    use Auth, Validator, DB, Mail, DataTables, File;

    class PurchaseOrderController extends Controller{
        /** index */
            public function index(Request $request){
                if($request->ajax()){
                    $data = PurchaseOrder::select('id', 'name', 'order_date', 'status')->get();

                    return Datatables::of($data)
                            ->addIndexColumn()
                            ->addColumn('action', function($data){
                                $return = '<div class="btn-group">
                                                <a href="'.route('purchase_orders.view', ['id' => base64_encode($data->id)]).'" class="btn btn-default btn-xs">
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
                                return '<a href="'.route('purchase_orders.view', ['id' => base64_encode($data->id)]).'" class="text-dark">'.$data->name.'</a>';
                            })

                            ->editColumn('order_date', function($data) {
                                return '<a href="'.route('purchase_orders.view', ['id' => base64_encode($data->id)]).'" class="text-dark">'.$data->order_date.'</a>';
                            })

                            ->editColumn('status', function($data) {
                                if($data->status == 'pending')
                                    return '<a href="'.route('purchase_orders.view', ['id' => base64_encode($data->id)]).'"><span class="badge badge-pill badge-info">Pending</span></a>';
                                else if($data->status == 'completed')
                                    return '<a href="'.route('purchase_orders.view', ['id' => base64_encode($data->id)]).'"><span class="badge badge-pill badge-success">Completed</span></a>';
                                else if($data->status == 'delivery')
                                    return '<a href="'.route('purchase_orders.view', ['id' => base64_encode($data->id)]).'"><span class="badge badge-pill badge-warning">Out For Delivery</span></a>';
                                else
                                    return '-';
                            })

                            ->rawColumns(['action', 'status', 'name', 'order_date'])
                            ->make(true);
                }

                return view('purchase_orders.index');
            }
        /** index */

        /** create */
            public function create(Request $request, $customer_id=''){
                $products = Product::select('id', 'name')->get();
                $customers = Customer::select('id', 'party_name', 'billing_name', 'contact_person', 'mobile_number', 'billing_address', 'delivery_address', 'office_contact_person')
                                        ->where(['status' => 'active'])
                                        ->get();
                                        
                return view('purchase_orders.create', ['products' => $products, 'customers' => $customers, 'customer_id' => $customer_id]);
            }
        /** create */

        /** insert */
            public function insert(PurchaseOrderRequest $request){
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

                        $folder_to_upload = public_path().'/uploads/purchase_orders/';

                        if (!File::exists($folder_to_upload))
                            File::makeDirectory($folder_to_upload, 0777, true, true);

                        $crud["file"] = $filenameToStore;
                    }

                    DB::beginTransaction();
                    try {
                        $last_id = PurchaseOrder::insertGetId($crud);
                        
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

                                        $order_detail_id = PurchaseOrderDetails::insertGetId($order_detail_crud);

                                        if($order_detail_id){
                                            if($quantity[$i] != null){
                                                $product = Product::select('quantity')->where(['id' => $product_id[$i]])->first();

                                                $qty = $product->quantity + $quantity[$i];

                                                $product_update = Product::where(['id' => $product_id[$i]])->update(['quantity' => $qty, 'updated_at' => date('Y-m-d H:i:s')]);

                                                if(!$product_update){
                                                    DB::rollback();
                                                    return redirect()->back()->with('error', 'Quantity update failed, please try again later')->withInput();
                                                }
                                            }
                                        }else{
                                            DB::rollback();
                                            return redirect()->back()->with('error', 'Product detail insertion failed, please try again later')->withInput();
                                        }
                                    }
                                }
                            }

                            if(!empty($request->file('file')))
                                $file->move($folder_to_upload, $filenameToStore);

                            DB::commit();
                            return redirect()->route('purchase_orders')->with('success', 'Order created successfully');
                        }else{
                            DB::rollback();
                            return redirect()->back()->with('error', 'Faild to create order')->withInput();
                        }
                    } catch (\Exception $e) {
                        DB::rollback();
                        return redirect()->back()->with('error', 'Faild to create order')->withInput();
                    }
                }else{
                    return redirect()->back()->with('error', 'Something went wrong')->withInput();
                }
            }
        /** insert */

        /** view */
            public function view(Request $request, $id=''){
                if($id == '')
                    return redirect()->route('purchase_orders')->with('error', 'Something went wrong');

                $id = base64_decode($id);

                $products = Product::select('id', 'name')->get();
                $customers = Customer::select('id', 'party_name', 'billing_name', 'contact_person', 'mobile_number', 'billing_address', 'delivery_address', 'office_contact_person')
                                        ->where(['status' => 'active'])
                                        ->get();

                $data = PurchaseOrder::select('id', 'name', 'file', 'remark', 'order_date')->where(['id' => $id])->first();
                
                if($data){
                    $order_details = DB::table('purchase_orders_details as pod')
                                        ->select('pod.id', 'pod.product_id', 'pod.quantity', 'pod.price', 'p.name as product_name')
                                        ->leftjoin('products as p', 'p.id', 'pod.product_id')
                                        ->where(['pod.order_id' => $data->id])
                                        ->get();

                    if($order_details->isNotEmpty())
                        $data->order_details = $order_details;
                    else
                        $data->order_details = collect();
                    
                       return view('purchase_orders.view', ['products' => $products, 'data' => $data, 'customers' => $customers]);
                }else{
                    return redirect()->route('purchase_orders')->with('error', 'No data found');
                }
            }
        /** view */ 

        /** edit */
            public function edit(Request $request, $id=''){
                if($id == '')
                    return redirect()->route('purchase_orders')->with('error', 'Something went wrong');

                $id = base64_decode($id);

                $products = Product::select('id', 'name')->get();
                $customers = Customer::select('id', 'party_name', 'billing_name', 'contact_person', 'mobile_number', 'billing_address', 'delivery_address', 'office_contact_person')
                                        ->where(['status' => 'active'])
                                        ->get();

                $data = PurchaseOrder::select('id', 'name', 'file', 'remark', 'order_date')->where(['id' => $id])->first();
                
                if($data){
                    $order_details = DB::table('purchase_orders_details as pod')
                                        ->select('pod.id', 'pod.product_id', 'pod.quantity', 'pod.price', 'p.name as product_name')
                                        ->leftjoin('products as p', 'p.id', 'pod.product_id')
                                        ->where(['pod.order_id' => $data->id])
                                        ->get();

                    if($order_details->isNotEmpty())
                        $data->order_details = $order_details;
                    else
                        $data->order_details = collect();

                    return view('purchase_orders.edit', ['products' => $products, 'data' => $data, 'customers' => $customers]);
                }else{
                    return redirect()->route('purchase_orders')->with('error', 'No data found');
                }
            }
        /** edit */ 

        /** update */
            public function update(PurchaseOrderRequest $request){
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

                        $folder_to_upload = public_path().'/uploads/purchase_orders/';

                        if (!File::exists($folder_to_upload))
                            File::makeDirectory($folder_to_upload, 0777, true, true);

                        $crud["file"] = $filenameToStore;
                    }

                    DB::beginTransaction();
                    try {
                        $update = PurchaseOrder::where(['id' => $request->id])->update($crud);
                       
                        if($update){
                            $product_id = $request->product_id ?? NULL;
                            $quantity = $request->quantity ?? NULL;
                            $price = $request->price ?? NULL;

                            if($product_id != null){
                                for($i=0; $i<count($product_id); $i++){
                                    if($product_id[$i] != null){
                                        $exst_detail = PurchaseOrderDetails::select('id', 'quantity')->where(['order_id' => $request->id, 'product_id' => $product_id[$i]])->first();

                                        if(!empty($exst_detail)){
                                            $order_detail_crud = [
                                                'order_id' => $request->id,
                                                'product_id' => $product_id[$i] ?? NULL,
                                                'quantity' => $quantity[$i] ?? NULL,
                                                'price' => $price[$i] ?? NULL,
                                                'updated_at' => date('Y-m-d H:i:s'),
                                                'updated_by' => auth()->user()->id
                                            ];

                                            $order_detail = PurchaseOrderDetails::where(['id' => $exst_detail->id])->update($order_detail_crud);

                                            if($order_detail){
                                                if($quantity[$i] != null){
                                                    $product = Product::select('quantity')->where(['id' => $product_id[$i]])->first();

                                                    $qty = $product->quantity + $quantity[$i] - $exst_detail->quantity;

                                                    $product_update = Product::where(['id' => $product_id[$i]])->update(['quantity' => $qty, 'updated_at' => date('Y-m-d H:i:s')]);

                                                    if(!$product_update){
                                                        DB::rollback();
                                                        return redirect()->back()->with('error', 'Quantity update failed, please try again later')->withInput();
                                                    }
                                                }
                                            }else{
                                                DB::rollback();
                                                return redirect()->back()->with('error', 'Product detail insertion failed, please try again later')->withInput();
                                            }
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

                                            $order_detail_id = PurchaseOrderDetails::insertGetId($order_detail_crud);

                                            if($order_detail_id){
                                                if($quantity[$i] != null){
                                                    $product = Product::select('quantity')->where(['id' => $product_id[$i]])->first();

                                                    $qty = $product->quantity + $quantity[$i];

                                                    $product_update = Product::where(['id' => $product_id[$i]])->update(['quantity' => $qty, 'updated_at' => date('Y-m-d H:i:s')]);

                                                    if(!$product_update){
                                                        DB::rollback();
                                                        return redirect()->back()->with('error', 'Quantity update failed, please try again later')->withInput();
                                                    }
                                                }
                                            }else{
                                                DB::rollback();
                                                return redirect()->back()->with('error', 'Product detail insertion failed, please try again later')->withInput();
                                            }
                                        }
                                    }
                                }
                            }

                            if(!empty($request->file('file')))
                                $file->move($folder_to_upload, $filenameToStore);

                            DB::commit();
                            return redirect()->route('purchase_orders')->with('success', 'Order updated successfully.');
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

                    $data = PurchaseOrder::where(['id' => $id])->first();
                    $orders = PurchaseOrderDetails::where(['order_id' => $id])->get();

                    if(!empty($data)){
                        DB::beginTransaction();
                        try {
                            if($status == 'delete'){
                                $update = PurchaseOrder::where(['id' => $id])->delete();

                                if($orders->isNotEmpty()){
                                    foreach($orders as $order){
                                        $product = Product::select('quantity')->where(['id' => $order->product_id])->first();
                                   
                                        $qty = $product->quantity + $order->quantity;

                                        $product_update = Product::where(['id' => $order->product_id])->update(['quantity' => $qty, 'updated_at' => date('Y-m-d H:i:s')]);

                                        if(!$product_update){
                                            DB::rollback();
                                            return response()->json(['code' => 201]);
                                        }
                                    }
                                }
                            }else{
                                $update = PurchaseOrder::where(['id' => $id])->update(['status' => $status, 'updated_at' => date('Y-m-d H:i:s'), 'updated_by' => auth()->user()->id]);
                            }

                            if($update){
                                DB::commit();
                                return response()->json(['code' => 200]);
                            }else{
                                DB::rollback();
                                return response()->json(['code' => 201]);
                            }
                        } catch (\Exception $e) {
                            DB::rollback();
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

        /** delete-detail */
            public function delete_detail(Request $request){
                if(!$request->ajax()){ exit('No direct script access allowed'); }

                if(!empty($request->all())){
                    $id = $request->id;

                    $data = PurchaseOrderDetails::where(['id' => $id])->first();

                    if(!empty($data)){
                        $update = PurchaseOrderDetails::where(['id' => $id])->delete();                        
                        
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

        /** product-detail */
            public function product_detail(Request $request){
                if(isset($request->id) && $request->id != null && $request->id != ''){
                    $product = Product::select('quantity')->where(['id' => $request->id])->first();

                    if($product){
                        $quantity = $product->quantity;
                        $required_quantity = 0;

                        $orders_ids = [];
                        $orders = Order::select('orders.id')->where(['orders.status' => 'pending'])->get()->toArray();
                        
                        if(!empty($orders)){
                            $orders_ids = array_map(function($row){return $row['id']; }, $orders);

                            $orders_details = OrderDetails::select(DB::Raw("SUM(".'quantity'.") as quantity"))->where(['product_id' => $request->id])->whereIn('order_id', $orders_ids)->first();
                            $required_quantity = $orders_details->quantity;
                        }

                        return response()->json(['code' => 200, 'data' => ['quantity' => $quantity, 'required_quantity' => $required_quantity]]);
                    }else{
                        return response()->json(['code' => 201]);
                    }
                }else{
                    return response()->json(['code' => 201]);
                }
            }
        /** product-detail */
    }