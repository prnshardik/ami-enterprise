@extends('layout.app')

@section('meta')
@endsection

@section('title')
    Dashboard
@endsection

@section('styles')
@endsection

@section('content')
    <div class="page-content fade-in-up">
        <div class="row">
            <div class="col-lg-3 col-md-6">
                <div class="ibox bg-success color-white widget-stat">
                    <div class="ibox-body">
                        <h2 class="m-b-5 font-strong">{{ $data['users'] ?? 0 }}</h2>
                        <div class="m-b-5">Users</div><i class="fa fa-users widget-stat-icon"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="ibox bg-info color-white widget-stat">
                    <div class="ibox-body">
                        <h2 class="m-b-5 font-strong">{{ $data['products'] ?? 0 }}</h2>
                        <div class="m-b-5">Products</div><i class="fa fa-product-hunt widget-stat-icon"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="ibox bg-warning color-white widget-stat">
                    <div class="ibox-body">
                        <h2 class="m-b-5 font-strong">{{ $data['tasks'] ?? 0 }}</h2>
                        <div class="m-b-5">Tasks</div><i class="fa fa-tasks widget-stat-icon"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="ibox bg-danger color-white widget-stat">
                    <div class="ibox-body">
                        <h2 class="m-b-5 font-strong">{{ $data['notices'] ?? 0 }}</h2>
                        <div class="m-b-5">Notices</div><i class="fa fa-bullhorn widget-stat-icon"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <!-- <script src="{{ asset('assets/js/scripts/dashboard_1_demo.js') }}" type="text/javascript"></script> -->
@endsection