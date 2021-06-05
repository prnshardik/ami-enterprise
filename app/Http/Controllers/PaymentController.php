<?php    

    namespace App\Http\Controllers;

    use Illuminate\Http\Request;
    use App\Models\Payment;
    use Illuminate\Support\Str;
    use App\Http\Requests\PaymentRequest;
    use App\Imports\PaymentImport;
    use Auth, Validator, DB, Mail, DataTables, Excel;

    class PaymentController extends Controller{
        /** index */
            public function index(Request $request){
                if($request->ajax()){
                    $data = Payment::select('id', 'party_name', 'bill_no', 'bill_date', 'due_days', 'bill_amount', 'balance_amount', 'mobile_no')->get();

                    return Datatables::of($data)
                            ->addIndexColumn()
                            ->make(true);
                }

                return view('payment.index');
            }
        /** index */

        /** import-view */
            public function file_import(){
                return view('payment.import');
            }
        /** import-view */

        /** import */
            public function import(PaymentRequest $request){
                DB::table('payments')->truncate();
                DB::statement("ALTER TABLE payments AUTO_INCREMENT = 1");

                Excel::import(new PaymentImport, $request->file('file'));

                return redirect()->route('payment');
            }
        /** import */
    }