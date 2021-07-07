@extends('layout.app')

@section('meta')
@endsection

@section('title')
    Edit Order
@endsection

@section('styles')
    <link href="{{ asset('assets/vendors/select2/dist/css/select2.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/vendors/bootstrap-datepicker/dist/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet" />
    <style>
        .select2-container--default .select2-selection--single{
            height: 35px;
        }
    </style>
@endsection

@section('content')
    <div class="page-content fade-in-up">
        <div class="row">
            <div class="col-md-12">
                <div class="ibox">
                    <div class="ibox-head">
                        <div class="ibox-title">Edit Order</div>
                    </div>
                    <div class="ibox-body">
                        <form name="form" action="{{ route('orders.update') }}" id="form" method="post" enctype="multipart/form-data">
                            @csrf
                            @method('PATCH')

                            <input type="hidden" name="id" value="{{ $data->id }}">
                            
                            <div class="row">
                                <div class="form-group col-sm-6">
                                    <label for="name">Name <span class="text-danger">*</span></label>
                                    <select name="name" id="name" class="form-control select2_demo_2" placeholder="Plese enter name">
                                        <option></option>
                                        @if(isset($customers) && $customers->isNotEmpty())
                                            @foreach($customers as $row)
                                                <option value="{{ $row->party_name }}" @if(isset($data) && $data->name != '' && $data->name == $row->party_name) selected @endif>{{ $row->party_name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                    <span class="kt-form__help error name"></span>
                                </div>
                                <div class="form-group col-sm-6">
                                    <label for="order_date">Order Date <span class="text-danger"></span></label>
                                    <div class="input-group input-group-sm">
                                        <div class="input-group-addon"><i class="fa fa-envelope"></i></div>
                                        <input type="text" name="order_date" id="order_date" class="form-control" placeholder="Plese enter order date" value="{{ date('d-m-Y' ,strtotime($data->order_date)) }}" />
                                    </div>
                                        <i class="fa fa-calender"></i>
                                    <span class="kt-form__help error order_date"></span>
                                </div>
                            </div>
                    
                            <div class="row" id="table" >
                                <div class="col-sm-12">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th style="width:10%">Sr. No</th>
                                                <th style="width:30%">Product</th>
                                                <th style="width:25%">Quantity</th>
                                                <th style="width:25%">Price</th>
                                                <th style="width:10%">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if(isset($data->order_details) && $data->order_details->isNotEmpty())
                                                @php $i=1; @endphp
                                                @foreach($data->order_details as $row)
                                                    <tr class="clone" id="clone_{{ $i }}">
                                                        <th style="width:10%">{{ $i }}</th>
                                                        <th style="width:30%">{{ $row->product_name }}
                                                            <input type="hidden" name="product_id[]" id="product_{{ $i }}" value="{{ $row->product_id }}">
                                                        </th>
                                                        <th style="width:25%">
                                                            <input type="text" name="quantity[]" id="quantity_{{ $i }}" value="{{ $row->quantity }}" class="form-control digit" required>
                                                        </th>
                                                        <th style="width:25%">
                                                            <input type="text" name="price[]" id="price_{{ $i }}" value="{{ $row->price }}" class="form-control digit" required>
                                                        </th>
                                                        <th style="width:10%">
                                                            <button type="button" class="btn btn-danger delete" data-detail="{{ $row->id }}" data-id="{{ $i }}">Remove</button>
                                                        </th>
                                                    </tr>
                                                    @php $i++; @endphp
                                                @endforeach
                                            @endif
                                        </tbody>
                                    </table>
                                    <div class="form-group col-sm-2 pull-right">
                                            <button type="button" class="btn btn-md btn-primary mt-4" id="add_product">Add Product</button>
                                    </div>
                                </div> 
                            </div>
                            
                            <div class="form-group"></div>
                            <div class="form-group"></div>

                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">Submit</button>
                                <a href="{{ route('orders') }}" class="btn btn-default">Back</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('assets/vendors/select2/dist/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('assets/js/scripts/form-plugins.js') }}"></script>
    <script src="{{ asset('assets/vendors/bootstrap-datepicker/dist/js/bootstrap-datepicker.js') }}"></script>
    
    <script>
        $(document).ready(function() {
            var date = new Date();
            var today = new Date(date.getFullYear(), date.getMonth(), date.getDate());
            $('#order_date').datepicker({
                format: 'dd-mm-yyyy',
                autoclose: true
            });

            let base_product = '';
            let base_product_id = '';
            let base_quantity = '';
            let base_price = '';

            $('#add_product').click(function(){
                $('#table').css('display', 'block');

                base_product = $('#base_product option:selected').text();
                base_product_id = $('#base_product').val();
                base_quantity = $('#base_quantity').val();
                base_price = $('#base_price').val();

                $('#base_product').val('');
                $('#base_quantity').val('');
                $('#base_price').val('');

                var regex = /^(.+?)(\d+)$/i;
                var cloneIndex = $("#table tbody tr").length;

                if(cloneIndex !== 0){
                    let num = parseInt(cloneIndex) + 1;

                    var clone = clone_div(num);
                    $("#table tbody").append(clone);
                    $("#product_"+num).select2();
                    $("#product_"+num).focus();
                }else{
                    var clone = clone_div(1);
                    $("#table tbody").append(clone);
                }
            });

            function clone_div(id){
                return '<tr class="clone" id="clone_'+id+'">'+
                        '<th style="width:10%">'+id+'</th>'+
                        '<th style="width:30%">'+
                            '<select name="product_id[]" id="product_'+id+'" class="form-control select2_demo_2"> @foreach($products as $row)<option value="{{ $row->id }}">{{ $row->name }}</option>@endforeach </select>'+
                        '</th>'+
                        '<th style="width:25%">'+
                            '<input type="text" name="quantity[]" id="quantity_'+id+'" class="form-control " required>'+
                        '</th>'+
                        '<th style="width:25%">'+
                            '<input type="text" name="price[]" id="price_'+id+'" class="form-control " required>'+
                        '</th>'+
                        '<th style="width:10%">'+
                            '<button type="button" class="btn btn-danger delete" data-id="'+id+'">Remove</button>'+
                        '</th>'+
                    '</tr>';
            }

            $(document).on('click', ".delete", function () {
                let id = $(this).data('id');
                let detail_id = $(this).data('detail');

                let con = confirm('Are you sure to delete?');
                if (con) {
                    if(detail_id != null){
                        $.ajax({
                            "url": "{!! route('orders.delete.detail') !!}",
                            "dataType": "json",
                            "type": "POST",
                            "data":{
                                id: detail_id,
                                _token: "{{ csrf_token() }}"
                            },
                            success: function (response){
                                if (response.code == 200){
                                    $('#clone_'+id).remove();
                                    toastr.success('Record deleted changed successfully.', 'Success');
                                }else{
                                    toastr.error('Failed to delete record.', 'Error');
                                }
                            }
                        });
                    } else {
                        $('#clone_'+id).remove();
                        toastr.success('Record deleted changed successfully.', 'Success');
                    }
                }
            })
        });
    </script>

    <script>
        $(document).ready(function () {
            var form = $('#form');
            $('.kt-form__help').html('');
            form.submit(function(e) {
                $('.help-block').html('');
                $('.m-form__help').html('');
                $.ajax({
                    url : form.attr('action'),
                    type : form.attr('method'),
                    data : form.serialize(),
                    dataType: 'json',
                    async:false,
                    success : function(json){
                        return true;
                    },
                    error: function(json){
                        if(json.status === 422) {
                            e.preventDefault();
                            var errors_ = json.responseJSON;
                            $('.kt-form__help').html('');
                            $.each(errors_.errors, function (key, value) {
                                $('.'+key).html(value);
                            });
                        }
                    }
                });
            });
        });
    </script>
@endsection

