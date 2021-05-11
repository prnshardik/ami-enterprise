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
                        @if(isset($data->order_details) && $data->order_details->isNotEmpty())
                            <div class="row" id="table" style="display:block">
                        @else
                            <div class="row" id="table" style="display:none">
                        @endif
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
                                        @if(isset($data->order_details) && $data->order_details->isNotEmpty())
                                            @php $i=1; @endphp
                                            @foreach($data->order_details as $row)
                                                <tr class="clone" id="clone_{{ $i }}">
                                                    <th style="width:10%">{{ $i }}</th>
                                                    <th style="width:30%">{{ $row->product_name }}
                                                        <input type="hidden" name="product_id[]" id="product_{{ $i }}" value="{{ $row->product_id }}">
                                                    </th>
                                                    <th style="width:25%">
                                                        <input type="text" name="quantity[]" id="quantity_{{ $i }}" value="{{ $row->quantity }}" class="form-control digit" disabled>
                                                    </th>
                                                    <th style="width:25%">
                                                        <input type="text" name="price[]" id="price_{{ $i }}" value="{{ $row->price }}" class="form-control digit" disabled>
                                                    </th>
                                                    <th style="width:10%">
                                                        <button type="button" class="btn btn-danger delete" data-id="{{ $i }}" disabled >Remove</button>
                                                    </th>
                                                </tr>
                                                @php $i++; @endphp
                                            @endforeach
                                        @endif
                                    </tbody>
                                </table>
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

