@extends('layout.app')

@section('meta')
@endsection

@section('title')
    View Reminders
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
                        <div class="ibox-title">View Reminders</div>
                    </div>
                    <div class="ibox-body">
                        <div class="row">
                            <div class="form-group col-sm-6">
                                <label for="title">Title <span class="text-danger">*</span></label>
                                <input type="text" name="title" id="title" class="form-control" placeholder="Plese enter title" value="{{ $data->title ??'' }}" disabled/>
                                <span class="kt-form__help error title"></span>
                            </div>
                            <div class="form-group col-sm-6">
                                <label for="date_time">Date Time <span class="text-danger">*</span></label>
                                <input type="text"  name="date_time" id="date_time" class="form-control" placeholder="Plese enter date_time" value="{{ date('m-d-Y H:i:A', strtotime($data->date_time)) ??'' }}" disabled/>
                                <span class="kt-form__help error date_time"></span>
                            </div>
                            <div class="form-group col-sm-12">
                                <label for="note">Description <span class="text-danger"></span></label>
                                <textarea name="note" id="note" class="form-control"  placeholder="Plese enter note" cols="30" rows="10" disabled>{{ $data->note?? '' }}</textarea>
                                <span class="kt-form__help error note"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <a href="{{ route('reminders') }}" class="btn btn-default">Back</a>
                        </div>
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
        });
    </script>
@endsection

