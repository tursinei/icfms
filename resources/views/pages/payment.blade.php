@extends('layouts.master')

@section('title', 'Payment')
@section('title2', 'Payment')

@section('content')
    <div class="row">
        <div class="panel">
            <div class="panel-body">
                <form id="fo-payment" class="form-horizontal">
                    <div class="form-group">
                        <label class="col-md-3 control-label">Name</label>
                        <div class="col-md-5 "style="padding-top: 7px">{{ Auth::user()->name }}</div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label">Affiliation</label>
                        <div class="col-md-5 "style="padding-top: 7px">{{ $users->affiliation }}</div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label">Number of Abstracts</label>
                        <div class="col-md-5 "style="padding-top: 7px">{{ $totalAbstract }}</div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label">Number of Papers</label>
                        <div class="col-md-5 "style="padding-top: 7px">{{ $totalPaper }}</div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label">Nominal</label>
                        <div class="col-md-2">
                            @php
                                $cur = ['IDR','USD'];
                                $opt = array_combine($cur,$cur);
                                $nominal = empty($payment->nominal)?'':number_format($payment->nominal, 0,',','.');
                            @endphp
                            @form_hidden('user_id',$users->user_id??'0')
                            @form_hidden('payment_id',$payment->payment_id??'0')
                            @form_select('currency',$opt,$payment->currency??'',['class' => 'form-control input-sm'])
                        </div>
                        <div class="col-md-3">
                            {!! Form::text('nominal', $nominal, ['class' =>'form-control input-sm', 'id'=>'nominal', 'placeholder' => '0,-']) !!}
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">Upload Your Payment Note</label>
                        <div class="col-sm-5">
                            <div class="btn-group @if(!$isFileUploaded) hidden @endif">
                                <a id="a-view" href="{{ route('payment.show',['payment' => $payment->payment_id??0, 'action' => 'view']) }}" target="_blank" class="btn btn-primary btn-sm">View File</a>
                                <button type="button" id="btn-removeFile" title="Change Payment File" class="btn btn-danger btn-sm">
                                    <i class="fa fa-rotate-left"></i>
                                </button>
                            </div>
                            <div id="dv-upload" class="@if($isFileUploaded) hidden @endif">
                            {!! Form::file('note', ['class' => 'form-control input-sm']) !!}
                                <div class="progress progress-sm progress-striped progress-half-rounded light active" style="margin-bottom: 0">
                                    <div id="bar-fileprogress" class="progress-bar progress-bar-info" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%;">%
                                    </div>
                                </div>
                                <small>Only file with pdf,jpg,jpeg,png Extension allowed</small>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">Send Payment Confirmation by Email</label>
                        <div class="col-md-3">
                            <div class="radio-inline">
                                <label>
                                    {!! Form::radio('send_confirm', 1, true) !!}
                                    Yes
                                </label>
                            </div>
                            <div class="radio-inline">
                                <label>
                                    {!! Form::radio('send_confirm', 0, false) !!}
                                    No
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-9 text-right">
                            <button type="submit" class="btn btn-sm btn-success"><i class="fa fa-save"></i>&nbsp;Save</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('js')
<script src="{{ asset('js/jquery.maskMoney.min.js') }}"></script>
<script type="text/javascript">
$(document).ready(function(e) {
    $('input[name="nominal"]').maskMoney({
        'precision' : 0,
        thousands : '.'
    });
}).on('submit','#fo-payment', function(e){
    e.preventDefault();
    let fo = $(this), i = fo.find('button[type="submit"] > i');
    let dataForm = toFormData(this);
    vAjax(i, {
        url : '{{ route('payment.store') }}',
        data : dataForm,
        type : 'POST',
        processData : false,
        contentType : false,
        dataType : 'JSON',
        done : function(res) {
            $('#a-view').attr('href', res.url);
            $('input[name="payment_id"]').val(res.id);
            let dv = $('#dv-upload');
            dv.prev('div').removeClass('hidden');
            dv.addClass('hidden');
            $('input[name="note"]').val('');
            $('#bar-fileprogress').attr('aria-valuenow', 0).css('width',0+'%').html(0+'%');
            msgSuccess(res.message);
        },
        async : true,
        xhr : function(evt) {
            let xhr = new window.XMLHttpRequest();
            $('#bar-fileprogress').attr('aria-valuenow', 0).css('width',0+'%').html(0+'%');
            if(fo.find('input[type="file"]')[0].files.length == 0){
                return xhr;
            }
            xhr.upload.onprogress = function(e) {
                if(e.lengthComputable){
                    let percent = Math.round((e.loaded / e.total) * 100);
                    $('#bar-fileprogress').attr('aria-valuenow', percent).css('width',percent+'%').html(percent+'%');
                }
            }
            return xhr;
        }
    });
}).on('click','#btn-removeFile', function(params) {
    let divParent = $(this).parent(), divUpload = divParent.next('div#dv-upload');
    divParent.addClass('hidden');
    divUpload.removeClass('hidden');
});
</script>
@endpush
