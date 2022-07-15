@extends('layouts.master')

@section('title', 'Dashboard | ICFMS ' . date('Y'))
@section('title2', 'Dashboard')

@section('content')
    <div class="row">
        <div class="panel">
            <div class="panel-body">
                <div class="form-horizontal">
                    <div class="form-group">
                        <label class="col-md-3 control-label">Abstract</label>
                        <div class="col-md-8">
                            @php
                                $abstract[0] = '-- Choose your Abstract --';
                                ksort($abstract);
                            @endphp
                            {!! Form::select( 'abstract_id',$abstract,[],
                                ['class' => 'form-control input-sm', 'style' => 'width:90%;float:left']) !!}
                            &nbsp;<i></i>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label">Title</label>
                        <div class="col-md-9" id="text-title" style="padding-top: 7px"></div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label">Authors</label>
                        <div class="col-md-9" id="text-authors" style="padding-top: 7px"></div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label">Abstract Submission</label>
                        <div class="col-md-9" id="text-abstracts" style="padding-top: 7px"></div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label">Full Paper Submission</label>
                        <div class="col-md-9" id="text-paper" style="padding-top: 7px"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script type="text/javascript">
        $(document).on('change', 'select[name="abstract_id"]', function(evt) {
            let c = $(this),
                i = c.siblings('i');
            let url = '{{ route('dashboard.abstract', ['id' => ':id']) }}';
            url = url.replace(':id', c.val());
            vAjax(i, {
                url : url,
                dataType : 'JSON',
                done : function (res) {
                    console.log(res);
                    $('#text-title').html(res.abstract_title);
                    $('#text-authors').html(res.authors);
                    $('#text-abstracts').html(res.abstract_submision);
                    $('#text-paper').html(res.paper_submision);
                }
            });
        });
    </script>
@endpush
