@extends('layout.app')

@section('meta')
@endsection

@section('title')
    View Task
@endsection

@section('styles')
    <link href="{{ asset('assets/css/dropify.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/sweetalert2.bundle.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/select2.min.css') }}" rel="stylesheet" />
@endsection

@section('content')
    <div class="page-content fade-in-up">
        <div class="row">
            <div class="col-md-12">
                <div class="ibox">
                    <div class="ibox-head">
                        <div class="ibox-title">View Task</div>
                    </div>
                    <div class="ibox-body">
                        <div class="row">
                            <div class="form-group col-sm-6">
                                <label for="type">Type <span class="text-danger">*</span></label>
                                @php
                                    $exst_type = '';
                                    $selected_type = '';

                                    if(isset($data->type)){
                                        if(in_array($data->type, ['order', 'site_visit', 'payment']))
                                            $selected_type = $data->type;
                                        else
                                            $exst_type = $data->type;
                                    }
                                @endphp
                                <select name="type" id="type" id="type" class="form-control select2_tag" placeholder="Plese select type" disabled="disabled" >
                                    <option value="" hidden>Select type</option>
                                    <option value="order" @if(@old('type', $selected_type) == 'order') selected @endif>Order</option>
                                    <option value="site_visit" @if(@old('type', $selected_type) == 'site_visit') selected @endif>Site Visit</option>
                                    <option value="payment" @if(@old('type', $selected_type) == 'payment') selected @endif>Payment</option>
                                    @if($exst_type != '') 
                                        <option value='{{ $exst_type }}' selected>{{ $exst_type }}</option> 
                                    @endif
                                </select>
                                <span class="kt-form__help error type"></span>
                            </div>
                            <div class="form-group col-sm-6">
                                <div id="customer_div">
                                    <label for="customer_id">Customer <span class="text-danger"></span></label>
                                    <select name="customer_id" class="form-control" placeholder="Plese select customer" id="customer_id" disabled="disabled" >
                                        <option value="" hidden>Select customer</option>
                                        @if(isset($customers) && !empty($customers))
                                            @foreach($customers AS $row)
                                                <option value="{{ $row->party_name }}" @if(isset($data->party_name) && $data->party_name == $row->party_name) selected @endif >{{ $row->party_name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                    <span class="kt-form__help error customer_id"></span>
                                </div>
                            </div>
                            <div class="row" id="details"></div>
                            <div class="form-group col-sm-6">
                                <label for="users">Allocate To <span class="text-danger">*</span></label>
                                <select name="users[]" class="form-control select2" placeholder="Plese Select Users" id="users" multiple disabled>
                                    @if(isset($users) && !empty($users))
                                        @foreach($users as $row)
                                            <option value="{{ $row->id }}" <?=(str_contains($data->user_id, $row->id) ? 'selected' : '')?>>{{ $row->name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                                <span class="kt-form__help error users"></span>
                            </div>
                            <div class="form-group col-sm-6">
                                <label for="description">Instruction <span class="text-danger">*</span></label>
                                <textarea name="description" id="description" class="form-control" placeholder="Plese enter Instruction" disabled> {{ $data->description ?? '' }} </textarea>
                                <span class="kt-form__help error description"></span>
                            </div>
                            <div class="form-group col-sm-6">
                                <label for="t_date">Taget Date</label>
                                <input type="date" name="t_date" id="t_date" value="{{ $data->target_date ??'' }}" class="form-control" placeholder="Plese enter target date" min="{{ Date('Y-m-d') }}" disabled/>
                                <span class="kt-form__help error t_date"></span>
                            </div>
                            <div class="form-group col-sm-12">
                                <label for="file">Attechment <span class="text-danger">*</span></label>
                                @if(isset($data->attechment) && !empty($data->attechment))
                                    @php $file = url('/uploads/task/').'/'.$data->attechment; @endphp
                                @else
                                    @php $file = ''; @endphp
                                @endif
                                <input type="file" name="file" id="file" class="dropify" data-default-file="{{ $file }}" placeholder="Plese select attachment" disabled/>
                                <span class="kt-form__help error file"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <a href="{{ route('tasks') }}" class="btn btn-default">Back</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script src="{{ asset('assets/js/dropify.min.js') }}"></script>
    <script src="{{ asset('assets/js/promise.min.js') }}"></script>
    <script src="{{ asset('assets/js/sweetalert2.bundle.js') }}"></script>
    <script src="{{ asset('assets/js/select2.min.js') }}"></script>

    <script>
        let type = "{{ $data->type ?? '' }}";

        $(document).ready(function(){
            $('.dropify').dropify({
                messages: {
                    'default': 'Drag and drop file here or click',
                    'remove':  'Remove',
                    'error':   'Ooops, something wrong happended.'
                }
            });
            var drEvent = $('.dropify').dropify();
        

            $('#users').select2({
                placeholder:"Plase Select User"
            });

            $('#customer_id').select2({
                placeholder:"Plase select customer"
            });

            $(".select2_tag").select2({
                tags: true
            });

            $('#type').change(function(){
                type = $(this).val();
                if(type == 'order' || type == 'payment' || type == 'site_visit'){
                    $('#customer_div').show();
                } else {
                    $('#customer_div').hide();
                }
            });

            $('#customer_div').hide();

            if(type == 'order' || type == 'payment' || type == 'site_visit'){
                $('#customer_div').show();

                let name = "{{ $data->party_name ?? '' }}";
                _customer_details(name);
            }

            $('#customer_id').change(function () {
                var name = $(this).val();
                if(name != '' || name != null){
                    $("#details").html('');
                    _customer_details(name);
                }
            });

            function _customer_details(name){
                $.ajax({
                    url : "{{ route('tasks.customer.details') }}",
                    type : 'post',
                    data : { "_token": "{{ csrf_token() }}", "name": name, 'type': type},
                    dataType: 'json',
                    async: false,
                    success : function(response){
                        if(response.code == 200){
                            $("#details").html(response.data);
                        }
                    }
                });
            }
        });
    </script>
@endsection

