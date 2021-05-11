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
                                        @if(isset($products) && $products->isNotEmpty())
                                            @foreach($products as $row)
                                                <option value="{{ $row->id }}">{{ $row->name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                    <span class="kt-form__help error product_id"></span>
                                </div>
                                <div class="form-group col-sm-2">
                                    <label for="base_quantity">Quantity <span class="text-danger">*</span></label>
                                    <input type="text" name="base_quantity" id="base_quantity" class="form-control digit" placeholder="Plese enter quantity" />
                                    <span class="kt-form__help error quantity"></span>
                                </div>
                                <div class="form-group col-sm-2">
                                    <label for="base_price">Price <span class="text-danger">*</span></label>
                                    <input type="text" name="base_price" id="base_price" class="form-control digit" placeholder="Plese enter price" />
                                    <span class="kt-form__help error price"></span>
                                </div>
                                <div class="form-group col-sm-2 d-flex align-items-center">
                                    <button type="button" class="btn btn-md btn-primary mt-4" id="add_product">Add Product</button>
                                </div>
                            </div>
                            <div class="row" id="table" style="display:none">
                                <div class="col-sm-12">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th style="width:10%">Sr. No</th>
                                                <th style="width:30%">Product</th>
                                                <th style="width:25%">Product</th>
                                                <th style="width:25%">Price</th>
                                                <th style="width:10%">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            
                                        </tbody>
                                    </table>
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
        $(document).ready(function() {
            $('.digit').keyup(function(e)                                {
                if (/\D/g.test(this.value)){
                    this.value = this.value.replace(/\D/g, '');
                }
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
                }else{
                    var clone = clone_div(1);
                    $("#table tbody").append(clone);
                }
            });

            function clone_div(id){
                return '<tr class="clone" id="clone_'+id+'">'+
                        '<th style="width:10%">'+id+'</th>'+
                        '<th style="width:30%">'+base_product+
                            '<input type="hidden" name="product_id[]" id="product_'+id+'" value="'+base_product_id+'">'+
                        '</th>'+
                        '<th style="width:25%">'+
                            '<input type="text" name="quantity[]" id="quantity_'+id+'" value="'+base_quantity+'" class="form-control digit" required>'+
                        '</th>'+
                        '<th style="width:25%">'+
                            '<input type="text" name="price[]" id="price_'+id+'" value="'+base_price+'" class="form-control digit" required>'+
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
    </script>
@endsection

