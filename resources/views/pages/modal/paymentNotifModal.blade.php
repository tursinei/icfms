@extends('layouts.modal', ['modalTitle' => 'Form Payment Notification', 'idForm' => 'fo-payment', 'isLarge' => false])

@section('modalBody')
    @php
        $options = ['class' => 'form-control input-sm', 'placeholder' => '-- Pilih Invoice --',
                    'list' => 'invoices', 'id' => 'invoice_id'];
    @endphp
    <div class="form-group">
        {!! Form::hidden('paymentnotif_id', $payment->paymentnotif_id ?? '') !!}
        <label class="col-sm-4 control-label">Invoice No.</label>
        <div class="col-sm-8">
            {!! Form::select('invoice_id', $invoices, $payment->invoice_id??'', $options) !!}
            {{-- {!! Form::text('invoice_id', '', $options) !!}
            <datalist id="invoices">
                @foreach ($invoices as $key => $in)
                    <option name="{{ $in }}" data-id="{{ $key }}" value="{{ $in }}" />
                @endforeach
            </datalist> --}}
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-4 control-label">Full Name</label>
        <div class="col-sm-2">
            @php
                unset($options['id'],$options['list']);
                $options['placeholder'] = 'Title';
                $options['readonly'] = 'readonly';
            @endphp
            {!! Form::text('attribut[title]', $payment->attribut->title ?? '', $options) !!}
        </div>
        <div class="col-sm-6">
            @php
                $options['placeholder'] = 'Full Name';
            @endphp
            {!! Form::text('attribut[fullname]', $payment->attribut->name ?? '', $options) !!}
        </div>
    </div>
    <div class="form-group">
        @php
            $options['placeholder'] = '';
        @endphp
        <label class="col-sm-4 control-label">Affiliation</label>
        <div class="col-sm-8">
            {!! Form::text('attribut[affiliation]', $payment->attribut->affiliation ?? '', $options) !!}
        </div>
    </div>
    <div class="form-group">
        <label class="col-md-4 control-label">Country</label>
        <div class="col-md-8">
            {!! Form::text('attribut[country]', $payment->attribut->country ?? '', $options) !!}
        </div>
    </div>
    <div class="form-group">
        <label class="col-md-4 control-label">Role</label>
        <div class="col-md-8">
            {!! Form::text('attribut[role]', $payment->attribut->role ?? '', $options) !!}
        </div>
    </div>
    <div class="form-group">
        <label class="col-md-4 control-label">Abstract Title</label>
        <div class="col-md-8">
            {!! Form::text('attribut[abstract_title]', $payment->attribut->abstract_title ?? '', $options) !!}
        </div>
    </div>
    <div class="form-group">
        <label class="col-md-4 control-label">Nominal</label>
        <div class="col-md-2">
            @php
                $cur = ['IDR', 'USD'];
                $opt = array_combine($cur, $cur);
                $nominal = empty($payment->nominal) ? '' : number_format($payment->nominal ?? '', 0, ',', '.');
            @endphp
            @form_select('attribut[currency]', $opt, $payment->attribut->currency ?? '', $options)
        </div>
        <div class="col-md-5">
            {!! Form::text('nominal', $nominal, [
                'class' => 'form-control input-sm',
                'id' => 'nominal',
                'placeholder' => '0,-',
                'readonly'=> 'readonly'
            ]) !!}
        </div>
    </div>
    <div class="form-group">
        <label class="col-md-4 control-label">Payment Date</label>
        <div class="col-md-8">
            @php
                unset($options['readonly']);
            @endphp
            {!! Form::date('payment_tgl', $payment->payment_tgl ?? date('Y-m-d'), $options) !!}
        </div>
    </div>
@endsection
