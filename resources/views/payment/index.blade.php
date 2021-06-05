@extends('layout.app')

@section('meta')
@endsection

@section('title')
    Payments
@endsection

@section('styles')
@endsection

@section('content')
    <div class="page-content fade-in-up">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox">
                    <div class="ibox-head">
                        <h1 class="ibox-title">Payments</h1>
                        <h1 class="pull-right">
                            <a class="btn btn-primary pull-right ml-2" style="margin-top: 8px;margin-bottom: 5px" href="{{ route('payment.import.file') }}">Import Payment File</a>
                        </h1>
                    </div>

                    <div class="dataTables_wrapper container-fluid dt-bootstrap4">
                        <table class="table table-bordered data-table" id="data-table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Party Name</th>
                                    <th>Bill No</th>
                                    <th>Bill Date</th>
                                    <th>Due Days</th>
                                    <th>Bill Amount</th>
                                    <th>Balance Amount</th>
                                    <th>Mobile No</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                    <div class="text-center"></div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script type="text/javascript">

        var datatable;

        $(document).ready(function() {
            if($('#data-table').length > 0){
                datatable = $('#data-table').DataTable({
                    processing: true,
                    serverSide: true,

                    // "pageLength": 10,
                    // "iDisplayLength": 10,
                    "responsive": true,
                    "aaSorting": [],
                    // "order": [], //Initial no order.
                    //     "aLengthMenu": [
                    //     [5, 10, 25, 50, 100, -1],
                    //     [5, 10, 25, 50, 100, "All"]
                    // ],

                    // "scrollX": true,
                    // "scrollY": '',
                    // "scrollCollapse": false,
                    // scrollCollapse: true,

                    // lengthChange: false,

                    "ajax":{
                        "url": "{{ route('payment') }}",
                        "type": "POST",
                        "dataType": "json",
                        "data":{
                            _token: "{{csrf_token()}}"
                        }
                    },
                    "columnDefs": [{
                            //"targets": [0, 5], //first column / numbering column
                            "orderable": true, //set not orderable
                        },
                    ],
                    columns: [
                        {
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex'
                        },
                        {
                            data: 'party_name',
                            name: 'party_name'
                        },
                        {
                            data: 'bill_no',
                            name: 'bill_no'
                        },
                        {
                            data: 'bill_date',
                            name: 'bill_date'
                        },
                        {
                            data: 'due_days',
                            name: 'due_days'
                        },
                        {
                            data: 'bill_amount',
                            name: 'bill_amount'
                        },
                        {
                            data: 'balance_amount',
                            name: 'balance_amount'
                        },
                        {
                            data: 'mobile_no',
                            name: 'mobile_no',
                        },
                    ]
                });
            }
        });
    </script>
@endsection
