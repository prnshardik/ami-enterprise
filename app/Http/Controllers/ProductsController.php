<?php    

    namespace App\Http\Controllers;

    use Illuminate\Http\Request;
    use App\Models\Product;
    use App\Models\User;
    use Illuminate\Support\Str;
    use App\Http\Requests\ProductsRequest;
    use Auth, Validator, DB, Mail, DataTables;

    class ProductsController extends Controller{

        /** index */
            public function index(Request $request){
                if($request->ajax()){
                    $data = Product::select('id', 'name')->get();

                    return Datatables::of($data)
                            ->addIndexColumn()
                            ->addColumn('action', function($data){
                                return ' <div class="btn-group">
                                                <a href="'.route('products.edit', ['id' => base64_encode($data->id)]).'" class="btn btn-default btn-xs">
                                                    <i class="fa fa-edit"></i>
                                                </a> &nbsp;
                                                <a href="'.route('products.delete', ['id' => base64_encode($data->id)]).'" class="btn btn-default btn-xs">
                                                    <i class="fa fa-trash text-danger"></i>
                                                </a> &nbsp;
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

                return view('products.index');
            }
        /** index */

        /** create */
            public function create(Request $request){
                return view('products.create');
            }
        /** create */

        /** insert */
            public function insert(ProductsRequest $request){
                if($request->ajax()){ return true; }

                if(!empty($request->all())){
                    $crud = [
                            'name' => ucfirst($request->name),
                            'created_at' => date('Y-m-d H:i:s'),
                            'created_by' => auth()->user()->id,
                            'updated_at' => date('Y-m-d H:i:s'),
                            'updated_by' => auth()->user()->id
                    ];

                    $last_id = Product::insertGetId($crud);
                    
                    if($last_id)
                        return redirect()->route('products')->with('success', 'Product Created Successfully.');
                    else
                        return redirect()->route('products')->with('error', 'Faild To Create Product!');
                }else{
                    return redirect()->back('products')->with('error', 'Something went wrong');
                }
            }
        /** insert */

        /** edit */
            public function edit(Request $request, $id=''){
                if($id == '')
                    return redirect()->route('products')->with('error', 'Something went wrong Found');

                $id = base64_decode($id);

                $data = Product::select('id', 'name')->where(['id' => $id])->first();
                
                if($data)
                    return view('products.edit')->with('data', $data);
                else
                    return redirect()->route('products')->with('error', 'No Product Found');
            }
        /** edit */ 

        /** update */
            public function update(ProductsRequest $request){
                if($request->ajax()){ return true; }

                if(!empty($request->all())){
                    $crud = [
                            'name' => ucfirst($request->name),
                            'updated_at' => date('Y-m-d H:i:s'),
                            'updated_by' => auth()->user()->id
                    ];

                    $update = Product::where(['id' => $request->id])->update($crud);

                    if($update)
                        return redirect()->route('products')->with('success', 'Product Updated Successfully.');
                    else
                        return redirect()->route('products')->with('error', 'Faild To Update Product!');
                }else{
                    return redirect()->back('products')->with('error', 'Something went wrong');
                }
            }
        /** update */

        /** delete */
            public function delete(Request $request){
                $id = base64_decode($request->id);

                $delete = Product::where(['id' => $id])->delete();
                
                if($delete)
                    return redirect()->route('products')->with('success', 'Product Deleted Successfully.');
                else
                    return redirect()->route('products')->with('error', 'Faild To Delete Product !');
            }
        /** delete */
    }