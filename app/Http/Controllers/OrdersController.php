<?php    

    namespace App\Http\Controllers;

    use Illuminate\Http\Request;
    use App\Models\Order;
    use App\Models\OrderDetails;
    use App\Models\Product;
    use Illuminate\Support\Str;
    use App\Http\Requests\OrderRequest;
    use Auth, Validator, DB, Mail, DataTables;

    class OrdersController extends Controller{

        /** index */
            public function index(Request $request){
                if($request->ajax()){
                    $data = Order::select('id', 'name', 'order_date', 'status')->get();

                    return Datatables::of($data)
                            ->addIndexColumn()
                            ->addColumn('action', function($data){
                                return '<div class="btn-group">
                                                <a href="'.route('orders.view', ['id' => base64_encode($data->id)]).'" class="btn btn-default btn-xs">
                                                    <i class="fa fa-eye"></i>
                                                </a> &nbsp;
                                                <a href="'.route('orders.edit', ['id' => base64_encode($data->id)]).'" class="btn btn-default btn-xs">
                                                        <i class="fa fa-edit"></i>
                                                    </a> &nbsp;
                                                <a href="javascript:;" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown">
                                                    <i class="fa fa-bars"></i>
                                                </a> &nbsp;
                                                <ul class="dropdown-menu">
                                                    <li><a class="dropdown-item" href="javascript:;" onclick="change_status(this);" data-status="pending" data-old_status="'.$data->status.'" data-id="'.base64_encode($data->id).'">Pending</a></li>
                                                    <li><a class="dropdown-item" href="javascript:;" onclick="change_status(this);" data-status="completed" data-old_status="'.$data->status.'" data-id="'.base64_encode($data->id).'">Completed</a></li>
                                                </ul>
                                            </div>';
                            })

                            ->editColumn('status', function($data) {
                                if($data->status == 'pending')
                                    return '<span class="badge badge-pill badge-success">Pending</span>';
                                else if($data->status == 'completed')
                                    return '<span class="badge badge-pill badge-warning">Completed</span>';
                                else
                                    return '-';
                            })

                            ->rawColumns(['action', 'status'])
                            ->make(true);
                }

                return view('orders.index');
            }
        /** index */

        /** create */
            public function create(Request $request){
                $products = Product::select('id', 'name')->get();

                return view('orders.create', ['products' => $products]);
            }
        /** create */

        /** insert */
            public function insert(OrderRequest $request){
                if($request->ajax()){ return true; }

                if(!empty($request->all())){
                    $crud = [
                            'name' => ucfirst($request->name),
                            'order_date' => $request->order_date,
                            'status' => 'pending',
                            'created_at' => date('Y-m-d H:i:s'),
                            'created_by' => auth()->user()->id,
                            'updated_at' => date('Y-m-d H:i:s'),
                            'updated_by' => auth()->user()->id
                    ];

                    DB::beginTransaction();
                    try {
                        $last_id = Order::insertGetId($crud);
                        
                        if($last_id){
                            $product_id = $request->product_id;
                            $quantity = $request->quantity;
                            $price = $request->price;

                            for($i=0; $i<count($product_id); $i++){
                                $order_detail_crud = [
                                        'order_id' => $last_id,
                                        'product_id' => $product_id[$i],
                                        'quantity' => $quantity[$i],
                                        'price' => $price[$i],
                                        'created_at' => date('Y-m-d H:i:s'),
                                        'created_by' => auth()->user()->id,
                                        'updated_at' => date('Y-m-d H:i:s'),
                                        'updated_by' => auth()->user()->id
                                ];

                                OrderDetails::insertGetId($order_detail_crud);
                            }

                            DB::commit();
                            return redirect()->route('orders')->with('success', 'Order Created Successfully.');
                        }else{
                            DB::rollback();
                            return redirect()->route('orders')->with('error', 'Faild To Create Order!');
                        }
                    } catch (\Exception $e) {
                        DB::rollback();
                        return redirect()->route('orders')->with('error', 'Faild To Create Order!');
                    }
                }else{
                    return redirect()->route('orders')->with('error', 'Something went wrong');
                }
            }
        /** insert */

        /** view */
            public function view(Request $request, $id=''){
                if($id == '')
                    return redirect()->route('orders')->with('error', 'Something went wrong Found');

                $id = base64_decode($id);

                $data = Order::select('id', 'name', 'order_date')->where(['id' => $id])->first();
                
                if($data){
                    $order_details = DB::table('orders_details as od')
                                        ->select('od.product_id', 'od.quantity', 'od.price', 'p.name as product_name')
                                        ->leftjoin('products as p', 'p.id', 'od.product_id')
                                        ->where(['od.order_id' => $data->id])
                                        ->get();

                    if($order_details->isNotEmpty())
                        $data->order_details = $order_details;
                    else
                       $data->order_details = collect();
                    
                       return view('orders.view')->with('data', $data);
                }else{
                    return redirect()->route('orders')->with('error', 'No Order Found');
                }
            }
        /** view */ 

        /** edit */
            public function edit(Request $request, $id=''){
                if($id == '')
                    return redirect()->route('orders')->with('error', 'Something went wrong Found');

                $id = base64_decode($id);

                $products = Product::select('id', 'name')->get();

                $data = Order::select('id', 'name', 'order_date')->where(['id' => $id])->first();
                
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

                    return view('orders.edit', ['products' => $products, 'data' => $data]);
                }else{
                    return redirect()->route('orders')->with('error', 'No Order Found');
                }
            }
        /** edit */ 

        /** update */
            public function update(OrderRequest $request){
                if($request->ajax()){ return true; }

                if(!empty($request->all())){
                    $crud = [
                            'name' => ucfirst($request->name),
                            'order_date' => $request->order_date,
                            'updated_at' => date('Y-m-d H:i:s'),
                            'updated_by' => auth()->user()->id
                    ];

                    DB::beginTransaction();
                    try {
                        $update = Order::where(['id' => $request->id])->update($crud);
                       
                        if($update){
                            $product_id = $request->product_id;
                            $quantity = $request->quantity;
                            $price = $request->price;

                            for($i=0; $i<count($product_id); $i++){
                                $exst_detail = OrderDetails::select('id')->where(['order_id' => $request->id, 'product_id' => $product_id[$i]])->first();

                                if(!empty($exst_detail)){
                                    $order_detail_crud = [
                                            'order_id' => $request->id,
                                            'product_id' => $product_id[$i],
                                            'quantity' => $quantity[$i],
                                            'price' => $price[$i],
                                            'updated_at' => date('Y-m-d H:i:s'),
                                            'updated_by' => auth()->user()->id
                                    ];

                                    OrderDetails::where(['id' => $exst_detail->id])->update($order_detail_crud);
                                }else{
                                    $order_detail_crud = [
                                            'order_id' => $request->id,
                                            'product_id' => $product_id[$i],
                                            'quantity' => $quantity[$i],
                                            'price' => $price[$i],
                                            'created_at' => date('Y-m-d H:i:s'),
                                            'created_by' => auth()->user()->id,
                                            'updated_at' => date('Y-m-d H:i:s'),
                                            'updated_by' => auth()->user()->id
                                    ];

                                    OrderDetails::insertGetId($order_detail_crud);
                                }
                            }

                            DB::commit();
                            return redirect()->route('orders')->with('success', 'Order Updated Successfully.');
                        }else{
                            DB::rollback();
                            return redirect()->route('orders')->with('error', 'Faild To Update Order!');
                        }
                    } catch (\Exception $e) {
                        DB::rollback();
                        return redirect()->route('orders')->with('error', 'Faild To Update Order!');
                    }
                }else{
                    return redirect()->route('orders')->with('error', 'Something went wrong');
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
                        if($status == 'deleted')
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