@extends('layouts.master')

@section('title', 'Full Paper')
@section('title2', 'Full Paper')

@section('content')
    <div class="row">
        <div class="panel">
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12">
                        <a class="btn btn-success btn-sm pull-right mb-sm" href="{{ route('fullpapers.create') }}" target="_blank"><i class="fa fa-file-excel-o"></i> Download Excel</a>
                    </div>
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table class="table table-sm table-striped table-bordered table-fixed table-condensed"
                                id="tbl-paper">
                                <thead>
                                    <tr>
                                        <th style="width: 5%" class="text-center">Date</th>
                                        <th style="width: 10%" class="text-center">Email</th>
                                        <th style="width: 5%" class="text-center">Title</th>
                                        <th style="width: 10%" class="text-center">Name</th>
                                        <th style="width: 7%" class="text-center">Affiliation</th>
                                        <th style="width: 5%" class="text-center">Country</th>
                                        <th style="width: 8%" class="text-center">Presentation</th>
                                        <th style="width: 8%" class="text-center">Presenter Name</th>
                                        <th style="width: 10%" class="text-center">Topic</th>
                                        <th style="width: 10%" class="text-center">Authors</th>
                                        <th style="width: 15%" class="text-center">Paper Title</th>
                                        <th style="width: 15%" class="text-center">Remarks</th>
                                        <th style="width: 7%" class="text-center">&nbsp;</th>
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
    </div>
@endsection

@push('js')
    <script type="text/javascript">
        $(document).ready(function(evt) {
            let url = '{{ route('fullpapers.index') }}';
            let cols = [{
                    data: 'date_upload',
                    name: 'date_upload',
                    className: 'text-center'
                },
                {
                    data: 'email',
                    name: 'email'
                },
                {
                    data: 'prefix',
                    name: 'prefix',
                    className: 'text-center'
                },
                {
                    data: 'fullname',
                    name: 'fullname'
                },
                {
                    data: 'affiliation',
                    name: 'affiliation'
                },
                {
                    data: 'country',
                    name: 'country'
                },
                {
                    data: 'presentation',
                    name: 'presentation',
                    className: 'text-center'
                },
                {
                    data: 'presenter',
                    name: 'presenter'
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
                    data: 'title',
                    name: 'title'
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
            refreshTableServerOn('#tbl-paper', url, cols);
        }).on('click', '.btn-hapus', function(params) {
            let b = $(this),
                i = b.find('i');
            url = b.attr('data-href')
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
