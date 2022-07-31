@extends('layouts.modal', ['modalTitle' => $title, 'idForm' => 'fo-changePass', 'isLarge' => false])

@section('modalBody')
    <div class="form-group">
        <label class="col-sm-4 control-label">Current Password</label>
        <div class="col-sm-8">
            @form_password('current',['class'=>'form-control input-sm'])
            {!! Form::hidden('id', $user->id) !!}
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-4 control-label">Password</label>
        <div class="col-sm-4">
            {!! Form::password('password', ['class' => 'form-control input-sm']) !!}
        </div>
        <div class="col-sm-4">
            {!! Form::password('password_confirmation', ['class' => 'form-control input-sm', 'placeholder' => 're-Type Password']) !!}
        </div>
    </div>
@endsection
