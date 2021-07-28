@extends('layout.app')

@section('meta')
@endsection

@section('title')
    Create Purchase Order
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
                        <div class="ibox-title">Create Purchase Order</div>
                    </div>
                    <div class="ibox-body">
                        <form name="form" action="{{ route('purchase_orders.insert') }}" id="form" method="post" enctype="multipart/form-data">
                            @csrf
                            
                            <div class="row">
                                <div class="form-group col-sm-6">
                                    <label for="name">Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" id="name" class="form-control" placeholder="Plese enter company name">
                                    <span class="kt-form__help error name"></span>
                                </div>
                                <div class="form-group col-sm-6">
                                    <label for="order_date">Order Date <span class="text-danger"></span></label>
                                    <div class="input-group">
                                        <div class="input-group-addon"><i class="fa fa-clock-o"></i></div>
                                        <input type="text" name="order_date" id="order_date" class="form-control" placeholder="Plese enter order date" value="{{ date('d-m-Y') }}" />
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
                                                <th style="width:48%">Product</th>
                                                <th style="width:15%">Quantity</th>
                                                <th style="width:15%">Price</th>
                                                <th style="width:17%">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr class="clone" id="clone_1">
                                                <th style="width:05%">1</th>
                                                <th style="width:48%">
                                                    <select class="form-control select2_demo_2 product_id" name="product_id[]" id="product_1" data-id="1">
                                                        @if(isset($products) && $products->isNotEmpty())
                                                            <option value="">Select Product</option>
                                                            @foreach($products as $row)
                                                                <option value="{{ $row->id }}">{{ $row->name }}</option>
                                                            @endforeach
                                                        @endif
                                                    </select>
                                                </th>
                                                <th style="width:15%">
                                                    <input type="text" name="quantity[]" id="quantity_1" class="form-control digit">
                                                </th>
                                                <th style="width:15%">
                                                    <input type="text" name="price[]" id="price_1" class="form-control digit">
                                                </th>
                                                <th style="width:17%">
                                                    <button type="button" class="btn btn-danger delete" style="display:none;" data-id="1">Remove</button>
                                                    <button type="button" class="btn btn-primary ml-2" data-toggle="modal" data-target="#detail_1">
                                                        <i class="fa fa-info"></i>
                                                    </button>
                                                    <div class="modal fade" id="detail_1" tabindex="-1" role="dialog" aria-labelledby="detail_1_Label" aria-hidden="true">
                                                        <div class="modal-dialog" role="document">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    Product Quantity Detail
                                                                </div>
                                                                <div class="modal-body">
                                                                    <div class="row" id="product_detail_1">
                                                                        <div class="col-sm-12">
                                                                            <h5 class="text-center">Please select product.</h5>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </th>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="col-sm-2 ml-auto">
                                    <button type="button" class="btn btn-md btn-primary m-4" id="add_product">Add Product</button>
                                </div> 
                            </div>
                            <div class="form-group col-sm-12">
                                <label for="remark">Remark <span class="text-danger"></span></label>
                                <textarea name="remark" id="remark" cols="30" rows="3" class="form-control" placeholder="Plese enter remark"></textarea>
                                <span class="kt-form__help error remark"></span>
                            </div>
                            <div class="form-group col-sm-12">
                                <label for="file">Attechment <span class="text-danger"></span></label>
                                <input type="file" name="file" id="file" class="form-control dropify" placeholder="Plese select attachment" />
                                <span class="kt-form__help error file"></span>
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">Submit</button>
                                <a href="{{ route('purchase_orders') }}" class="btn btn-default">Back</a>
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
                date: new Date(),
                autoclose: true
            });

            let base_product = '';
            let base_product_id = '';
            let base_quantity = '';
            let base_price = '';

            $('#add_product').click(function(){                
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
                    $("#product_"+num).select2();
                    $("#product_"+num).focus();
                }
            });

            function clone_div(id){
                return '<tr class="clone" id="clone_'+id+'">'+
                        '<th style="width:05%">'+id+'</th>'+
                        '<th style="width:48%">'+
                            '<select name="product_id[]" id="product_'+id+'" data-id="'+id+'" class="form-control select2_demo_2 product_id"> <option value="">Select</option> @foreach($products as $row)<option value="{{ $row->id }}">{{ $row->name }}</option>@endforeach </select>'+
                        '</th>'+
                        '<th style="width:15%">'+
                            '<input type="text" name="quantity[]" id="quantity_'+id+'"class="form-control">'+
                        '</th>'+
                        '<th style="width:15%">'+
                            '<input type="text" name="price[]" id="price_'+id+'" class="form-control">'+
                        '</th>'+
                        '<th style="width:17%">'+
                            '<button type="button" class="btn btn-danger delete" data-id="'+id+'">Remove</button>'+
                            '<button type="button" class="btn btn-primary ml-2" data-toggle="modal" id="#detail_model_'+id+'" data-target="#detail_'+id+'">'+
                                '<i class="fa fa-info"></i>'+
                            '</button>'+
                            '<div class="modal fade" id="detail_'+id+'" tabindex="-1" role="dialog" aria-labelledby="detail_'+id+'_Label" aria-hidden="true">'+
                                '<div class="modal-dialog" role="document">'+
                                    '<div class="modal-content">'+
                                        '<div class="modal-header">'+
                                            'Product Quantity Detail'+
                                        '</div>'+
                                        '<div class="modal-body">'+
                                            '<div class="row" id="product_detail_'+id+'">'+
                                                '<div class="col-sm-12">'+
                                                    '<h5 class="text-center">Please select product.</h5>'+
                                                '</div>'+
                                            '</div>'+
                                        '</div>'+
                                        '<div class="modal-footer">'+
                                            '<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>'+
                                        '</div>'+
                                    '</div>'+
                                '</div>'+
                            '</div>'+
                        '</th>'+
                    '</tr>';
            }

            $(document).on('click', ".delete", function () {
                let id = $(this).data('id');

                let con = confirm('Are you sure to delete?');
                if (con) {
                    $('#clone_'+id).remove();
                }
            });
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

        $(document).ready(function () {
            var form = $('#customerform');
            $('.kt-form__help').html('');
            form.submit(function(e) {
                e.preventDefault();
                $('.help-block').html('');
                $('.m-form__help').html('');
                $.ajax({
                    url : form.attr('action'),
                    type : form.attr('method'),
                    data : form.serialize(),
                    dataType: 'json',
                    async:false,
                    success : function(json){
                        if(json.code == 200){
                            toastr.success(success, 'Customer added successfully');
                            setTimeout(function(){ location.reload(); }, 3000);
                        } else {
                            toastr.success(success, 'Something went wrong, please try again later');
                        }
                    },
                    error: function(json){
                        if(json.status === 422) {
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
        
        $(document).on('change', ".product_id", function () {
            var id = $(this).val();
            var div_id = $(this).data('id');

            if(id != '' || id != null){
                _detail(id, div_id);
            }
        });

        function _detail(id, div_id){
            $.ajax({
                url : "{{ route('purchase_orders.product.detail') }}",
                type : 'post',
                data : { "_token": "{{ csrf_token() }}", "id": id},
                dataType: 'json',
                async: false,
                success : function(response){
                    if(response.code == 200){
                        let quantity = parseInt(response.data.quantity);
                        let required_quantity = parseInt(response.data.required_quantity);

                        let max = (quantity > required_quantity) ? quantity - required_quantity  : required_quantity - quantity;

                        let div =   '<div class="col-sm-6">Available Quantity</div>'+
                                    '<div class="col-sm-6">'+quantity+'</div>'+
                                    '<div class="col-sm-6">Orders Quantity</div>'+
                                    '<div class="col-sm-6">'+required_quantity+'</div>'+
                                    '<div class="col-sm-6">Required Quantity</div>'+
                                    '<div class="col-sm-6">'+max+'</div>';
                                    
                        $('#product_detail_'+div_id).html(div);
                    }
                }
            });
        }
    </script>
@endsection

