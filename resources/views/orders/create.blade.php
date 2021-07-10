@extends('layout.app')

@section('meta')
@endsection

@section('title')
    Create Order
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
                        <div class="ibox-title">Create Order</div>
                        <h1 class="pull-right">
                            <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#customerModal">New Customer</button>
                        </h1>
                    </div>
                    <div class="ibox-body">
                        <form name="form" action="{{ route('orders.insert') }}" id="form" method="post" enctype="multipart/form-data">
                            @csrf
                            
                            <div class="row">
                                <div class="form-group col-sm-6">
                                    <label for="name">Name <span class="text-danger">*</span></label>
                                    <select name="name" id="name" class="form-control select2_demo_2" placeholder="Plese enter name">
                                        <option></option>
                                        @if(isset($customers) && $customers->isNotEmpty())
                                            @foreach($customers as $row)
                                                <option value="{{ $row->party_name }}" @if(isset($customer_id) && $customer_id == $row->id) selected @endif 
                                                    data-billing_name="{{ $row->billing_name }}" data-contact_person="{{ $row->contact_person }}" 
                                                    data-mobile_number="{{ $row->mobile_number }}" data-office_contact_person="{{ $row->office_contact_person }}" 
                                                    data-billing_address="{{ $row->billing_address }}" data-delivery_address="{{ $row->delivery_address }}"
                                                >
                                                    {{ $row->party_name }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
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
                                <div id="customer_detail" class="col-sm-12 mt-2 mb-4" style="display:none" >
                                    <div class="row">
                                        <div class="col-sm-4">Billing Name</div>
                                        <div class="col-sm-4">Contact Person</div>
                                        <div class="col-sm-4">Mobile Number</div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-4" id="billing_name"></div>
                                        <div class="col-sm-4" id="contact_person"></div>
                                        <div class="col-sm-4" id="mobile_number"></div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-4">Office Contact Person</div>
                                        <div class="col-sm-4">Billing Address</div>
                                        <div class="col-sm-4">Delivery Name</div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-4" id="office_contact_person"></div>
                                        <div class="col-sm-4" id="billing_address"></div>
                                        <div class="col-sm-4" id="delivery_address"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="row" id="table">
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
                                            <tr class="clone" id="clone_1">
                                                <th style="width:10%">1</th>
                                                <th style="width:30%">
                                                    <select class="form-control select2_demo_2" name="product_id[]" id="product_1">
                                                        @if(isset($products) && $products->isNotEmpty())
                                                            <option value="">Select Product</option>
                                                            @foreach($products as $row)
                                                                <option value="{{ $row->id }}">{{ $row->name }}</option>
                                                            @endforeach
                                                        @endif
                                                    </select>
                                                </th>
                                                <th style="width:25%">
                                                    <input type="text" name="quantity[]" id="quantity_1" class="form-control digit" required>
                                                </th>
                                                <th style="width:25%">
                                                    <input type="text" name="price[]" id="price_1" class="form-control digit" required>
                                                </th>
                                                <th style="width:10%">
                                                    <button type="button" class="btn btn-danger delete" style="display:none;" data-id="1">Remove</button>
                                                </th>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="col-sm-2 ml-auto">
                                    <button type="button" class="btn btn-md btn-primary m-4" id="add_product">Add Product</button>
                                </div> 
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

    <div class="modal fade" id="customerModal" tabindex="-1" role="dialog" aria-labelledby="customerModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="customerModalLabel">New Customer</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form name="customerform" action="{{ route('customers.insert.ajax') }}" id="customerform" method="post" enctype="multipart/form-data">
                    <div class="modal-body">
                        @csrf
                        <input type="hidden" name="order" value="order">
                        
                        <div class="row">
                            <div class="form-group col-sm-6">
                                <label for="party_name">Party Name <span class="text-danger">*</span></label>
                                <input type="text" name="party_name" id="party_name" class="form-control" placeholder="Plese enter party name" />
                                <span class="kt-form__help error party_name"></span>
                            </div>
                            <div class="form-group col-sm-6">
                                <label for="billing_name">Billing Name <span class="text-danger"></span></label>
                                <input type="text" name="billing_name" id="billing_name" class="form-control" placeholder="Plese enter billing name" />
                                <span class="kt-form__help error billing_name"></span>
                            </div>
                            <div class="form-group col-sm-6">
                                <label for="contact_person">Contact person <span class="text-danger"></span></label>
                                <input type="text" name="contact_person" id="contact_person" class="form-control" placeholder="Plese enter contact person" />
                                <span class="kt-form__help error contact_person"></span>
                            </div>
                            <div class="form-group col-sm-6">
                                <label for="mobile_number">Mobile number <span class="text-danger"></span></label>
                                <input type="text" name="mobile_number" id="mobile_number" class="form-control digits" placeholder="Plese enter mobile number" />
                                <span class="kt-form__help error mobile_number"></span>
                            </div>
                            <div class="form-group col-sm-6">
                                <label for="billing_address">Billing address <span class="text-danger"></span></label>
                                <textarea name="billing_address" id="billing_address" cols="3" rows="5" class="form-control" placeholder="Plese enter billing address"></textarea>
                                <span class="kt-form__help error billing_address"></span>
                            </div>
                            <div class="form-group col-sm-6">
                                <label for="delivery_address">Delivery address <span class="text-danger"></span></label>
                                <textarea name="delivery_address" id="delivery_address" cols="3" rows="5" class="form-control" placeholder="Plese enter delivery address"></textarea>
                                <span class="kt-form__help error delivery_address"></span>
                            </div>
                            <div class="form-group col-sm-6">
                                <label for="electrician">Electrician <span class="text-danger"></span></label>
                                <input type="text" name="electrician" id="electrician" class="form-control" placeholder="Plese enter electrician" />
                                <span class="kt-form__help error electrician"></span>
                            </div>
                            <div class="form-group col-sm-6">
                                <label for="electrician_number">Electrician number <span class="text-danger"></span></label>
                                <input type="text" name="electrician_number" id="electrician_number" class="form-control digits" placeholder="Plese enter electrician number" />
                                <span class="kt-form__help error electrician_number"></span>
                            </div>
                            <div class="form-group col-sm-6">
                                <label for="architect">Architect <span class="text-danger"></span></label>
                                <input type="text" name="architect" id="architect" class="form-control" placeholder="Plese enter architect" />
                                <span class="kt-form__help error architect"></span>
                            </div>
                            <div class="form-group col-sm-6">
                                <label for="architect_number">Architect number <span class="text-danger"></span></label>
                                <input type="text" name="architect_number" id="architect_number" class="form-control digits" placeholder="Plese enter architect number" />
                                <span class="kt-form__help error architect_number"></span>
                            </div>
                            <div class="form-group col-sm-6">
                                <label for="office_contact_person">Office contact person <span class="text-danger"></span></label>
                                <input type="text" name="office_contact_person" id="office_contact_person" class="form-control" placeholder="Plese enter office contact person" />
                                <span class="kt-form__help error office_contact_person"></span>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
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
                }else{
                    var clone = clone_div(1);
                    $("#table tbody").append(clone);
                    $("#product_"+num).select2();
                    $("#product_"+num).focus();
                }
            });

            function clone_div(id){
                return '<tr class="clone" id="clone_'+id+'">'+
                        '<th style="width:10%">'+id+'</th>'+
                        '<th style="width:30%">'+
                            '<select name="product_id[]" id="product_'+id+'" class="form-control select2_demo_2"> @foreach($products as $row)<option value="{{ $row->id }}">{{ $row->name }}</option>@endforeach </select>'+
                        '</th>'+
                        '<th style="width:25%">'+
                            '<input type="text" name="quantity[]" id="quantity_'+id+'"class="form-control" required>'+
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

                let con = confirm('Are you sure to delete?');
                if (con) {
                    $('#clone_'+id).remove();
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

            $(document).ready(function () {
                // $('#name').change('select2:select', function (e) {
                //     console.log(e.params.element);
                // });
                
            });
        });
    </script>
@endsection

