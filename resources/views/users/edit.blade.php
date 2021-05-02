@extends('layout.app')

@section('meta')
@endsection

@section('title')
    Edit User
@endsection

@section('styles')
@endsection

@section('content')
    <div class="page-content fade-in-up">
        <div class="row">
            <div class="col-md-12">
                <div class="ibox">
                    <div class="ibox-head">
                        <div class="ibox-title">Edit User</div>
                    </div>
                    <div class="ibox-body">
                        <form name="form" action="{{ route('admin.users.update') }}" id="form" method="post" enctype="multipart/form-data">
                            @csrf
                            @method('PATCH')
                            
                            <input type="hidden" name="id" value="{{ $data->id }}">

                            <div class="row">
                                <div class="form-group col-sm-6">
                                    <label for="name">First Name</label>
                                    <input type="text" name="name" id="name" class="form-control" placeholder="Plese enter name" value="{{ $data->name ?? '' }}" />
                                    <span class="kt-form__help error name"></span>
                                </div>
                                <div class="form-group col-sm-6">
                                    <label for="email">Email</label>
                                    <input type="text" name="email" id="email" class="form-control" placeholder="Plese enter email address" value="{{ $data->email ?? '' }}" />
                                    <span class="kt-form__help error email"></span>
                                </div>
                                <div class="form-group col-sm-6">
                                    <label for="phone">Phone Number</label>
                                    <input type="text" name="phone" id="phone" class="form-control" placeholder="Plese enter phone number" value="{{ $data->phone ?? '' }}" />
                                    <span class="kt-form__help error phone"></span>
                                </div>
                                <div class="form-group col-sm-6">
                                    <label for="password">Password</label>
                                    <input type="password" name="password" id="password" class="form-control" placeholder="Plese enter password number" value="{{ $data->password ?? '' }}" />
                                    <span class="kt-form__help error password"></span>
                                </div>
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">Submit</button>
                                <a href="{{ route('admin.users') }}" class="btn btn-default">Back</a>
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

