@extends('layout.app')

@section('meta')
@endsection

@section('title')
    Create Product
@endsection

@section('styles')
@endsection

@section('content')
    <div class="page-content fade-in-up">
        <div class="row">
            <div class="col-md-12">
                <div class="ibox">
                    <div class="ibox-head">
                        <div class="ibox-title">Create Product</div>
                    </div>
                    <div class="ibox-body">
                        <form name="form" action="{{ route('products.insert') }}" id="form" method="post" enctype="multipart/form-data">
                            @csrf
                            
                            <div class="row">
                                <div class="form-group col-sm-6">
                                    <label for="name">Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" id="name" class="form-control" placeholder="Plese enter name" value="{{ @old('name') }}" />
                                    <span class="kt-form__help error name"></span>
                                </div>
                                
                                <div class="form-group col-sm-6">
                                    <label for="code">Product Code <span class="text-danger"></span></label>
                                    <input type="text" name="code" id="code" class="form-control" placeholder="Plese enter product code" value="{{ @old('code') }}" />
                                    <span class="kt-form__help error code"></span>
                                </div>

                                <div class="form-group col-sm-6">
                                    <label for="unit">Unit <span class="text-danger"></span></label>
                                    <input type="text" name="unit" id="unit" class="form-control" placeholder="Plese enter unit" value="{{ @old('unit') }}" />
                                    <span class="kt-form__help error unit"></span>
                                </div>
                                
                                <div class="form-group col-sm-6">
                                    <label for="price">Price <span class="text-danger"></span></label>
                                    <input type="text" name="price" id="price" class="form-control digits" placeholder="Plese enter price" value="{{ @old('price') }}" />
                                    <span class="kt-form__help error price"></span>
                                </div>
                                <div class="form-group col-sm-6">
                                    <label for="note">Note <span class="text-danger"></span></label>
                                    <input type="text" name="note" id="note" class="form-control" placeholder="Plese enter note" value="{{ @old('note') }}" />
                                    <span class="kt-form__help error note"></span>
                                </div>
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">Submit</button>
                                <a href="{{ route('products') }}" class="btn btn-default">Back</a>
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
        $(document).ready(function () {
            $('.digits').keyup(function(e){
                if (/\D/g.test(this.value)){
                    this.value = this.value.replace(/\D/g, '');
                }
            });
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

