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
                    $type = $request->type;
                    $start_date = $request->start_date;
                    $end_date = $request->end_date;

                    $data = Payment::select('id', 'party_name', 'bill_date', 'balance_amount', 'mobile_no', DB::Raw("null as note"), DB::Raw("null as reminder"))
                                    ->whereRaw('id IN (select MAX(id) FROM payments GROUP BY party_name)')
                                    ->get();

                    return Datatables::of($data)
                            ->addIndexColumn()
                            ->addColumn('action', function($data){
                                $rec = Payment::select('bill_no', 'bill_date', 'bill_amount')->where(['party_name' => $data->party_name])->get();

                                $info = "<table class='table table-bordered'>
                                            <thead class='thead-default'>
                                                <tr>
                                                    <th>Sr. No</th>
                                                    <th>Bill No</th>
                                                    <th>Bill Date</th>
                                                    <th>Bill Amount</th>
                                                </tr>
                                            </thead>
                                            <tbody>";

                                if($rec->isNotEmpty()){
                                    $i=1;
                                    foreach($rec as $r){
                                        $info .= "<tr>
                                                    <td>$i</td>
                                                    <td>$r->bill_no</td>
                                                    <td>$r->bill_date</td>
                                                    <td>$r->bill_amount</td>
                                                </tr>";
                                        $i++;
                                    }
                                }

                                    $info .="</tbody></table>";

                                return '<div class="btn-group">
                                                <button type="button" class="btn btn-default btn-xs" data-toggle="modal" data-target="#infoModal'.$data->id.'">
                                                    <i class="fa fa-exclamation-circle"></i>
                                                </button> &nbsp;
                                                <button type="button" class="btn btn-default btn-xs" data-toggle="modal" data-target="#assignModal'.$data->id.'">
                                                    <i class="fa fa-legal"></i>
                                                </button> &nbsp;
                                            </div>
                                        <div class="modal fade" id="infoModal'.$data->id.'" tabindex="-1" role="dialog" aria-labelledby="infoModalLabel'.$data->id.'" aria-hidden="true">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="infoModalLabel'.$data->id.'">'.$data->party_name.'</h5>
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">'.$info.'</div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="modal fade" id="assignModal'.$data->id.'" tabindex="-1" role="dialog" aria-labelledby="assignModalLabel'.$data->id.'" aria-hidden="true">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="assignModalLabel'.$data->id.'">Assign</h5>
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        Assign
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>';
                            })
                            ->rawColumns(['action'])
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