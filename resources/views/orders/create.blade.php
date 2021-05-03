@extends('layout.app')

@section('meta')
@endsection

@section('title')
    Create Order
@endsection

@section('styles')
@endsection

@section('content')
    <div class="page-content fade-in-up">
        <div class="row">
            <div class="col-md-12">
                <div class="ibox">
                    <div class="ibox-head">
                        <div class="ibox-title">Create Order</div>
                    </div>
                    <div class="ibox-body">
                        <form name="form" action="{{ route('orders.insert') }}" id="form" method="post" enctype="multipart/form-data">
                            @csrf
                            
                            <div class="row">
                                <div class="form-group col-sm-6">
                                    <label for="name">Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" id="name" class="form-control" placeholder="Plese enter name" />
                                    <span class="kt-form__help error name"></span>
                                </div>
                                <div class="form-group col-sm-6">
                                    <label for="order_date">Order Date <span class="text-danger">*</span></label>
                                    <input type="date" name="order_date" id="order_date" class="form-control" placeholder="Plese enter order date" min="{{ Date('Y-m-d') }}" />
                                    <span class="kt-form__help error order_date"></span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-sm-6">
                                    <label for="base_product">Product <span class="text-danger">*</span></label>
                                    <select name="base_product" id="base_product" class="form-control">
                                        <option value="" hidden>Select Product</option>
                                    </select>
                                    <span class="kt-form__help error product"></span>
                                </div>
                                <div class="form-group col-sm-2">
                                    <label for="base_quantity">Quantity <span class="text-danger">*</span></label>
                                    <input type="text" name="base_quantity" id="base_quantity" class="form-control" placeholder="Plese enter quantity" />
                                    <span class="kt-form__help error quantity"></span>
                                </div>
                                <div class="form-group col-sm-2">
                                    <label for="base_price">Price <span class="text-danger">*</span></label>
                                    <input type="text" name="base_price" id="base_price" class="form-control" placeholder="Plese enter price" />
                                    <span class="kt-form__help error price"></span>
                                </div>
                                <div class="form-group col-sm-2 d-flex align-items-center">
                                    <button class="btn btn-md btn-primary mt-4" id="add_product">Add Product</button>
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
@endsection

@section('scripts')
    <script>
        $( document ).ready(function() {
            console.log( "ready!" );
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

