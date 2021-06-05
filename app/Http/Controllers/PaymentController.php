<?php    

    namespace App\Http\Controllers;

    use Illuminate\Http\Request;
    use Illuminate\Support\Str;
    use App\Http\Requests\PaymentRequest;
    use App\Imports\PaymentImport;
    use Auth, Validator, DB, Mail, DataTables, Excel;

    class PaymentController extends Controller{
        /** import-view */
            public function file_import(){
                return view('payment.import');
            }
        /** import-view */

        /** import */
            public function import(PaymentRequest $request){
                Excel::import(new PaymentImport, $request->file('file'));

                return back();
            }
        /** import */
    }