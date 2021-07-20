@extends('layout.app')

@section('meta')
@endsection

@section('title')
    View Product
@endsection

@section('styles')
@endsection

@section('content')
    <div class="page-content fade-in-up">
        <div class="row">
            <div class="col-md-12">
                <div class="ibox">
                    <div class="ibox-head">
                        <div class="ibox-title">View Product</div>
                    </div>
                    <div class="ibox-body">
                        <div class="row">
                            <div class="form-group col-sm-6">
                                <label for="name">Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" id="name" class="form-control" value="{{ $data->name ?? '' }}" placeholder="Plese enter name" disabled />
                                <span class="kt-form__help error name"></span>
                            </div>
                            <div class="form-group col-sm-6">
                                <label for="code">Product Code <span class="text-danger"></span></label>
                                <input type="text" name="code" id="code" class="form-control" value="{{ $data->code ?? '' }}" placeholder="Plese enter product code" disabled />
                                <span class="kt-form__help error code"></span>
                            </div>
                            <div class="form-group col-sm-6">
                                <label for="unit">Unit <span class="text-danger"></span></label>
                                <input type="text" name="unit" id="unit" class="form-control" value="{{ $data->unit ?? '' }}" placeholder="Plese enter unit" disabled />
                                <span class="kt-form__help error unit"></span>
                            </div>
                            <div class="form-group col-sm-6">
                                <label for="price">Price <span class="text-danger"></span></label>
                                <input type="text" name="price" id="price" class="form-control digits" value="{{ $data->price ?? '' }}" placeholder="Plese enter price" disabled />
                                <span class="kt-form__help error price"></span>
                            </div>
                            <div class="form-group col-sm-6">
                                <label for="note">Note <span class="text-danger"></span></label>
                                <input type="text" name="note" id="note" class="form-control" value="{{ $data->note ?? '' }}" placeholder="Plese enter note" disabled />
                                <span class="kt-form__help error note"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <a href="{{ route('products') }}" class="btn btn-default">Back</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
@endsection

