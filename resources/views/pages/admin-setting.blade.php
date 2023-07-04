@extends('layouts.master')

@section('title', 'Setting')
@section('title2', 'Setting')

@section('content')
    <div class="row">
        <div class="panel">
            <div class="panel-body">
                <form id="fo-settings" class="form-horizontal">
                    @php
                        $options = ['class' => 'form-control input-sm', 'placeholder' => 'Batas maksimal upload abstract'];
                    @endphp
                    <div class="form-group">
                        <label class="col-md-3 control-label">Batas Tanggal Upload Abstract</label>
                        <div class="col-md-5 ">
                            {!! Form::date("setting[tgl_bts_abstract]", $data['tgl_bts_abstract'] ?? '', $options) !!}
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label">Message Tanggal Upload Abstract</label>
                        <div class="col-md-5 ">
                            @php
                                $options['placeholder'] = 'Message info batas maksimal abstract';
                                $options['rows'] = '2';
                            @endphp
                            {!! Form::textarea("setting[msg_bts_abstract]", $data['msg_bts_abstract'] ?? '', $options) !!}
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label">Batas Tanggal Upload Full Paper</label>
                        <div class="col-md-5 ">
                            {!! Form::date("setting[tgl_bts_paper]", $data['tgl_bts_paper'] ?? '', $options) !!}
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label">Message Tanggal Upload Full Paper</label>
                        <div class="col-md-5 ">
                            @php
                                $options['placeholder'] = 'Message info batas maksimal Full Paper';
                                $options['rows'] = '2';
                            @endphp
                            {!! Form::textarea("setting[msg_bts_paper]", $data['msg_bts_paper'] ?? '', $options) !!}
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-9 text-right">
                            <button type="submit" class="btn btn-sm btn-success"><i
                                    class="fa fa-save"></i>&nbsp;Save</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script type="text/javascript">
        $(document).on('submit', '#fo-settings', function(e) {
            e.preventDefault();
            let b = $(this);
            vAjax(b.find('i'), {
                url: '{{ route('setting.store') }}',
                data : b.serializeArray(),
                type: 'POST',
                dataType: 'JSON',
                done: function(params) {
                    msgSuccess(params.message);
                }
            });
        });
    </script>
@endpush
