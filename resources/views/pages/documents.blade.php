@extends('layouts.master')

@section('title', 'Documents')
@section('title2', 'Documents')

@section('content')
    <div class="row">
        <div class="panel">
            <div class="panel-body">
                <div class="row">
                    {{-- <div class="col-md-12">
                        <a class="btn btn-success btn-sm pull-right mb-sm" href="{{ route('user.excel') }}" target="_blank"><i class="fa fa-file-excel-o"></i> Download Excel</a>
                    </div> --}}
                    <div class="col-md-12">
                        <button class="btn btn-sm btn-primary mb-sm" data-id="0" id="btn-addForm"><i class="fa fa-plus"></i>
                            Add Document</button>
                        <table class="table table-sm table-striped table-bordered table-fixed table-condensed"
                            id="tbl-documents">
                            <thead>
                                <tr>
                                    <th style="width: 40%" class="text-center">Document Name</th>
                                    <th style="width: 20%" class="text-center">Upload at</th>
                                    <th style="width: 10%" class="text-center">Users</th>
                                    <th style="width: 10%" class="text-center">&nbsp;</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('css')
    <link rel="stylesheet" href="{{ asset('css/select2.min.css') }}">
@endpush
@push('js')
    <script src="{{ asset('js/select2.min.js') }}"></script>
    <script type="text/javascript">
        $(document).ready(function(evt) {
            let url = '{{ route('documents.index') }}';
            let cols = [{
                    data: 'namaLink',
                    name: 'namaLink',
                },
                {
                    data: 'upload_at',
                    name: 'upload_at',
                    className: 'text-center'
                },
                {
                    data: 'users',
                    name: 'users'
                },
                {
                    data: 'action',
                    name: 'action',
                    className: 'text-center'
                }
            ];
            refreshTableServerOn('#tbl-documents', url, cols);
        }).on('click', '.span-more', function(e) {
            e.preventDefault();
            let ul = $(this).parents('ol').siblings('ol.hide').clone();
            ul.removeClass('hide');
            bootbox.alert({
                size: 'small',
                title: 'List Users',
                message: ul[0],
            });
        }).on('click', '#btn-addForm', function(e) {
            let b = $(this),
                url = '{{ route('documents.create') }}';
            vAjax(b.find('i'), {
                url: url,
                done: function(res) {
                    showModal(res).on('shown.bs.modal', function(e) {
                        $(e.target).find('select#user-doc').select2({
                            dropdownParent: $('#myModal')
                        })
                    });

                }
            });
        }).on('click', 'span.badge', function(e) {
            let id = $(this).find('input').val();
            let opt = $('select#user-doc > option[value=' + id + ']')
            opt.removeClass('bg-gray-200').removeAttr('disabled');
            $(this).remove();
        }).on('submit', '#fo-document', function(e) {
            e.preventDefault();
            let f = $(this),
                url = '{{ route('documents.store') }}';
            let b = f.find('button[type=submit]');
            vAjax(b.find('i'), {
                url: url,
                data: toFormData('#fo-document'),
                dataType: 'JSON',
                processData: false,
                contentType: false,
                type: 'POST',
                done: function(e) {
                    b.parents('div.modal').modal('hide');
                    $('#tbl-documents').DataTable().ajax.reload();
                }
            });
        }).on('change', 'select#user-doc', function(e) {
            let cb = $(this),
                opt = cb.find('option:selected');
            colorBadgeEmail(opt);
        }).on('change', 'input[name=path_file]', function(e) {
            let str = e.target.files[0].name;
            let name = str.substring(0, str.length - 4);
            console.log(name);
            $('input[name=nama]').val(name);
        }).on('click', '.btn-delete', function(params) {
            let b = $(this),
                i = b.find('i');
            let url = '{{ route('documents.destroy', ['document' => ':id']) }}';
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

        let colorBadgeEmail = function(opt) {
            if (opt.val() == '') {
                return false;
            }
            let bg = ['success', 'warning', 'danger', 'lime', 'green', 'info', 'black'];
            let div = $('#selected-email');
            let rdm = Math.floor(Math.random() * bg.length);
            let spn = $("<span></span>").addClass('badge bg-' + bg[rdm]).html(opt.text());
            spn.append('<input type="hidden" name="user[]" value="' + opt.val() + '"> <i class="fa fa-times"></i>').css(
                'cursor', 'pointer');
            div.append(spn).append(' ');
            opt.addClass('bg-gray-200').attr('disabled', 'disabled')
        }
    </script>
@endpush
