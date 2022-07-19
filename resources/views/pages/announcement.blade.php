@extends('layouts.master')

@section('title', 'Announcement | ICFMS ' . date('Y'))
@section('title2', 'Announcement')

@section('content')
    <div class="row">
        <div class="panel">
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12">
                        <x-auth-session-status class="alert alert-success" />
                        <!-- Validation Errors -->
                        <x-auth-validation-errors class="alert alert-danger" :errors="$errors" />
                        <form id="fo-announcement" class="form-horizontal">
                            @csrf
                            <div class="form-group">
                                <label class="control-label col-sm-2">Title</label>
                                <div class="col-sm-5">
                                    {!! Form::text('title', '', ['class' => 'form-control input-sm']) !!}
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-sm-2">Target Email</label>
                                <div class="col-sm-5">
                                    {!! Form::select('target', $target, '', ['class' => 'form-control input-sm']) !!}
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-sm-2">Content Email</label>
                                <div class="col-sm-10">
                                    <textarea name="isi_email" id="isi_email" class="form-control"></textarea>
                                    <div id="isi_email"></div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-sm-2">File Attachment</label>
                                <div class="col-sm-6">
                                    {!! Form::file('attachment', ['class' => 'form-control input-sm']) !!}
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-5">
                                    <button type="button" class="btn btn-sm btn-info"><i
                                            class="fa fa-search"></i>&nbsp;Preview</button>
                                    <button type="submit" class="btn btn-sm btn-success"><i
                                            class="fa fa-send"></i>&nbsp;Send To Email</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('css')
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">
@endpush
@push('js')
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function(params) {
            let editor = $('#isi_email').summernote({
                height: 150
            });
        }).on('submit', '#fo-announcement', function(e) {
            e.preventDefault();
            let f = $(this);
            var b = f.find('button[type="submit"]');
            let datx = toFormData(this);
            let i = b.find('i');
            vAjax(i, {
                url: '{{ route('announcement.store') }}',
                type: 'POST',
                processData: false,
                contentType: false,
                data: datx,
                dataType: 'JSON',
                done: function(res) {
                    f.clear();
                    if(res.status){
                        msgSuccess(res.head.head);
                    } else {
                        msgAlert(res.head.head);
                    }
                }
            });
        });
    </script>
@endpush
