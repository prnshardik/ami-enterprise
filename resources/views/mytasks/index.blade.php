@extends('layout.app')

@section('meta')
@endsection

@section('title')
    My Tasks
@endsection

@section('styles')
@endsection

@section('content')
    <div class="page-content fade-in-up">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox">
                    <div class="ibox-head">
                        <h1 class="ibox-title">My Tasks</h1>
                    </div>

                    <div class="ibox-body">
                        <div class="dataTables_wrapper container-fluid dt-bootstrap4">
                            <table class="table table-bordered data-table" id="data-table">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Allocated From</th>
                                        <th>Task</th>
                                        <th>Task Date</th>
                                        <th>Target Date</th>
                                        <th>Status</th>
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
                    serverSide: false,

                    "pageLength": 25,
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
                        "url": "{{ route('mytasks') }}",
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
                            data: 'allocate_from',
                            name: 'allocate_from'
                        },
                        {
                            data: 'type',
                            name: 'type'
                        },
                        {
                            data: 'task_date',
                            name: 'task_date'
                        },
                        {
                            data: 'target_date',
                            name: 'target_date'
                        },
                        {
                            data: 'status',
                            name: 'status'
                        },
                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                        },
                    ]
                });
            }
        });

        function change_status(object){
            var id = $(object).data("id");
            var status = $(object).data("status");
            var old_status = $(object).data("old_status");
            var msg = "Are you Sure?";

            if (confirm(msg)) {
                $.ajax({
                    "url": "{!! route('mytasks.change.status') !!}",
                    "dataType": "json",
                    "type": "POST",
                    "data":{
                        id: id,
                        status: status,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function (response){
                        if (response.code == 200){
                            datatable.ajax.reload();
                            toastr.success('Record status changed successfully.', 'Success');
                        }else{
                            toastr.error('Failed to delete record.', 'Error');
                        }
                    }
                });
            }
        }
    </script>
@endsection
