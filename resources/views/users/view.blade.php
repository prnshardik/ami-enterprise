@extends('layout.app')

@section('meta')
@endsection

@section('title')
    View User
@endsection

@section('styles')
@endsection

@section('content')
    <div class="page-content fade-in-up">
        <div class="row">
            <div class="col-md-12">
                <div class="ibox">
                    <div class="ibox-head">
                        <div class="ibox-title">View User</div>
                    </div>
                    <div class="ibox-body">
                        <div class="row">
                            <div class="form-group col-sm-6">
                                <label for="firstname">Name <span class="text-danger">*</span></label>
                                <input type="text" name="firstname" id="firstname" class="form-control" placeholder="Plese enter firstname" value="{{ $data->firstname ?? '' }}" disabled="disabled" />
                                <span class="kt-form__help error firstname"></span>
                            </div>
                            <div class="form-group col-sm-6">
                                <label for="email">Email <span class="text-danger">*</span></label>
                                <input type="text" name="email" id="email" class="form-control" placeholder="Plese enter email address" value="{{ $data->email ?? '' }}" disabled="disabled" />
                                <span class="kt-form__help error email"></span>
                            </div>
                            <div class="form-group col-sm-6">
                                <label for="phone">Phone Number <span class="text-danger"></span></label>
                                <input type="text" name="phone" id="phone" class="form-control" placeholder="Plese enter phone number" value="{{ $data->phone ?? '' }}" disabled="disabled" />
                                <span class="kt-form__help error phone"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <a href="{{ route('users') }}" class="btn btn-default">Back</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
@endsection

