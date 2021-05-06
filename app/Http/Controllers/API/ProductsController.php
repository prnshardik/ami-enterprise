<?php

    namespace App\Http\Controllers\API;

    use App\Http\Controllers\Controller;
    use Illuminate\Http\Request;
    use App\Models\Product;
    use Auth, DB, Validator, File;

    class ProductsController extends Controller{

        /** products */
            public function products(Request $request){
                $data = Product::select('id', 'name')->get();

                if($data->isNotEmpty())
                    return response()->json(['status' => 200, 'message' => 'success', 'data' => $data]);
                else
                    return response()->json(['status' => 201, 'message' => 'No Products Found']);
            }
        /** products */

        /** product */
            public function product(Request $request, $id){
                $data = Product::select('id', 'name')->where(['id' => $id])->first();

                if(!empty($data))
                    return response()->json(['status' => 200, 'message' => 'success', 'data' => $data]);
                else
                    return response()->json(['status' => 201, 'message' => 'No Product Found']);
            }
        /** product */

        /** insert */
            public function insert(Request $request){
                $rules = ['name' => 'required'];

                $validator = Validator::make($request->all(), $rules);

                if($validator->fails())
                    return response()->json(['status' => 422, 'message' => $validator->errors()]);

                $crud = [
                        'name' => ucfirst($request->name),
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
