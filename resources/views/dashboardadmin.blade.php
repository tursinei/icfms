@extends('layouts.master')

@section('title', 'Dashboard')
@section('title2', 'Dashboard')

@section('content')
    <div class="row">
        <div class="panel">
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12">
                        <button class="btn btn-sm btn-primary mb-sm"  data-id="0" id="btn-addForm"><i class="fa fa-plus"></i> Add User Admin</button>
                        <div class="table-responsive">
                            <table class="table table-sm table-striped table-bordered table-fixed table-condensed" id="tbl-user">
                                <thead>
                                    <tr>
                                        <th style="width: 20%" class="text-center">Email / Username</th>
                                        <th style="width: 30%" class="text-center">Name</th>
                                        <th style="width: 20%" class="text-center">Affiliation</th>
                                        <th style="width: 10%" class="text-center">Phone Number</th>
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
    </div>
@endsection

@push('js')
    <script type="text/javascript">
       $(document).ready(function(evt) {
            let url = '{{ route('dashboard.index') }}';
            let cols = [
                { data : 'email', name: 'email'},
                { data : 'name', name: 'name'},
                { data : 'affiliation', name: 'affiliation'},
                { data : 'phonenumber', name: 'phonenumber'},
                { data : 'action', name: 'action',className:'text-center'}
            ];
            refreshTableServerOn('#tbl-user', url, cols);
        }).on('click', '#btn-addForm, .btn-edit',function (e) {
            let b = $(this), url = '{{ route('dashboard.show',['dashboard'=>':id']) }}';
            url = url.replace(':id', b.attr('data-id'));
            vAjax(b.find('i'), {
                url : url,
                done : function (res) {
                    showModal(res);
                }
            });
        }).on('submit', '#fo-user', function(e){
            e.preventDefault();
            var b = $(this).find('button[type="submit"]');
            let datx = $(this).serializeArray();
            let i = b.find('i');
            vAjax(i,{
                url : '{{ route('user.store') }}',
                type : 'POST',
                data : datx,
                dataType: 'JSON',
                done : function(res){
                    $('#tbl-user').DataTable().ajax.reload();
                    b.parents('.modal').modal('hide');
                }
            });
        }).on('click','.btn-hapus',function(params) {
            let b = $(this), i = b.find('i');
            let url = '{{ route('user.destroy',['user' => ':id']) }}';
            url = url.replace(':id',b.attr('data-id'));
            let conf = bootbox.confirm("Do you want to remove this data ?", function(ans) {
                if(ans){
                    vAjax(i, {
                        url : url,
                        type : 'DELETE',
                        dataType : 'JSON',
                        done : function(res) {
                            b.parents('tr').remove();
                        }
                    });
                }
            });
        });
    </script>
@endpush
