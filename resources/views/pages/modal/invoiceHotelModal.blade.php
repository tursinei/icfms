@extends('layouts.modal', ['modalTitle' => 'Form Invoice Hotel', 'idForm' => 'fo-hotel', 'isLarge' => false])

@section('modalBody')
    @php
        $options = ['class' => 'form-control input-sm', 'placeholder' => '--Pilih Email--', 'list' => 'emails'];
    @endphp
    <div class="form-group">
        {!! Form::hidden('invoice_id', $invoice->invoice_id ?? '') !!}
        <label class="col-sm-4 control-label">Email</label>
        <div class="col-sm-8">
            {!! Form::select('user_id', $listEmail, '', $options) !!}
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-4 control-label">Full Name</label>
        <div class="col-sm-2">
            @php
                unset($options['list']);
                $options['placeholder'] = 'Title';
                $options['readonly'] = 'readonly';
            @endphp
            {!! Form::text('attribut[title]', $invoice->attribut->title ?? '', $options) !!}
        </div>
        <div class="col-sm-6">
            @php
                $options['placeholder'] = 'Full Name';
            @endphp
            {!! Form::text('attribut[fullname]', $invoice->attribut->name ?? '', $options) !!}
        </div>
    </div>
    <div class="form-group">
        @php
            $options['placeholder'] = '';
        @endphp
        <label class="col-sm-4 control-label">Affiliation</label>
        <div class="col-sm-8">
            {!! Form::text('attribut[affiliation]', $invoice->attribut->affiliation ?? '', $options) !!}
        </div>
    </div>
    <div class="form-group">
        <label class="col-md-4 control-label">Country</label>
        <div class="col-md-8">
            {!! Form::text('attribut[country]', $invoice->attribut->country ?? '', $options) !!}
        </div>
    </div>
    @php
        unset($options['readonly']);
    @endphp
    <div class="form-group">
        <label class="col-md-4 control-label">Arrival</label>
        <div class="col-md-8">
            @php
                $options['placeholder'] = '';
            @endphp
            {!! Form::date('attribut[arrival]', $invoice->attribut->arrival ?? '', $options) !!}
        </div>
    </div>
    <div class="form-group">
        <label class="col-md-4 control-label">Departure</label>
        <div class="col-md-8">
            {!! Form::date('attribut[departure]', $invoice->attribut->departure ?? '', $options) !!}
        </div>
    </div>
    <div class="form-group">
        <label class="col-md-4 control-label">Spent</label>
        <div class="col-md-8">
            <div class="input-group">
                {!! Form::text('attribut[night]', $invoice->attribut->night ?? 1, $options) !!}
                <div class="input-group-addon">Nights</div>
            </div>
        </div>
    </div>
    <div class="form-group">
        <label class="col-md-4 control-label">Invoice Date</label>
        <div class="col-md-8">
            {!! Form::date('tgl_invoice', $invoice->tgl_invoice ?? date('Y-m-d'), $options) !!}
        </div>
    </div>
    <div class="form-group">
        <label class="col-md-4 control-label">Invoice Number</label>
        <div class="col-md-8">
            {!! Form::text('invoice_number', $invoice->invoice_number ?? '', $options) !!}
        </div>
    </div>
    <div class="form-group">
        <label class="col-md-4 control-label">Nominal</label>
        <div class="col-md-2">
            @php
                $cur = ['IDR', 'USD'];
                $opt = array_combine($cur, $cur);
                $nominal = empty($invoice->nominal) ? '' : number_format($invoice->nominal ?? '', 0, ',', '.');
            @endphp
            @form_select('currency', $opt, $invoice->currency ?? '', $options)
        </div>
        <div class="col-md-5">
            {!! Form::text('nominal', $nominal, [
                'class' => 'form-control input-sm',
                'id' => 'nominal',
                'placeholder' => '0,-',
            ]) !!}
        </div>
        {!! Form::hidden('jenis', 'hotel') !!}
    </div>
@endsection
