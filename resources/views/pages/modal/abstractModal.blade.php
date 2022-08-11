@extends('layouts.modal', ['modalTitle' => $title, 'idForm' => 'fo-abstract', 'isLarge' => true])

@section('modalBody')
    @php
    $presentation = ['oral', 'poster', 'audience', 'keynote speaker'];
    $value = array_map('ucwords', $presentation);
    $listPresentation = array_combine($presentation, $value);
    array_unshift($listPresentation, '-- Choose Type --');
    @endphp
    <div class="form-group mt-lg">
        <label class="col-sm-3 control-label">Presentation</label>
        <div class="col-sm-3">
            {!! Form::select('presentation', $listPresentation, [], ['class' => 'form-control input-sm']) !!}
            {!! Form::hidden('user_id', Auth::user()->id) !!}
        </div>
        <div class="col-sm-6">
            {!! Form::text('presenter', '', [
                'class' => 'form-control input-sm',
                'placeholder' => 'Presenter Name',
                'data-role' => 'tagsinput',
            ]) !!}
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-3 control-label">Authors</label>
        <div class="col-sm-9">
            {!! Form::text('authors', '', ['class' => 'form-control input-sm']) !!}
            <small class="text-muted">If author more than one, separate by coma (,)</small>
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-3 control-label">Topic</label>
        <div class="col-sm-9">
            @php
                array_unshift($topic, '-- Choose Topic --');
            @endphp
            {!! Form::select('topic_id', $topic, [], ['class' => 'form-control input-sm']) !!}
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-3 control-label">Abstract Title</label>
        <div class="col-sm-9">
            {!! Form::text('abstract_title', '', ['class' => 'form-control input-sm']) !!}
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-3 control-label">Paper Title</label>
        <div class="col-sm-9">
            {!! Form::text('paper_title', '', ['class' => 'form-control input-sm']) !!}
            <small class="text-mute">Paper Title must be the same as Abstract Title</small>
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-3 control-label">Abstract</label>
        <div class="col-sm-9">
            {!! Form::textarea('abstract', '', ['class' => 'form-control', 'rows' => '', 'cols' => '']) !!}
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-3 control-label">Your Abstract File</label>
        <div class="col-sm-9">
            {!! Form::file('abstract_file', ['class' => 'form-control input-sm']) !!}
            <div class="progress progress-sm progress-striped progress-half-rounded light active" style="margin-bottom: 0">
                <div id="bar-fileprogress" class="progress-bar progress-bar-info" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%;">%
                </div>
            </div>
            <small>
                If you want to revise the abstract, please delete the abstract and then add the revised abstract
            </small>
        </div>
    </div>
@endsection
