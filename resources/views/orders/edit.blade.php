@extends('layout.app')

@section('meta')
@endsection

@section('title')
    Edit Order
@endsection

@section('styles')
    <link href="{{ asset('assets/vendors/select2/dist/css/select2.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/vendors/bootstrap-datepicker/dist/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet" />

    <link href="{{ asset('assets/css/dropify.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/sweetalert2.bundle.css') }}" rel="stylesheet">

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
                                    <div class="input-group">
                                        <div class="input-group-addon"><i class="fa fa-clock-o"></i></div>
                                        <input type="text" name="order_date" id="order_date" class="form-control" placeholder="Plese enter order date" value="{{ date('d-m-Y' ,strtotime($data->order_date)) }}" />
                                    </div>
                                        <i class="fa fa-calender"></i>
                                    <span class="kt-form__help error order_date"></span>
                                </div>
                                <div class="row" id="customer_details"></div>
                            </div>
                            <div class="row" id="table">
                                <div class="col-sm-12">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th style="width:05%">Sr. No</th>
                                                <th style="width:50%">Product</th>
                                                <th style="width:10%">Quantity</th>
                                                <th style="width:10%">Price</th>
                                                <th style="width:10%">Remark</th>
                                                <th style="width:15%">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if(!empty($data) && $data->order_details->isNotEmpty())
                                                @php $i=1; @endphp
                                                @foreach($data->order_details as $product)
                                                    <tr class="clone" id="clone_{{ $i }}">
                                                        <th style="width:05%">1</th>
                                                        <th style="width:50%">
                                                            <select class="form-control select2_demo_2 product_id" name="product_id[]" id="product_{{ $i }}" data-id="{{ $i }}">
                                                                @if(isset($products) && $products->isNotEmpty())
                                                                    <option value="">Select Product</option>
                                                                    @foreach($products as $row)
                                                                        <option value="{{ $row->id }}" @if($product->product_id == $row->id) selected @endif >{{ $row->name }}</option>
                                                                    @endforeach
                                                                @endif
                                                            </select>
                                                        </th>
                                                        <th style="width:10%">
                                                            <input type="text" name="quantity[]" id="quantity_{{ $i }}" value="{{ $product->quantity }}" class="form-control digit">
                                                        </th>
                                                        <th style="width:10%">
                                                            <input type="text" name="price[]" id="price_{{ $i }}" value="{{ $product->price }}" class="form-control digit">
                                                        </th>
                                                        <th style="width:10%">
                                                            <textarea name="remarks[]" id="remarks_{{ $i }}" cols="1" rows="1" class="form-control">{{ $product->remark ?? '' }}</textarea>
                                                        </th>
                                                        <th style="width:15%">
                                                            <button type="button" class="btn btn-danger delete" data-detail="{{ $product->id }}" data-id="{{ $i }}">Remove</button>
                                                        </th>
                                                    </tr>
                                                    @php $i++; @endphp
                                                @endforeach
                                            @else
                                                <tr class="clone" id="clone_1">
                                                    <th style="width:05%">1</th>
                                                    <th style="width:50%">
                                                        <select class="form-control select2_demo_2 product_id" name="product_id[]" id="product_1" data-id="1">
                                                            @if(isset($products) && $products->isNotEmpty())
                                                                <option value="">Select Product</option>
                                                                @foreach($products as $row)
                                                                    <option value="{{ $row->id }}">{{ $row->name }}</option>
                                                                @endforeach
                                                            @endif
                                                        </select>
                                                    </th>
                                                    <th style="width:10%">
                                                        <input type="text" name="quantity[]" id="quantity_1" class="form-control digit">
                                                    </th>
                                                    <th style="width:10%">
                                                        <input type="text" name="price[]" id="price_1" class="form-control digit">
                                                    </th>
                                                    <th style="width:10%">
                                                        <textarea name="remarks[]" id="remarks_1" cols="1" rows="1" class="form-control"></textarea>
                                                    </th>
                                                    <th style="width:15%">
                                                        <button type="button" class="btn btn-danger delete" style="display:none;" data-id="1">Remove</button>
                                                    </th>
                                                </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div> 
                                <div class="col-sm-2 ml-auto">
                                    <button type="button" class="btn btn-md btn-primary m-4" id="add_product">Add Product</button>
                                </div>
                            </div>
                            <div class="form-group col-sm-12">
                                <label for="remark">Remark <span class="text-danger"></span></label>
                                <textarea name="remark" id="remark" cols="30" rows="5" class="form-control" placeholder="Plese enter remark">{{ $data->remark ?? '' }}</textarea>
                                <span class="kt-form__help error remark"></span>
                            </div>
                            <div class="form-group col-sm-12">
                                @if(isset($data->file) && !empty($data->file))
                                    @php $file = url('/uploads/orders/').'/'.$data->file; @endphp
                                @else
                                    @php $file = ''; @endphp
                                @endif
                                <label for="file">Attechment <span class="text-danger"></span></label>
                                <input type="file" name="file" id="file" class="form-control dropify" placeholder="Plese select attachment" data-default-file="{{ $file }}" data-show-remove="false" />
                                <span class="kt-form__help error file"></span>
                            </div>
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

    <script src="{{ asset('assets/js/dropify.min.js') }}"></script>
    <script src="{{ asset('assets/js/promise.min.js') }}"></script>
    <script src="{{ asset('assets/js/sweetalert2.bundle.js') }}"></script>
    
    <script>
        $(document).ready(function() {
            $('.dropify').dropify({
                messages: {
                    'default': 'Drag and drop file here or click',
                    'remove':  'Remove',
                    'error':   'Ooops, something wrong happended.'
                }
            });
            var drEvent = $('.dropify').dropify(); 

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
                    $("#product_"+num).select2('open');
                }else{
                    var clone = clone_div(1);
                    $("#table tbody").append(clone);
                }
            });

            function clone_div(id){
                return '<tr class="clone" id="clone_'+id+'">'+
                        '<th style="width:05%">'+id+'</th>'+
                        '<th style="width:50%">'+
                            '<select name="product_id[]" id="product_'+id+'" data-id="'+id+'" class="form-control select2_demo_2 product_id"> <option value="">Select</option> @foreach($products as $row)<option value="{{ $row->id }}">{{ $row->name }}</option>@endforeach </select>'+
                        '</th>'+
                        '<th style="width:10%">'+
                            '<input type="text" name="quantity[]" id="quantity_'+id+'"class="form-control">'+
                        '</th>'+
                        '<th style="width:10%">'+
                            '<input type="text" name="price[]" id="price_'+id+'" class="form-control">'+
                        '</th>'+
                        '<th style="width:10%">'+
                            '<textarea name="remarks[]" id="remarks_'+id+'" cols="1" rows="1" class="form-control"></textarea>'+
                        '</th>'+
                        '<th style="width:15%">'+
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
                    success : function(response){
                        return true;
                    },
                    error: function(response){
                        if(response.status === 422) {
                            e.preventDefault();
                            var errors_ = response.responseJSON;
                            $('.kt-form__help').html('');
                            $.each(errors_.errors, function (key, value) {
                                $('.'+key).html(value);
                            });
                        }
                    }
                });
            });
        });

        $(document).ready(function () {
            $('#name').change(function () {
                var name = $(this).val();
                if(name != '' || name != null){
                    $("#customer_details").html('');
                    _customer_details(name);
                }
            });

            let exst_name = "{{ $data->name ?? '' }}";
            console.log(exst_name);
            if(exst_name != '' || exst_name != null){
                $("#customer_details").html('');
                _customer_details(exst_name);
            }

            $(document).on('change', ".product_id", function () {
                var id = $(this).val();
                var div_id = $(this).data('id');
                
                if(id != '' || id != null){
                    _product_price(id, div_id);
                }
            });
        });

        function _customer_details(name){
            $.ajax({
                url : "{{ route('orders.customer.details') }}",
                type : 'post',
                data : { "_token": "{{ csrf_token() }}", "name": name},
                dataType: 'json',
                async: false,
                success : function(response){
                    $("#customer_details").append(
                        '<div class="form-group col-md-6"><span style="font-weight: bold; padding-left:16px;">Name: </span><span>'+response.data.party_name+'</span></div>'+
                        '<div class="form-group col-md-6"><span style="font-weight: bold; padding-left:16px;">Billing Name: </span><span>'+response.data.billing_name+'</span></div>'+
                        '<div class="form-group col-md-6"><span style="font-weight: bold; padding-left:16px;">Contact Person: </span><span>'+response.data.contact_person+'</span></div>'+
                        '<div class="form-group col-md-6"><span style="font-weight: bold; padding-left:16px;">Mobile Number: </span><span>'+response.data.mobile_number+'</span></div>'+
                        '<div class="form-group col-md-6"><span style="font-weight: bold; padding-left:16px;">Billing Address: </span><span>'+response.data.billing_address+'</span></div>'+
                        '<div class="form-group col-md-6"><span style="font-weight: bold; padding-left:16px;">Delivery Address: </span><span>'+response.data.delivery_address+'</span></div>'+
                        '<div class="form-group col-md-6"><span style="font-weight: bold; padding-left:16px;">Electrician: </span><span>'+response.data.electrician+'</span></div>'+
                        '<div class="form-group col-md-6"><span style="font-weight: bold; padding-left:16px;">Electrician Number: </span><span>'+response.data.electrician_number+'</span></div>');
                }
            });
        }

        function _product_price(id, div_id){
            $.ajax({
                url : "{{ route('orders.product.price') }}",
                type : 'post',
                data : { "_token": "{{ csrf_token() }}", "id": id},
                dataType: 'json',
                async: false,
                success : function(response){
                    if(response.code == 200){
                        $('#price_'+div_id).val(response.data.price);
                    }
                }
            });
        }
    </script>
@endsection

