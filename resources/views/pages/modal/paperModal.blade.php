@extends('layouts.modal', ['modalTitle' => $title, 'idForm' => 'fo-paper', 'isLarge' => false])

@section('modalBody')
    <div class="form-group mt-lg">
        <label class="col-sm-3 control-label">Abstract</label>
        <div class="col-sm-9">
            {!! Form::select('abstract_id', $abstract, [], ['class' => 'form-control input-sm list-abstract', 'style' =>'width: 90%;float: left;']) !!}
            {!! Form::hidden('user_id', Auth::user()->id) !!}
            &nbsp;<i></i>
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-3 control-label">Presentation</label>
        <div class="col-sm-9" id='text-presentation'>

        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-3 control-label">Authors</label>
        <div class="col-sm-9" id="text-authors">
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-3 control-label">Topic</label>
        <div class="col-sm-9" id="text-topic">
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-3 control-label">Paper Title</label>
        <div class="col-sm-9">
            {!! Form::text('title', '', ['class' => 'form-control input-sm']) !!}
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-3 control-label">Upload Your Paper</label>
        <div class="col-sm-9">
            {!! Form::file('paper_file', ['class' => 'form-control input-sm']) !!}
            <small>Only file with pdf,doc,docx,odt Extension allowed</small>
        </div>
    </div>
@endsection
