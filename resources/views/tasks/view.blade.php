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
                                <label for="title">Title</label>
                                <input type="text" name="title" id="title" value="{{ $data->title ?? '' }}" class="form-control" placeholder="Plese enter title" disabled />
                                <span class="kt-form__help error title"></span>
                            </div>
                            <div class="form-group col-sm-6">
                                <label for="users">Allocate To</label>
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
                                <label for="description">Instruction</label>
                                <textarea name="description" id="description" class="form-control" placeholder="Plese enter Instruction" disabled> {{ $data->description ?? '' }} </textarea>
                                <span class="kt-form__help error description"></span>
                            </div>
                            <div class="form-group col-sm-6">
                                <label for="t_date">Taget Date</label>
                                <input type="date" name="t_date" id="t_date" value="{{ $data->target_date ??'' }}" class="form-control" placeholder="Plese enter target date" min="{{ Date('Y-m-d') }}" disabled/>
                                <span class="kt-form__help error t_date"></span>
                            </div>
                            <div class="form-group col-sm-12">
                                <label for="file">Attechment</label>
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
        $(document).ready(function(){
            var drEvent = $('.dropify').dropify();
        
            $('#users').select2({
                placeholder:"Plase Select User"
            });
        });
    </script>
   
@endsection

