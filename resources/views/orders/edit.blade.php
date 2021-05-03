@extends('layout.app')

@section('meta')
@endsection

@section('title')
    Edit Order
@endsection

@section('styles')
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
                                    <label for="name">Name</label>
                                    <input type="text" name="name" id="name" class="form-control" value="{{ $data->name ?? '' }}" placeholder="Plese enter name" />
                                    <span class="kt-form__help error name"></span>
                                </div>
                                <div class="form-group col-sm-6">
                                    <label for="order_date">Order Date</label>
                                    <input type="date" name="order_date" id="order_date" class="form-control" value="{{ $data->order_date ?? '' }}" placeholder="Plese enter order date" min="{{ Date('Y-m-d') }}" />
                                    <span class="kt-form__help error order_date"></span>
                                </div>
                                <div class="form-group col-sm-6">
                                    <label for="status">Status</label>
                                    <select name="status" id="status" class="form-control">
                                        <option value="pending" @if($data->status == 'pending') selected @endif>Pending</option>
                                        <option value="completed" @if($data->status == 'completed') selected @endif>Completed</option>
                                    </select>
                                    <span class="kt-form__help error status"></span>
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

