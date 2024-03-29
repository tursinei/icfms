@extends('layouts.master')

@section('title', 'Abstract Submission')
@section('title2', 'Abstract Submission')

@push('css')
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">
    <<link rel="stylesheet" href="{{ asset('vendor/bootstrap-tagsinput/bootstrap-tagsinput.css') }}" />
@endpush

@section('content')
    @php
        $title = ['Dr.', 'Prof.', 'Mr.', 'Mrs.'];
        $optTitle = array_combine($title, $title);
        $isMaxUpload = strtotime($setting['tgl_bts_abstract']) >= strtotime('now');
        if(empty($setting['tgl_bts_abstract'])){
            $isMaxUpload = true;
        }
    @endphp
    <div class="row">
        <div class="panel">
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12">
                        @if (session()->has('success'))
                            <div class="alert alert-success">{{ session()->get('success') }}</div>
                        @elseif(empty(Auth::user()->email_verified_at))
                            <div class="alert alert-danger">
                                <form method="POST" action="{{ route('verification.resend') }}">
                                    @csrf
                                    Please verify your email address first, for Abstract Submission.
                                    <strong>[ <a href="#" id="resend-verification">Resend Verification </a>]</strong>
                                </form>
                            </div>
                        @elseif($isMaxUpload)
                            <button class="btn btn-sm btn-primary mb-sm" id="btn-addForm"><i class="fa fa-plus"></i> Add
                                Abstract</button>
                        @endif
                        <span class="text-success text-small pull-right">
                            <strong>{{ $setting['msg_bts_abstract']??'' }}</strong>
                        </span><br/>
                        <div class="table-responsive" style="margin-top:10px">
                            <table class="table table-sm table-striped table-bordered table-fixed table-condensed"
                                id="tbl-abstract">
                                <thead>
                                    <tr>
                                        <th style="width: 10%" class="text-center">Date</th>
                                        <th style="width: 10%" class="text-center">Presentation</th>
                                        <th style="width: 20%" class="text-center">Topic</th>
                                        <th style="width: 20%" class="text-center">Authors</th>
                                        <th style="width: 20%" class="text-center">Abstract Title</th>
                                        <th style="width: 10%" class="text-center">Remarks</th>
                                        <th style="width: 10%" class="text-center">&nbsp;</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                            <span class="text-info text-small">
                                Please delete if there are two or more same abstract titles<br />
                                If you want to revise the abstract, please delete the abstract and then add the revised
                                abstract
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>
    <script src="{{ asset('vendor/bootstrap-tagsinput/bootstrap-tagsinput.js') }}"></script>
    <script type="text/javascript">
        $(document).ready(function(evt) {
            let url = '{{ route('abstract.index') }}';
            let cols = [{
                    data: 'date_upload',
                    name: 'date_upload',
                    className: 'text-center'
                },
                {
                    data: 'presentation',
                    name: 'presentation',
                    className: 'text-center'
                },
                {
                    data: 'topic',
                    name: 'topic'
                },
                {
                    data: 'authors',
                    name: 'authors'
                },
                {
                    data: 'abstract_title',
                    name: 'abstract_title'
                },
                {
                    data: 'remarks',
                    name: 'remarks'
                },
                {
                    data: 'action',
                    name: 'action',
                    className: 'text-center'
                }
            ];
            refreshTableServerOn('#tbl-abstract', url, cols);
        }).on('click', '#resend-verification', function(e) {
            e.preventDefault();
            let form = $(this).parents('form');
            form.submit()
        }).on('click', '#btn-addForm', function(e) {
            let b = $(this);
            vAjax(b.find('i'), {
                url: '{{ route('abstract.create') }}',
                done: function(res) {
                    showModal(res).on('shown.bs.modal', function(params) {
                        $('input[name="authors"]').tagsinput();
                        // let editor = $('textarea[name="abstract"]').summernote({
                        //     height: 120
                        // });
                    });
                }
            });
        }).on('click', '#cektitle', function(e) {
            let b = $(this),
                abs = $('input[name="abstract_title"]'),
                paper = $('input[name="paper_title"]');
            if (!abs.val()) {
                msgAlert('Fill Abstract Title First');
                b.prop('checked', false);
                abs.focus();
                return false;
            }
            paper.val(abs.val());
            paper.attr('readonly', true);
            if (!b.is(':checked')) {
                paper.val('');
                paper.removeAttr('readonly');
            }
        }).on('submit', '#fo-abstract', function(e) {
            e.preventDefault();
            var fo = $(this),
                b = fo.find('button[type="submit"]');
            let datx = toFormData(this);
            let i = b.find('i');
            vAjax(i, {
                url: '{{ route('abstract.store') }}',
                type: 'POST',
                processData: false,
                contentType: false,
                data: datx,
                dataType: 'JSON',
                done: function(res) {
                    $('#tbl-abstract').DataTable().ajax.reload();
                    b.parents('.modal').modal('hide');
                },
                async: true,
                xhr: function() {
                    var xhr = new window.XMLHttpRequest();
                    if (fo.find('input[type="file"]')[0].files.length == 0) {
                        return xhr;
                    }
                    xhr.upload.onprogress = function(evt) {
                        if (evt.lengthComputable) {
                            var percent = Math.round((evt.loaded / evt.total) * 100);
                            $('#bar-fileprogress').attr('aria-valuenow', percent).css('width',
                                percent + '%').html(percent + '%');
                        }
                    }
                    return xhr;
                }
            });
        }).on('click', '.btn-hapus', function(params) {
            let b = $(this),
                i = b.find('i');
            let url = '{{ route('abstract.destroy', ['abstract' => ':id']) }}';
            url = url.replace(':id', b.attr('data-id'));
            let conf = bootbox.confirm("Do you want to remove this data ?", function(ans) {
                if (ans) {
                    vAjax(i, {
                        url: url,
                        type: 'DELETE',
                        dataType: 'JSON',
                        done: function(res) {
                            b.parents('tr').remove();
                        }
                    });
                }
            });
        });
    </script>
@endpush
