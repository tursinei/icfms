@extends('layouts.master')

@section('title', 'Full Paper Submission')
@section('title2', 'Full Paper Submission')

@section('content')
    @php
        $isMaxUpload = strtotime($setting['tgl_bts_paper']) >= strtotime('now');
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
                                Paper</button>
                        @endif
                        <span class="text-success text-small pull-right mb-10">
                            <strong>{{ $setting['msg_bts_paper']??'' }}</strong>
                        </span><br>
                        <div class="table-responsive" style="margin-top:10px">
                            <table class="table table-sm table-striped table-bordered table-fixed table-condensed"
                                id="tbl-paper">
                                <thead>
                                    <tr>
                                        <th style="width: 10%" class="text-center">Date</th>
                                        <th style="width: 10%" class="text-center">Presenter Name</th>
                                        <th style="width: 20%" class="text-center">Authors</th>
                                        <th style="width: 20%" class="text-center">Topic</th>
                                        <th style="width: 20%" class="text-center">Paper Title</th>
                                        <th style="width: 10%" class="text-center">Presentation</th>
                                        <th style="width: 10%" class="text-center">&nbsp;</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                            <span class="text-info text-small">
                                if you change the paper title,please delete both uploaded files of the abstract & paper
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script type="text/javascript">
        $(document).ready(function(evt) {
            let url = '{{ route('fullpaper.index') }}';
            let cols = [{
                    data: 'date_upload',
                    name: 'date_upload',
                    className: 'text-center'
                },
                {
                    data: 'presenter',
                    name: 'presenter'
                },
                {
                    data: 'authors',
                    name: 'authors'
                },
                {
                    data: 'topic',
                    name: 'topic'
                },
                {
                    data: 'title',
                    name: 'title'
                },
                {
                    data: 'presentation',
                    name: 'presentation'
                },
                {
                    data: 'action',
                    name: 'action',
                    className: 'text-center'
                }
            ];
            refreshTableServerOn('#tbl-paper', url, cols);
        }).on('click', '#resend-verification', function(e) {
            e.preventDefault();
            let form = $(this).parents('form');
            form.submit()
        }).on('click', '#btn-addForm', function(e) {
            let b = $(this);
            vAjax(b.find('i'), {
                url: '{{ route('fullpaper.create') }}',
                done: function(res) {
                    showModal(res);
                }
            });
        }).on('change', 'select.list-abstract', function(e) {
            let cb = $(this),i = cb.siblings('i');
            let url = '{{ route('abstract.edit', ['abstract' => ':id']) }}';
            url = url.replace(':id', cb.val());
            vAjax(i,{
                url : url,
                dataType : 'JSON',
                done :function(e) {
                    $.each(e, function(k,v){
                        let key = k.replace('_','');
                        $('#text-'+key).html(v);
                        $('#'+key).val(v);
                        if(key == 'topic'){
                            $('#text-topic').html(v.name);
                        }

                    });
                }
            });
        }).on('submit', '#fo-paper', function(e) {
            e.preventDefault();
            var fo = $(this), b = fo.find('button[type="submit"]');
            let datx = toFormData(this);
            let i = b.find('i');
            vAjax(i, {
                url: '{{ route('fullpaper.store') }}',
                type: 'POST',
                processData: false,
                contentType: false,
                data: datx,
                dataType: 'JSON',
                done: function(res) {
                    b.parents('div.modal').modal('hide');
                    $('#tbl-paper').DataTable().ajax.reload();
                },
                async : true,
                xhr : function(){
                    var xhr = new window.XMLHttpRequest();
                    if(fo.find('input[type="file"]')[0].files.length == 0){
                        return xhr;
                    }
                    xhr.upload.onprogress = function(evt) {
                        if(evt.lengthComputable){
                            var percent = Math.round((evt.loaded / evt.total) * 100);
                            $('#bar-fileprogress').attr('aria-valuenow', percent).css('width',percent+'%').html(percent+'%');
                        }
                    }
                    return xhr;
                }
            });
        }).on('click', '.btn-hapus', function(params) {
            let b = $(this),
                i = b.find('i');
            let url = '{{ route('fullpaper.destroy', ['fullpaper' => ':id']) }}';
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
