@extends('layout.app')

@section('meta')
@endsection

@section('title')
    View Order
@endsection

@section('styles')
@endsection

@section('content')
    <div class="page-content fade-in-up">
        <div class="row">
            <div class="col-md-12">
                <div class="ibox">
                    <div class="ibox-head">
                        <div class="ibox-title">View Order</div>
                    </div>
                    <div class="ibox-body">
                        <div class="row">
                            <div class="form-group col-sm-6">
                                <label for="name">Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" id="name" class="form-control" value="{{ $data->name ?? '' }}" placeholder="Plese enter name" disabled />
                                <span class="kt-form__help error name"></span>
                            </div>
                            <div class="form-group col-sm-6">
                                <label for="order_date">Order Date <span class="text-danger">*</span></label>
                                <input type="date" name="order_date" id="order_date" class="form-control" value="{{ $data->order_date ?? '' }}" placeholder="Plese enter order date" disabled />
                                <span class="kt-form__help error order_date"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <a href="{{ route('orders') }}" class="btn btn-default">Back</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
@endsection

