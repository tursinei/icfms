@extends('layouts.master')

@section('title', 'Participants | ICFMS ' . date('Y'))
@section('title2', 'Participants')

@section('content')
    <div class="row">
        <div class="panel">
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table class="table table-sm table-striped table-bordered table-fixed table-condensed" id="tbl-participant">
                                <thead>
                                    <tr>
                                        <th style="width: 5%" class="text-center">Title</th>
                                        <th style="width: 10%" class="text-center">First Name</th>
                                        <th style="width: 10%" class="text-center">Middle Name</th>
                                        <th style="width: 10%" class="text-center">Last Name</th>
                                        <th style="width: 15%" class="text-center">Address</th>
                                        <th style="width: 10%" class="text-center">Country</th>
                                        <th style="width: 15%" class="text-center">Main Email</th>
                                        <th style="width: 15%" class="text-center">Second Email</th>
                                        <th style="width: 5%" class="text-center">Affiliation</th>
                                        <th style="width: 10%" class="text-center">Mobile Number</th>
                                        <th style="width: 10%" class="text-center">Phone Number</th>
                                        <th style="width: 5%" class="text-center">&nbsp;</th>
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
            let url = '{{ route('user.index') }}';
            let cols = [
                { data : 'title', name: 'title', className:'text-center'},
                { data : 'firstname', name: 'firstname'},
                { data : 'midlename', name: 'midlename'},
                { data : 'lastname', name: 'lastname'},
                { data : 'address', name: 'address'},
                { data : 'country', name: 'country'},
                { data : 'email', name: 'email'},
                { data : 'secondemail', name: 'secondemail'},
                { data : 'affiliation', name: 'affiliation'},
                { data : 'mobilenumber', name: 'mobilenumber'},
                { data : 'phonenumber', name: 'phonenumber'},
                { data: 'action', name: 'action',className:'text-center'}
            ];
            refreshTableServerOn('#tbl-participant', url, cols);
        }).on('click','.btn-download',function(params) {
            let b = $(this), url = '{{ route('user.show', ['user' => ':id']) }}';
            url = url.replace(':id', b.attr('data-id'));
            vAjax(b.find('i'), {
                url : url,
                done : function (res) {
                    showModal(res);
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
