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
                            <div class="col-sm-3">
                                <select name="type" id="type" class="form-control">
                                    <option value="">Select Type</option>
                                    <option value="assigned">Assigned</option>
                                    <option value="not_assigned">Not Assigned</option>
                                </select>
                            </div>
                            <div class="col-sm-3">
                                <input type="date" name="start_date" id="start_date" class="form-control date">
                            </div>
                            <div class="col-sm-3">
                                <input type="date" name="end_date" id="end_date" class="form-control date">
                            </div>
                            <div class="col-sm-3">
                                <button type="button" name="reset" id="reset" class="form-control btn btn-primary">Reset</button>
                            </div>
                        </div>

                        <div class="dataTables_wrapper container-fluid dt-bootstrap4">
                            <table class="table table-bordered data-table" id="data-table">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Party Name</th>
                                        <th>Amount</th>
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
                            data: 'balance_amount',
                            name: 'balance_amount'
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

        $('#type').change(function(){
            $('#data-table').DataTable().draw(true);            
        });

        $('#reset').click(function(){
            $('#type').val('');
            $('#start_date').val('');
            $('#end_date').val('');
            $('#data-table').DataTable().draw(true);            
        });

        $('.date').change(function(){
            let startDate = $('#start_date').val();
            let endDate = $('#end_date').val();

            if(startDate && endDate){
                $('#data-table').DataTable().draw(true);            
            }
        });

        $(document).on("submit", ".form", function(e){
            e.preventDefault();
            $('.error').html('');

            let id = $(this).attr('id');

            let assign_id = $('#assign_id').val();
            let party_name = $('#party_name'+id).val();
            let date = $('#date'+id).val();
            let user = $('#user'+id+' option').filter(':selected').val();
            let note = $('#note'+id).val();

            $.ajax({
                "url": "{!! route('payment.assign') !!}",
                "dataType": "json",
                "type": "POST",
                "data":{
                    assign_id: assign_id,
                    party_name: party_name,
                    date: date,
                    user: user,
                    note: note,
                    _token: "{{ csrf_token() }}"
                },
                success: function (response){
                    if (response.code == 200){
                        $('#date'+id).val('');
                        $('#note'+id).val('');
                        $('#user'+id+' option').filter(':selected').prop('selected', false);
                        $('#assignModal'+id).modal('hide');
                        toastr.success(response.message, 'Success');
                        $('#data-table').DataTable().draw(true);
                    }else{
                        toastr.error(response.message, 'Error');
                    }
                },
                error: function(response){
                    if(response.status === 422) {
                        var errors_ = response.responseJSON;
                        $.each(errors_, function (key, value) {
                            toastr.error(value, 'Error');
                        });
                    }
                }

            });

        });
    </script>
@endsection
