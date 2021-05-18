@extends('layout.app')

@section('meta')
@endsection

@section('title')
    Edit Task
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
                        <div class="ibox-title">Edit Task</div>
                    </div>
                    <div class="ibox-body">
                        <form name="form" action="{{ route('tasks.update') }}" id="form" method="post" enctype="multipart/form-data">
                            @csrf
                            @method('PATCH')
                            
                            <input type="hidden" name="id" value="{{ $data->id }}">
                            
                            <div class="row">
                                <div class="form-group col-sm-6">
                                    <label for="title">Title <span class="text-danger">*</span></label>
                                    <input type="text" name="title" id="title" value="{{ @old('title', $data->title) }}" class="form-control" placeholder="Plese enter title" />
                                    <span class="kt-form__help error title"></span>
                                </div>
                                <div class="form-group col-sm-6">
                                    <label for="users">Allocate To <span class="text-danger">*</span></label>
                                    <select name="users[]" class="form-control select2" placeholder="Plese Select Users" id="users" multiple>
                                        @if(isset($users) && !empty($users))
                                            @foreach($users AS $row)
                                                <option value="{{ $row->id }}" <?=(str_contains($data->user_id, $row->id) ? 'selected' : '')?>>{{ $row->name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                    <span class="kt-form__help error users"></span>
                                </div>
                                <div class="form-group col-sm-6">
                                    <label for="description">Instruction <span class="text-danger">*</span></label>
                                    <textarea name="description" id="description" class="form-control" placeholder="Plese enter Instruction"> {{ @old('description', $data->description) }} </textarea>
                                    <span class="kt-form__help error description"></span>
                                </div>
                                <div class="form-group col-sm-6">
                                    <label for="t_date">Taget Date</label>
                                    <input type="date" name="t_date" id="t_date" value="{{ @old('t_date', $data->target_date) }}" class="form-control" placeholder="Plese enter target date" min="{{ Date('Y-m-d') }}" />
                                    <span class="kt-form__help error t_date"></span>
                                </div>
                                <div class="form-group col-sm-12">
                                    <label for="file">Attechment <span class="text-danger">*</span></label>
                                    @if(isset($data->attechment) && !empty($data->attechment))
                                        @php $file = url('/uploads/task/').'/'.$data->attechment; @endphp
                                    @else
                                        @php $file = ''; @endphp
                                    @endif
                                    <input type="file" name="file" id="file" class="form-control dropify" data-default-file="{{ $file }}" data-show-remove="false" placeholder="Plese select attachment" />
                                    <span class="kt-form__help error file"></span>
                                </div>
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">Submit</button>
                                <a href="{{ route('tasks') }}" class="btn btn-default">Back</a>
                            </div>
                        </form>
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
        });
    </script>
    <script>
        $(document).ready(function () {
            var form = $('#form');
            $('.kt-form__help').html('');
            form.submit(function(e) {
                $('.help-block').html('');
                $('.m-form__help').html('');
                $.ajax({
                    url : form.attr('action'),
                    type : form.attr('method'),
                    data : form.serialize(),
                    dataType: 'json',
                    async:false,
                    success : function(json){
                        return true;
                    },
                    error: function(json){
                        if(json.status === 422) {
                            e.preventDefault();
                            var errors_ = json.responseJSON;
                            $('.kt-form__help').html('');
                            $.each(errors_.errors, function (key, value) {
                                $('.'+key).html(value);
                            });
                        }
                    }
                });
            });
        });
    </script>
@endsection

