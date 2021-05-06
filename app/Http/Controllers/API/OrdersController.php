<?php

    namespace App\Http\Controllers\API;

    use App\Http\Controllers\Controller;
    use Illuminate\Http\Request;
    use App\Models\Order;
    use App\Models\OrderDetails;
    use Auth, DB, Validator, File;

    class OrdersController extends Controller{

        /** orders */
            public function orders(Request $request){
                $data = Order::select('id', 'name', 'order_date', 'status')->get();

                if($data->isNotEmpty())
                    return response()->json(['status' => 200, 'message' => 'success', 'data' => $data]);
                else
                    return response()->json(['status' => 201, 'message' => 'No Orders Found']);
            }
        /** orders */

        /** pending-orders */
            public function pending_orders(Request $request){
                $data = Order::select('id', 'name', 'order_date', 'status')->where(['status' => 'pending'])->get();

                if($data->isNotEmpty())
                    return response()->json(['status' => 200, 'message' => 'success', 'data' => $data]);
                else
                    return response()->json(['status' => 201, 'message' => 'No Orders Found']);
            }
        /** pending-orders */

        /** completed-orders */
            public function completed_orders(Request $request){
                $data = Order::select('id', 'name', 'order_date', 'status')->where(['status' => 'completed'])->get();

                if($data->isNotEmpty())
                    return response()->json(['status' => 200, 'message' => 'success', 'data' => $data]);
                else
                    return response()->json(['status' => 201, 'message' => 'No Orders Found']);
            }
        /** completed-orders */

        /** order */
            public function order(Request $request, $id){
                $data = Order::select('id', 'name', 'order_date', 'status')->where(['id' => $id])->first();

                if(!empty($data)){
                    $order_details = DB::table('orders_details as od')
                                        ->select('od.id', 'od.product_id', 'od.quantity', 'od.price', 'p.name as product_name')
                                        ->leftjoin('products as p', 'p.id', 'od.product_id')
                                        ->where(['od.order_id' => $data->id])
                                        ->get();

                    if($order_details->isNotEmpty())
                        $data->order_details = $order_details;
                    else
                       $data->order_details = collect();

                    return response()->json(['status' => 200, 'message' => 'success', 'data' => $data]);
                }else{
                    return response()->json(['status' => 201, 'message' => 'No Order Found']);
                }
            }
        /** order */

        /** insert */
            public function insert(Request $request){
                $rules = [
                    'name' => 'required',
                    'order_date' => 'required',
                    'product_id' => 'required|array|min:1',
                    'quantity' => 'required|array|min:1',
                    'price' => 'required|array|min:1'
                ];

                $validator = Validator::make($request->all(), $rules);

                if($validator->fails())
                    return response()->json(['status' => 422, 'message' => $validator->errors()]);

                $crud = [
                    'name' => ucfirst($request->name),
                    'order_date' => $request->order_date,
                    'status' => 'pending',
                    'created_at' => date('Y-m-d H:i:s'),
                    'created_by' => auth('sanctum')->user()->id,
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_by' => auth('sanctum')->user()->id
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
                                    'created_by' => auth('sanctum')->user()->id,
                                    'updated_at' => date('Y-m-d H:i:s'),
                                    'updated_by' => auth('sanctum')->user()->id
                            ];

                            OrderDetails::insertGetId($order_detail_crud);
                        }

                        DB::commit();
                        return response()->json(['status' => 200, 'message' => 'Order created successfully']);
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
                    'name' => 'required',
                    'order_date' => 'required',
                    'product_id' => 'required|array|min:1',
                    'quantity' => 'required|array|min:1',
                    'price' => 'required|array|min:1'
                ];

                $validator = Validator::make($request->all(), $rules);

                if($validator->fails())
                    return response()->json(['status' => 422, 'message' => $validator->errors()]);

                $crud = [
                    'name' => ucfirst($request->name),
                    'order_date' => $request->order_date,
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_by' => auth('sanctum')->user()->id
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
                                    'updated_by' => auth('sanctum')->user()->id
                                ];

                                OrderDetails::where(['id' => $exst_detail->id])->update($order_detail_crud);
                            }else{
                                $order_detail_crud = [
                                    'order_id' => $request->id,
                                    'product_id' => $product_id[$i],
                                    'quantity' => $quantity[$i],
                                    'price' => $price[$i],
                                    'created_at' => date('Y-m-d H:i:s'),
                                    'created_by' => auth('sanctum')->user()->id,
                                    'updated_at' => date('Y-m-d H:i:s'),
                                    'updated_by' => auth('sanctum')->user()->id
                                ];

                                OrderDetails::insertGetId($order_detail_crud);
                            }
                        }

                        DB::commit();
                        return response()->json(['status' => 200, 'message' => 'Order updated successfully']);
                    }else{
                        DB::rollback();
                        return response()->json(['status' => 201, 'message' => 'Faild to update order']);
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

                if(!empty($data)){
                    if($request->status == 'deleted')
                        $update = Order::where(['id' => $request->id])->delete();
                    else
                        $update = Order::where(['id' => $request->id])->update(['status' => $request->status, 'updated_at' => date('Y-m-d H:i:s'), 'updated_by' => auth('sanctum')->user()->id]);
                    
                    if($update){
                        return response()->json(['status' => 200, 'message' => 'Status changed successfully']);
                    }else{
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
                        return response()->json(['status' => 200, 'message' => 'Order item delete successfully']);
                    else
                        return response()->json(['status' => 200, 'message' => 'Failed to detele order item']);
                }else{
                    return response()->json(['status' => 200, 'message' => 'Something went wrong']);
                }
            }
        /** item-delete */
    }
