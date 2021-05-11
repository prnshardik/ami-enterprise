<?php

    namespace App\Http\Controllers\API;

    use App\Http\Controllers\Controller;
    use Illuminate\Http\Request;
    use App\Models\Product;
    use Auth, DB, Validator, File;

    class ProductsController extends Controller{

        /** products */
            public function products(Request $request){
                $data = Product::select('id', 'name', 'quantity', 'unit', 'color', 'price', 'note')->get();

                if($data->isNotEmpty())
                    return response()->json(['status' => 200, 'message' => 'success', 'data' => $data]);
                else
                    return response()->json(['status' => 201, 'message' => 'No Products Found']);
            }
        /** products */

        /** product */
            public function product(Request $request, $id){
                $data = Product::select('id', 'name', 'quantity', 'unit', 'color', 'price', 'note')->where(['id' => $id])->first();

                if(!empty($data))
                    return response()->json(['status' => 200, 'message' => 'success', 'data' => $data]);
                else
                    return response()->json(['status' => 201, 'message' => 'No Product Found']);
            }
        /** product */

        /** insert */
            public function insert(Request $request){
                $rules = [
                    'name' => 'required|unique:products,name',
                    'quantity' => 'required',
                    'unit' => 'required',
                    'color' => 'required',
                    'price' => 'required'
                ];

                $validator = Validator::make($request->all(), $rules);

                if($validator->fails())
                    return response()->json(['status' => 422, 'message' => $validator->errors()]);

                $crud = [
                        'name' => ucfirst($request->name),
                        'quantity' => $request->quantity, 
                        'unit' => $request->unit, 
                        'color' => $request->color, 
                        'price' => $request->price, 
                        'note' => $request->note ?? NULL,
                        'created_at' => date('Y-m-d H:i:s'),
                        'created_by' => auth('sanctum')->user()->id,
                        'updated_at' => date('Y-m-d H:i:s'),
                        'updated_by' => auth('sanctum')->user()->id
                ];

                $last_id = Product::insertGetId($crud);

                if($last_id)
                    return response()->json(['status' => 200, 'message' => 'Product added successfully']);
                else
                    return response()->json(['status' => 201, 'message' => 'Something went wrong.']);
            }
        /** insert */

        /** update */
            public function update(Request $request){
                $rules = [
                    'id' => 'required',
                    'name' => 'required|unique:products,name,'.$request->id,
                    'quantity' => 'required',
                    'unit' => 'required',
                    'color' => 'required',
                    'price' => 'required'
                ];

                $validator = Validator::make($request->all(), $rules);

                if($validator->fails())
                    return response()->json(['status' => 422, 'message' => $validator->errors()]);

                $exst_data = Product::where(['id' => $request->id])->first();

                $crud = [
                        'name' => ucfirst($request->name),
                        'quantity' => $request->quantity, 
                        'unit' => $request->unit, 
                        'color' => $request->color, 
                        'price' => $request->price, 
                        'note' => $request->note ?? NULL,
                        'updated_at' => date('Y-m-d H:i:s'),
                        'updated_by' => auth('sanctum')->user()->id
                ];

                $update = Product::where(['id' => $request->id])->update($crud);

                if($update)
                    return response()->json(['status' => 200, 'message' => 'Product updated successfully']);
                else
                    return response()->json(['status' => 201, 'message' => 'Something went wrong.']);
            }
        /** update */

        /** delete */
            public function delete(Request $request){
                $product = Product::where(['id' => $request->id])->delete();

                if($product)
                    return response()->json(['status' => 200, 'message' => 'Product deleted successfully']);
                else
                    return response()->json(['status' => 201, 'message' => 'No Products Found']);
            }
        /** delete */
    }
