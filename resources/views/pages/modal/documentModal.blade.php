@extends('layouts.modal', ['modalTitle' => 'Form Add Document', 'idForm' => 'fo-document', 'isLarge' => false])

@section('modalBody')
    <div class="form-group">
        <label class="col-sm-4 control-label">Upload file</label>
        <div class="col-sm-8">
            {!! Form::hidden('id', '') !!}
            {!! Form::file("path_file",['class' => 'form-control input-sm']) !!}
            <small class="text-info">Only <strong>PDF</strong> file are allowed</small>
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-4 control-label">Document Name</label>
        <div class="col-sm-8">
            {!! Form::text('nama', '',['class' => 'form-control input-sm']) !!}
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-4 control-label">For Users</label>
        <div class="col-sm-8">
            {!! Form::select('', $listEmail, '', ['id' => 'user-doc', 'class' => 'form-control input-sm', 'placeholder' => '--Pilih Email--',
                    'list' => 'emails']) !!}
        </div>
    </div>
    <div class="form-group">
        <div class="col-sm-12" id="selected-email">

        </div>
    </div>
@endsection
