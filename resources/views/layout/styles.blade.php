<link href="{{ asset('assets/vendors/bootstrap/dist/css/bootstrap.min.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/vendors/font-awesome/css/font-awesome.min.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/vendors/themify-icons/css/themify-icons.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/vendors/jvectormap/jquery-jvectormap-2.0.3.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/css/main.min.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/vendors/DataTables/datatables.min.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/vendors/toastr/toastr.min.css') }}" rel="stylesheet" />

<style>
	.error{
		color:red;
	}
	
	/* .page-footer{
		position: fixed;
		top: 600px;
		left: 222px;
	} */

	/* body:not(.fixed-layout).sidebar-mini .page-footer {
    	left: 60px; 
	} */

	/* @media only screen and (max-device-width: 1024px) { 
		.page-footer {
			top: 530px !important;
		}
	} */

	.ibox{
		margin: 10px;
	}
</style>

@yield('styles')