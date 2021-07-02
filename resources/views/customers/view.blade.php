@extends('layout.app')

@section('meta')
@endsection

@section('title')
    Edit Customer
@endsection

@section('styles')
@endsection

@section('content')
    <div class="page-content fade-in-up">
        <div class="row">
            <div class="col-md-12">
                <div class="ibox">
                    <div class="ibox-head">
                        <div class="ibox-title">Edit Customer</div>
                    </div>
                    <div class="ibox-body">                        
                        <div class="row">
                            <div class="form-group col-sm-6">
                                <label for="party_name">Party Name <span class="text-danger">*</span></label>
                                <input type="text" name="party_name" id="party_name" class="form-control" value="{{ $data->party_name ?? '' }}" placeholder="Plese enter party name" disabled />
                                <span class="kt-form__help error party_name"></span>
                            </div>
                            <div class="form-group col-sm-6">
                                <label for="billing_name">Billing Name <span class="text-danger"></span></label>
                                <input type="text" name="billing_name" id="billing_name" class="form-control" value="{{ $data->billing_name ?? '' }}" placeholder="Plese enter billing name" disabled />
                                <span class="kt-form__help error billing_name"></span>
                            </div>
                            <div class="form-group col-sm-6">
                                <label for="contact_person">Contact person <span class="text-danger"></span></label>
                                <input type="text" name="contact_person" id="contact_person" class="form-control" value="{{ $data->contact_person ?? '' }}" placeholder="Plese enter contact person" disabled />
                                <span class="kt-form__help error contact_person"></span>
                            </div>
                            <div class="form-group col-sm-6">
                                <label for="mobile_number">Mobile number <span class="text-danger"></span></label>
                                <input type="text" name="mobile_number" id="mobile_number" class="form-control" value="{{ $data->mobile_number ?? '' }}" placeholder="Plese enter mobile number" disabled />
                                <span class="kt-form__help error mobile_number digits"></span>
                            </div>
                            <div class="form-group col-sm-6">
                                <label for="billing_address">Billing address <span class="text-danger"></span></label>
                                <textarea name="billing_address" id="billing_address" cols="5" rows="10" class="form-control" placeholder="Plese enter billing address" disabled>{{ $data->billing_address ?? '' }}</textarea>
                                <span class="kt-form__help error billing_address"></span>
                            </div>
                            <div class="form-group col-sm-6">
                                <label for="billing_address">Delivery address <span class="text-danger"></span></label>
                                <textarea name="billing_address" id="billing_address" cols="5" rows="10" class="form-control" placeholder="Plese enter delivery address" disabled>{{ $data->billing_address ?? '' }}</textarea>
                                <span class="kt-form__help error billing_address"></span>
                            </div>
                            <div class="form-group col-sm-6">
                                <label for="electrician">Electrician <span class="text-danger"></span></label>
                                <input type="text" name="electrician" id="electrician" class="form-control" value="{{ $data->electrician ?? '' }}" placeholder="Plese enter electrician" disabled />
                                <span class="kt-form__help error electrician"></span>
                            </div>
                            <div class="form-group col-sm-6">
                                <label for="electrician_number">Electrician number <span class="text-danger"></span></label>
                                <input type="text" name="electrician_number" id="electrician_number" class="form-control" value="{{ $data->electrician_number ?? '' }}" placeholder="Plese enter electrician number" disabled />
                                <span class="kt-form__help error electrician_number digits"></span>
                            </div>
                            <div class="form-group col-sm-6">
                                <label for="architect">Architect <span class="text-danger"></span></label>
                                <input type="text" name="architect" id="architect" class="form-control" value="{{ $data->architect ?? '' }}" placeholder="Plese enter architect" disabled />
                                <span class="kt-form__help error architect"></span>
                            </div>
                            <div class="form-group col-sm-6">
                                <label for="architect_number">Architect number <span class="text-danger"></span></label>
                                <input type="text" name="architect_number" id="architect_number" class="form-control" value="{{ $data->architect_number ?? '' }}" placeholder="Plese enter architect number" disabled />
                                <span class="kt-form__help error architect_number digits"></span>
                            </div>
                            <div class="form-group col-sm-6">
                                <label for="office_contact_person">Office contact person <span class="text-danger"></span></label>
                                <input type="text" name="office_contact_person" id="office_contact_person" class="form-control" value="{{ $data->office_contact_person ?? '' }}" placeholder="Plese enter office contact person" disabled/>
                                <span class="kt-form__help error office_contact_person"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <a href="{{ route('customers') }}" class="btn btn-default">Back</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
@endsection

