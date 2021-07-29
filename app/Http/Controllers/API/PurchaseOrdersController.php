<?php    

    namespace App\Http\Controllers\API;

    use App\Http\Controllers\Controller;
    use Illuminate\Http\Request;
    use App\Models\PurchaseOrder;
    use App\Models\PurchaseOrderDetails;
    use App\Models\Product;
    use App\Models\Customer;
    use App\Models\Order;
    use App\Models\OrderDetails;
    use Illuminate\Support\Str;
    use Auth, Validator, DB, Mail, DataTables, File;

    class PurchaseOrdersController extends Controller{
        /** orders */
            public function orders(Request $request){
                $data = PurchaseOrder::all();
                
                if($data->isNotEmpty()){
                    return response()->json(['code' => 200 , 'message' => 'Record found' ,'data' => $data]);
                }else{
                    return response()->json(['code' => 201 , 'message' => 'No record found']);
                }
            }
        /** orders */


        /** insert */
            public function insert(Request $request){
                $rules = [
                    'name' => 'required',
                    'order_date' => 'required',
                    'remark' => 'required',
                ];

                $validator = Validator::make($request->all(), $rules);

                if($validator->fails())
                    return response()->json(['status' => 422, 'message' => $validator->errors()]);  
              
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
                                                    return response()->json(['code' => 201 ,'message' => 'Quantity update failed, please try again later']);
                                                }
                                            }
                                        }else{
                                            DB::rollback();
                                            return response()->json(['code' => 201 ,'message' => 'Quantity update failed, please try again later']);
                                        }
                                    }
                                }
                            }

                            if(!empty($request->file('file')))
                                $file->move($folder_to_upload, $filenameToStore);

                            DB::commit();
                            return response()->json(['code' => 200 ,'message' => 'Record added successfully']);
                        }else{
                            DB::rollback();
                            return response()->json(['code' => 201 ,'message' => 'Faild to add record']);
                        }
                    } catch (\Exception $e) {
                        DB::rollback();
                        return response()->json(['code' => 201 ,'message' => 'Faild to add record']);
                    }
                
            }
        /** insert */

        /** order */
            public function order(Request $request){
                $rules = [
                    'id' => 'required'
                ];

                $validator = Validator::make($request->all(), $rules);

                if($validator->fails())
                    return response()->json(['status' => 422, 'message' => $validator->errors()]);

                $id = $request->id;

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
                    
                       return response()->json(['code' => 200 , 'message' =>'Record found' , 'data' => $data]);
                }else{
                    return response()->json(['code' => 201 , 'message' =>'No record found']);
                }
            }
        /** order */ 


        /** update */
            public function update(Request $request){
                
                $rules = [
                    'id' => 'required',
                    'name' => 'required',
                    'order_date' => 'required',
                    'remark' => 'required',
                ];

                $validator = Validator::make($request->all(), $rules);

                if($validator->fails())
                    return response()->json(['status' => 422, 'message' => $validator->errors()]);
               
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
                                                        return response()->json(['code' => 201 ,'message'=>'Quantity update failed, please try again later']);
                                                    }
                                                }
                                            }else{
                                                DB::rollback();
                                                return response()->json(['code' => 201 ,'message'=>'Product detail insertion failed, please try again later']);
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
                                                        return response()->json(['code' => 201 ,'message'=>'Quantity update failed, please try again later']);
                                                    }
                                                }
                                            }else{
                                                DB::rollback();
                                                return response()->json(['code' => 201 ,'message'=>'Product detail insertion failed, please try again later']);
                                            }
                                        }
                                    }
                                }
                            }

                            if(!empty($request->file('file')))
                                $file->move($folder_to_upload, $filenameToStore);

                            DB::commit();
                            return response()->json(['code' => 200 ,'message'=>'Order updated successfully']);
                        }else{
                            DB::rollback();
                            return response()->json(['code' => 201 ,'message'=>'Faild to update order!']);
                        }
                    } catch (\Exception $e) {
                        DB::rollback();
                        return response()->json(['code' => 201 ,'message'=>'Faild to update order!']);
                    }
                
            }
        /** update */

        /** change-status */
            public function change_status(Request $request){
                    
                $rules = [
                    'id' => 'required',
                    'status' => 'required'
                ];

                $validator = Validator::make($request->all(), $rules);

                if($validator->fails())
                    return response()->json(['status' => 422, 'message' => $validator->errors()]);

              
                    $id = $request->id;
                    $status = $request->status;

                    $data = PurchaseOrder::where(['id' => $id])->first();
                    $orders = PurchaseOrderDetails::where(['order_id' => $id])->get();

                    if(!empty($data)){
                        DB::beginTransaction();
                        try {
                            if($status == 'deleted'){
                                $update = PurchaseOrder::where(['id' => $id])->delete();

                                if($orders->isNotEmpty()){
                                    foreach($orders as $order){
                                        $product = Product::select('quantity')->where(['id' => $order->product_id])->first();
                                   
                                        $qty = $product->quantity + $order->quantity;

                                        $product_update = Product::where(['id' => $order->product_id])->update(['quantity' => $qty, 'updated_at' => date('Y-m-d H:i:s')]);

                                        if(!$product_update){
                                            DB::rollback();
                                            return response()->json(['code' => 201 ,'message' => 'Faild to update quantity please try again later']);
                                        }
                                    }
                                }
                            }else{
                                $update = PurchaseOrder::where(['id' => $id])->update(['status' => $status, 'updated_at' => date('Y-m-d H:i:s'), 'updated_by' => auth('sanctum')->user()->id]);
                            }

                            if($update){
                                DB::commit();
                                return response()->json(['code' => 200 , 'message' => 'Record status change successfully']);
                            }else{
                                DB::rollback();
                                return response()->json(['code' => 201 , 'message' => 'Faild to change record status']);
                            }
                        } catch (\Exception $e) {
                            DB::rollback();
                            return response()->json(['code' => 201 , 'message' => 'Faild to change record status']);
                        }
                    }else{
                        return response()->json(['code' => 201 , 'message' => 'Faild to change record status']);
                    }
                
            }
        /** change-status */

        /** delete-detail */
            public function item_delete(Request $request){
                
                $rules = [
                    'id' => 'required'
                ];

                $validator = Validator::make($request->all(), $rules);

                if($validator->fails())
                    return response()->json(['status' => 422, 'message' => $validator->errors()]);
                
                    $id = $request->id;

                    $data = PurchaseOrderDetails::where(['id' => $id])->first();

                    if(!empty($data)){
                        $update = PurchaseOrderDetails::where(['id' => $id])->delete();                        
                        
                        if($update)
                            return response()->json(['code' => 200 , 'message' => 'Record deleted successfully']);
                        else
                            return response()->json(['code' => 201 , 'message' => 'Faild to delete record']);
                    }else{
                        return response()->json(['code' => 201 , 'message' => 'Faild to delete record']);
                    }
                
            }
        /** delete-detail */

        /** product-detail */
            public function product_details(Request $request){
                $rules = [
                    'id' => 'required'
                ];

                $validator = Validator::make($request->all(), $rules);

                if($validator->fails())
                    return response()->json(['status' => 422, 'message' => $validator->errors()]);

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

                    return response()->json(['code' => 200, 'message' =>'Record found' , 'data' => ['quantity' => $quantity, 'required_quantity' => $required_quantity]]);
                }else{
                    return response()->json(['code' => 201 ,'message' =>'No record found']);
                }
                
            }
        /** product-detail */
    }