@extends('layouts.master')

@section('title', 'Payments | ICFMS ' . date('Y'))
@section('title2', 'Payments')

@section('content')
    <div class="row">
        <div class="panel">
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12">
                        <a class="btn btn-success btn-sm pull-right mb-sm" href="{{ route('payment.create') }}" target="_blank"><i class="fa fa-file-excel-o"></i> Download Excel</a>
                    </div>
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table class="table table-sm table-striped table-bordered table-fixed table-condensed"
                                id="tbl-payment">
                                <thead>
                                    <tr>
                                        <th style="width: 10%" class="text-center">Date</th>
                                        <th style="width: 10%" class="text-center">Name</th>
                                        <th style="width: 20%" class="text-center">Affiliation</th>
                                        <th style="width: 20%" class="text-center">Nominal</th>
                                        <th style="width: 20%" class="text-center">Email</th>
                                        <th style="width: 10%" class="text-center">Note Payments</th>
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
        $(document).on('click', '.btn-delete',function(params) {
            let b = $(this);
            let conf = bootbox.confirm("Do you want to remove this data ?", function(ans) {
                if(ans){
                    vAjax(b.find('i'),{
                        url : b.attr('data-url'),
                        type : 'DELETE',
                        dataType : 'JSON',
                        done : function (params) {
                            if(params.status){
                                b.parents('tr').remove();
                            }
                        }
                    });
                }
            });
        }).ready(function(evt) {
            let url = '{{ route('payment.index') }}';
            let cols = [{
                    data: 'date_upload',
                    name: 'date_upload',
                    className: 'text-center'
                },
                {
                    data: 'name',
                    name: 'name',
                    className: 'text-center'
                },
                {
                    data: 'affiliation',
                    name: 'affiliation'
                },
                {
                    data: 'terbilang',
                    name: 'terbilang'
                },
                {
                    data: 'email',
                    name: 'email'
                },
                {
                    data: 'action',
                    name: 'action',
                    className: 'text-center'
                }
            ];
            refreshTableServerOn('#tbl-payment', url, cols);
        });
    </script>
@endpush
