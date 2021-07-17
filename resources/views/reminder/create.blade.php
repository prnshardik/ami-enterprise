@extends('layout.app')

@section('meta')
@endsection

@section('title')
    Create Reminders
@endsection

@section('styles')
    <link href="{{ asset('assets/css/bootstrap-dateTimePicker.css') }}" rel="stylesheet" />
@endsection

@section('content')
    <div class="page-content fade-in-up">
        <div class="row">
            <div class="col-md-12">
                <div class="ibox">
                    <div class="ibox-head">
                        <div class="ibox-title">Create Reminders</div>
                    </div>
                    <div class="ibox-body">
                        <form name="form" action="{{ route('reminders.insert') }}" id="form" method="post" enctype="multipart/form-data">
                            @csrf
                            
                            <div class="row">
                                <div class="form-group col-sm-6">
                                    <label for="title">Title <span class="text-danger">*</span></label>
                                    <input type="text" name="title" id="title" class="form-control" placeholder="Plese enter title" value="{{ @old('title') }}" />
                                    <span class="kt-form__help error title"></span>
                                </div>
                                <div class="form-group col-sm-6">
                                    <label for="date_time">Date Time <span class="text-danger">*</span></label>
                                    <input type="text"  name="date_time" id="date_time" class="form-control" placeholder="Plese enter date_time" value="{{ @old('date_time') }}" />
                                    <span class="kt-form__help error date_time"></span>
                                </div>
                                <div class="form-group col-sm-12">
                                    <label for="note">Description <span class="text-danger"></span></label>
                                    <textarea name="note" id="note" class="form-control"  placeholder="Plese enter note" cols="30" rows="10">{{ @old('note') }}</textarea>
                                    <span class="kt-form__help error note"></span>
                                </div>
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">Submit</button>
                                <a href="{{ route('reminders') }}" class="btn btn-default">Back</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('assets/js/moment.js') }}"></script>
    <script src="{{ asset('assets/js/bootstrap-dateTimePicker.js') }}"></script>
    
    <script>
        $(document).ready(function () {
            $('#date_time').datetimepicker();

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

