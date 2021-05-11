@extends('layout.app')

@section('meta')
@endsection

@section('title')
    Products
@endsection

@section('styles')
@endsection

@section('content')
    <div class="page-content fade-in-up">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox">
                    <div class="ibox-head">
                        <h1 class="ibox-title">Products</h1>
                        <h1 class="pull-right">
                            <a class="btn btn-primary pull-right ml-2" style="margin-top: 8px;margin-bottom: 5px" href="{{ route('products.create') }}">Add New</a>
                        </h1>
                    </div>
                    <div class="dataTables_wrapper container-fluid dt-bootstrap4">
                        <table class="table table-bordered data-table" id="data-table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Name</th>
                                    <th>Quantity</th>
                                    <th>Unit</th>
                                    <th>Color</th>
                                    <th>Price</th>
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
                        "url": "{{ route('products') }}",
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
                            data: 'name',
                            name: 'name'
                        },
                        {
                            data: 'quantity',
                            name: 'quantity'
                        },
                        {
                            data: 'unit',
                            name: 'unit'
                        },
                        {
                            data: 'color',
                            name: 'color'
                        },
                        {
                            data: 'price',
                            name: 'price'
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
    </script>
@endsection
