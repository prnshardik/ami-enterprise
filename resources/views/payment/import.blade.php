@extends('layout.app')

@section('meta')
@endsection

@section('title')
    Upload new data - Payment
@endsection

@section('styles')
@endsection

@section('content')
    <div class="page-content fade-in-up">
        <div class="row">
            <div class="col-md-12">
                <div class="ibox">
                    <div class="ibox-head">
                        <div class="ibox-title">Upload new data - Payment</div>
                    </div>
                    <div class="ibox-body">
                        <form name="form" action="{{ route('payment.import') }}" id="form" method="post" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="form-group col-sm-12">
                                    <label for="file">File <span class="text-danger">*</span></label>
                                    <input type="file" name="file" id="file" class="form-control" placeholder="Plese select file" />
                                    <span class="kt-form__help error file"></span>
                                    @error('file')
                                        <div class="invalid-feedback" style="display: block;">
                                            <strong>{{ $message }}</strong>
                                        </div>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">Submit</button>
                                <a href="{{ route('dashboard') }}" class="btn btn-default">Back</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
@endsection

