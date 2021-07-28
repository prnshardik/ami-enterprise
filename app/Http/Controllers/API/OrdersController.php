<?php

    namespace App\Http\Controllers\API;

    use App\Http\Controllers\Controller;
    use Illuminate\Http\Request;
    use App\Models\Order;
    use App\Models\customer;
    use App\Models\OrderDetails;
    use Auth, DB, Validator, File;

    class OrdersController extends Controller{
        /** orders */
            public function orders(Request $request){
                $path = URL('/uploads/orders').'/';

                $data = Order::select('id', 'name', 'order_date', 'remark', 'status',
                                    DB::Raw("CASE
                                        WHEN ".'file'." != '' THEN CONCAT("."'".$path."'".", ".'file'.")
                                        ELSE 'null'
                                    END as file"))
                                ->get();

                if($data->isNotEmpty())
                    return response()->json(['status' => 200, 'message' => 'Data found', 'data' => $data]);
                else
                    return response()->json(['status' => 201, 'message' => 'No records found']);
            }
        /** orders */

        /** pending-orders */
            public function pending_orders(Request $request){
                $path = URL('/uploads/orders').'/';

                $data = Order::select('id', 'name', 'order_date', 'remark', 'status',
                                    DB::Raw("CASE
                                        WHEN ".'file'." != '' THEN CONCAT("."'".$path."'".", ".'file'.")
                                        ELSE 'null'
                                    END as file"))
                                ->where(['status' => 'pending'])
                                ->get();

                if($data->isNotEmpty())
                    return response()->json(['status' => 200, 'message' => 'Data found', 'data' => $data]);
                else
                    return response()->json(['status' => 201, 'message' => 'No records found']);
            }
        /** pending-orders */

        /** completed-orders */
            public function completed_orders(Request $request){
                $path = URL('/uploads/orders').'/';

                $data = Order::select('id', 'name', 'order_date', 'remark', 'status',
                                    DB::Raw("CASE
                                        WHEN ".'file'." != '' THEN CONCAT("."'".$path."'".", ".'file'.")
                                        ELSE 'null'
                                    END as file"))
                                ->where(['status' => 'completed'])
                                ->get();

                if($data->isNotEmpty())
                    return response()->json(['status' => 200, 'message' => 'Data found', 'data' => $data]);
                else
                    return response()->json(['status' => 201, 'message' => 'No records found']);
            }
        /** completed-orders */

        /** completed-delivered */
            public function delivered_orders(Request $request){
                $path = URL('/uploads/orders').'/';

                $data = Order::select('id', 'name', 'order_date', 'remark', 'status',
                                    DB::Raw("CASE
                                        WHEN ".'file'." != '' THEN CONCAT("."'".$path."'".", ".'file'.")
                                        ELSE 'null'
                                    END as file"))
                                ->where(['status' => 'delivered'])
                                ->get();

                if($data->isNotEmpty())
                    return response()->json(['status' => 200, 'message' => 'Data found', 'data' => $data]);
                else
                    return response()->json(['status' => 201, 'message' => 'No records found']);
            }
        /** completed-delivered */

        /** order */
            public function order(Request $request, $id){
                $path = URL('/uploads/orders').'/';

                $data = Order::select('id', 'name', 'order_date', 'remark', 'status',
                                    DB::Raw("CASE
                                        WHEN ".'file'." != '' THEN CONCAT("."'".$path."'".", ".'file'.")
                                        ELSE 'null'
                                    END as file"))
                                ->where(['id' => $id])
                                ->first();

                if(!empty($data)){
                    $order_details = DB::table('orders_details as od')
                                        ->select('od.id', 'od.product_id', 'od.quantity', 'od.price', 'od.remark', 'p.name as product_name')
                                        ->leftjoin('products as p', 'p.id', 'od.product_id')
                                        ->where(['od.order_id' => $data->id])
                                        ->get();

                    if($order_details->isNotEmpty())
                        $data->order_details = $order_details;
                    else
                       $data->order_details = collect();

                    return response()->json(['status' => 200, 'message' => 'Data found', 'data' => $data]);
                }else{
                    return response()->json(['status' => 201, 'message' => 'No record found']);
                }
            }
        /** order */

        /** insert */
            public function insert(Request $request){
                $rules = [
                    'name' => 'required'
                ];

                $validator = Validator::make($request->all(), $rules);

                if($validator->fails())
                    return response()->json(['status' => 422, 'message' => $validator->errors()]);

                $crud = [
                    'name' => $request->name,
                    'order_date' => $request->order_date ?? NULL,
                    'status' => 'pending',
                    'remark' => $request->remark ?? NULL,
                    'created_at' => date('Y-m-d H:i:s'),
                    'created_by' => auth('sanctum')->user()->id,
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_by' => auth('sanctum')->user()->id
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
                        $remarks = $request->remarks ?? NULL;

                        if($product_id != null){
                            for($i=0; $i<count($product_id); $i++){
                                if($product_id[$i] != null){
                                    $order_detail_crud = [
                                        'order_id' => $last_id,
                                        'product_id' => $product_id[$i] ?? NULL,
                                        'quantity' => $quantity[$i] ?? NULL,
                                        'price' => $price[$i] ?? NULL,
                                        'remark' => $remarks[$i] ?? NULL,
                                        'created_at' => date('Y-m-d H:i:s'),
                                        'created_by' => auth('sanctum')->user()->id,
                                        'updated_at' => date('Y-m-d H:i:s'),
                                        'updated_by' => auth('sanctum')->user()->id
                                    ];
    
                                    OrderDetails::insertGetId($order_detail_crud);
                                }
                            }
                        }

                        if(!empty($request->file('file')))
                            $file->move($folder_to_upload, $filenameToStore);

                        DB::commit();
                        return response()->json(['status' => 200, 'message' => 'Record added successfully']);
                    }else{
                        DB::rollback();
                        return response()->json(['status' => 201, 'message' => 'Something went wrong']);
                    }
                } catch (\Exception $e) {
                    DB::rollback();
                    return response()->json(['status' => 201, 'message' => 'Something went wrong']);
                }
            }
        /** insert */

        /** update */
            public function update(Request $request){
                $rules = [
                    'id' => 'required',
                    'name' => 'required'
                ];

                $validator = Validator::make($request->all(), $rules);

                if($validator->fails())
                    return response()->json(['status' => 422, 'message' => $validator->errors()]);

                $crud = [
                    'name' => $request->name,
                    'order_date' => $request->order_date ?? NULL,
                    'remark' => $request->remark ?? '',
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_by' => auth('sanctum')->user()->id
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
                        $remarks = $request->remarks ?? NULL;

                        if($product_id != null){
                            for($i=0; $i<count($product_id); $i++){
                                if($product_id[$i] != null){
                                    $exst_detail = OrderDetails::select('id')->where(['order_id' => $request->id, 'product_id' => $product_id[$i]])->first();

                                    if(!empty($exst_detail)){
                                        $order_detail_crud = [
                                            'order_id' => $request->id,
                                            'product_id' => $product_id[$i],
                                            'quantity' => $quantity[$i],
                                            'price' => $price[$i],
                                            'remark' => $remarks[$i],
                                            'updated_at' => date('Y-m-d H:i:s'),
                                            'updated_by' => auth('sanctum')->user()->id
                                        ];

                                        OrderDetails::where(['id' => $exst_detail->id])->update($order_detail_crud);
                                    }else{
                                        if($product_id[$i] != null){
                                            $order_detail_crud = [
                                                'order_id' => $request->id,
                                                'product_id' => $product_id[$i],
                                                'quantity' => $quantity[$i],
                                                'price' => $price[$i],
                                                'remark' => $remarks[$i],
                                                'created_at' => date('Y-m-d H:i:s'),
                                                'created_by' => auth('sanctum')->user()->id,
                                                'updated_at' => date('Y-m-d H:i:s'),
                                                'updated_by' => auth('sanctum')->user()->id
                                            ];
        
                                            OrderDetails::insertGetId($order_detail_crud);
                                        }
                                    }
                                }
                            }
                        }

                        if(!empty($request->file('file')))
                            $file->move($folder_to_upload, $filenameToStore);

                        DB::commit();
                        return response()->json(['status' => 200, 'message' => 'Record updated successfully']);
                    }else{
                        DB::rollback();
                        return response()->json(['status' => 201, 'message' => 'Faild to update record']);
                    }
                } catch (\Exception $e) {
                    DB::rollback();
                    return response()->json(['status' => 201, 'message' => 'Something went wrong']);
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

                $data = Order::where(['id' => $request->id])->first();
                $orders = OrderDetails::where(['order_id' => $id])->get();

                if(!empty($data)){
                    DB::beginTransaction();
                    try {
                        if($request->status == 'deleted'){
                            $update = Order::where(['id' => $request->id])->delete();
                        }else{
                            $update = Order::where(['id' => $request->id])->update(['status' => $request->status, 'updated_at' => date('Y-m-d H:i:s'), 'updated_by' => auth('sanctum')->user()->id]);

                            if($data->status == 'pending' && ($status == 'completed' || $status == 'delivery')){
                                if($orders->isNotEmpty()){
                                    foreach($orders as $order){
                                        $product = Product::select('quantity')->where(['id' => $order->product_id])->first();
                                   
                                        $qty = $product->quantity - $order->quantity;

                                        $product_update = Product::where(['id' => $order->product_id])->update(['quantity' => $qty, 'updated_at' => date('Y-m-d H:i:s')]);

                                        if(!$product_update){
                                            DB::rollback();
                                            return response()->json(['status' => 201, 'message' => 'Something went wrong']);
                                        }
                                    }
                                }
                            }elseif(($data->status == 'completed' || $data->status == 'delivery') && $status == 'pending'){
                                if($orders->isNotEmpty()){
                                    foreach($orders as $order){
                                        $product = Product::select('quantity')->where(['id' => $order->product_id])->first();
                                   
                                        $qty = $product->quantity + $order->quantity;

                                        $product_update = Product::where(['id' => $order->product_id])->update(['quantity' => $qty, 'updated_at' => date('Y-m-d H:i:s')]);

                                        if(!$product_update){
                                            DB::rollback();
                                            return response()->json(['status' => 201, 'message' => 'Something went wrong']);
                                        }
                                    }
                                }
                            }
                        }
                        
                        if($update){
                            DB::commit();
                            return response()->json(['status' => 200, 'message' => 'Record status changed successfully']);
                        }else{
                            DB::rollback();
                            return response()->json(['status' => 201, 'message' => 'Something went wrong']);
                        }
                    }catch (\Exception $e) {
                        DB::rollback();
                        return response()->json(['status' => 201, 'message' => 'Something went wrong']);
                    }
                }else{
                    return response()->json(['status' => 201, 'message' => 'Something went wrong']);
                }
            }
        /** change-status */

        /** item-delete */
            public function item_delete(Request $request){ 
                $rules = [
                    'id' => 'required'
                ];

                $validator = Validator::make($request->all(), $rules);

                if($validator->fails())
                    return response()->json(['status' => 422, 'message' => $validator->errors()]);

                $data = OrderDetails::where(['id' => $request->id])->first();

                if(!empty($data)){
                    $delete = OrderDetails::where(['id' => $request->id])->delete();                        
                    
                    if($delete)
                        return response()->json(['status' => 200, 'message' => 'Record delete successfully']);
                    else
                        return response()->json(['status' => 201, 'message' => 'Failed to detele record']);
                }else{
                    return response()->json(['status' => 201, 'message' => 'Something went wrong']);
                }
            }
        /** item-delete */

        /** order-deliver */
            public function deliver(Request $request){ 
                $rules = [
                    'id' => 'required',
                    'file' => 'mimes:jpeg,jpg,png,gif|required|max:5000' 
                ];

                $validator = Validator::make($request->all(), $rules);

                if($validator->fails())
                    return response()->json(['status' => 422, 'message' => $validator->errors()]);

                $data = Order::where(['id' => $request->id, 'status' => 'completed'])->first();
                
                if($data == null && empty($data))
                    return response()->json(['status' => 201, 'message' => 'order is not completed, please first complete order']);

                $crud = [
                    'status' => 'delivered'  
                ];

                if(!empty($request->file('file'))){
                    $file = $request->file('file');
                    $filenameWithExtension = $request->file('file')->getClientOriginalName();
                    $filename = pathinfo($filenameWithExtension, PATHINFO_FILENAME);
                    $extension = $request->file('file')->getClientOriginalExtension();
                    $filenameToStore = time()."_".$filename.'.'.$extension;

                    $folder_to_upload = public_path().'/uploads/order/';

                    if (!\File::exists($folder_to_upload)) {
                        \File::makeDirectory($folder_to_upload, 0777, true, true);
                    }

                    $crud["file"] = $filenameToStore;
                }

                $update = Order::where(['id' => $request->id])->update($crud);

                if($update){
                    if(!empty($request->file('file')))
                        $file->move($folder_to_upload, $filenameToStore);
                    
                    return response()->json(['status' => 200, 'message' => 'Order deliver successfully']);
                }else{
                    return response()->json(['status' => 201, 'message' => 'Something went wrong']);
                }
            }
        /** order-deliver */

        /** order-customer */
            public function customer(Request $request){
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
        /** order-customer */
    }
