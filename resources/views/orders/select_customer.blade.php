@extends('layout.app')

@section('meta')
@endsection

@section('title')
    Orders select customer
@endsection

@section('styles')
@endsection

@section('content')
    <div class="page-content fade-in-up">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox">
                    <div class="ibox-head">
                        <h1 class="ibox-title">Orders select customer</h1>
                    </div>
                    <div class="ibox-body">
                        <div class="row mt-5">
                            <div class="col-lg-3 col-md-6"></div>
                            <div class="col-lg-3 col-md-6">
                                <a href="{{ route('orders.create') }}"> 
                                    <div class="ibox bg-info color-white widget-stat">
                                        <div class="ibox-body p-5 text-center">
                                            <h2 class="m-b-5 font-strong text-white">Old Customers</h2>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <div class="col-lg-3 col-md-6">
                                <a href="{{ route('customers.create') }}"> 
                                    <div class="ibox bg-warning color-white widget-stat">
                                        <div class="ibox-body p-5 text-center">
                                            <h2 class="m-b-5 font-strong text-white">New Customer</h2>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <div class="col-lg-3 col-md-6"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
@endsection
