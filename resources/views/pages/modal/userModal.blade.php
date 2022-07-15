@extends('layouts.modal', ['modalTitle' => $title, 'idForm' => 'fo-paper', 'isLarge' => false])

@section('modalBody')
    <div class="form-group">
        <label class="col-sm-4 control-label">Email / Username</label>
        <div class="col-sm-8">
            {!! Form::text('email', $user->email ?? '', ['class' => 'form-control input-sm']) !!}
            {!! Form::hidden('id', $user->id??0) !!}
            &nbsp;<i></i>
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-4 control-label">Password</label>
        <div class="col-sm-4">
            {!! Form::password('password', ['class' => 'form-control input-sm']) !!}
        </div>
        <div class="col-sm-4">
            {!! Form::password('re-password', ['class' => 'form-control input-sm', 'placeholder' => 're-Type Password']) !!}
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-4 control-label">Name</label>
        <div class="col-sm-8" >
            {!! Form::text('name', $user->name ?? '', ['class' => 'form-control input-sm']) !!}
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-4 control-label">Affiliation</label>
        <div class="col-sm-8">
            {!! Form::text('affiliation', $user->affiliation??'', ['class' => 'form-control input-sm']) !!}
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-4 control-label">Phone Number</label>
        <div class="col-sm-8">
            <input type="phone" class="form-control input-sm" value="{{ $user->phonenumber??'' }}"/>
        </div>
    </div>
@endsection
