@extends('layouts.master')

@section('title', 'Documents')
@section('title2', 'Documents')

@section('content')
    <div class="row">
        <div class="panel">
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12">
                        <table class="table table-sm table-striped table-bordered table-fixed table-condensed"
                            id="tbl-documents">
                            <thead>
                                <tr>
                                    <th style="width: 80%" class="text-center">Document Name</th>
                                    <th style="width: 20%" class="text-center">&nbsp;</th>
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

@push('js')
    <script type="text/javascript">
        $(document).ready(function(evt) {
            let url = '{{ route('documents.index') }}';
            let cols = [{
                    data: 'nama',
                    name: 'nama',
                },
                {
                    data: 'btnView',
                    name: 'btnView',
                    className: 'text-center'
                }
            ];
            refreshTableServerOn('#tbl-documents', url, cols);
        })
    </script>
@endpush
