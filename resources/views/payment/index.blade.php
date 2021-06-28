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
                            <a class="btn btn-primary pull-right ml-2" style="margin-top: 8px;margin-bottom: 5px" href="{{ route('payment.import.file') }}">Upload new data </a>
                        </h1>
                    </div>

                    <div class="ibox-body">
                        <div class="row mb-5 mt-2 mx-2">
                            <div class="col-sm-4">
                                <select name="type" id="type" class="form-control param">
                                    <option value="">Select Type</option>
                                    <option value="assigned">Assigned</option>
                                    <option value="not_assigned">Not Assigned</option>
                                </select>
                            </div>
                            <div class="col-sm-4">
                                <input type="date" name="start_date" id="start_date" class="form-control param">
                            </div>
                            <div class="col-sm-4">
                                <input type="date" name="end_date" id="end_date" class="form-control param">
                            </div>
                        </div>

                        <div class="dataTables_wrapper container-fluid dt-bootstrap4">
                            <table class="table table-bordered data-table" id="data-table">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Party Name</th>
                                        <th>Date</th>
                                        <th>Amount</th>
                                        <th>Mobile No</th>
                                        <th>Reminder</th>
                                        <th>Note</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                        <div class="text-center"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script type="text/javascript">
        $('.param').change(function(){
            $('#data-table').DataTable().draw(true);            
        });

        var datatable;

        $(document).ready(function() {
            if($('#data-table').length > 0){
                datatable = $('#data-table').DataTable({
                    processing: true,
                    serverSide: true,

                    "pageLength": 5,
                    // "iDisplayLength": 10,
                    "responsive": true,
                    "aaSorting": [],
                    "order": [], //Initial no order.
                        "aLengthMenu": [
                        [5, 10, 25, 50, 100, -1],
                        [5, 10, 25, 50, 100, "All"]
                    ],

                    // "scrollX": true,
                    // "scrollY": '',
                    // "scrollCollapse": false,
                    // scrollCollapse: true,

                    // lengthChange: false,

                    "ajax":{
                        "url": "{{ route('payment') }}",
                        "type": "POST",
                        "dataType": "json",
                        // "data":{
                        //     _token: "{{csrf_token()}}"
                        // },
                        data: function (d) {
                            d._token = "{{csrf_token()}}";
                            d.type = $('#type').val();
                            d.start_date = $('#start_date').val();
                            d.end_date = $('#end_date').val();
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
                            data: 'bill_date',
                            name: 'bill_date'
                        },
                        {
                            data: 'balance_amount',
                            name: 'balance_amount'
                        },
                        {
                            data: 'mobile_no',
                            name: 'mobile_no',
                        },
                        {
                            data: 'reminder',
                            name: 'reminder',
                        },
                        {
                            data: 'note',
                            name: 'note',
                        },
                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                        }
                    ]
                });
            }
        }); 
    </script>
@endsection
