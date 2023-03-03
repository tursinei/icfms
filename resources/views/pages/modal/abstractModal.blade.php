@extends('layouts.modal', ['modalTitle' => $title, 'idForm' => 'fo-abstract', 'isLarge' => true])

@section('modalBody')
    @php
    $value = array_map('ucwords', $presentation);
    $listPresentation = array_combine($presentation, $value);
    $listPresentation = array_merge(['' =>'-- Choose Type --'],$listPresentation);
    @endphp
    <div class="form-group mt-lg">
        <label class="col-sm-3 control-label">Your Role</label>
        <div class="col-sm-3">
            {!! Form::select('presentation', $listPresentation, $userRole, ['class' => 'form-control input-sm']) !!}
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
                $topic[''] = '-- Choose Topic --';
                ksort($topic);
            @endphp
            {!! Form::select('topic_id', $topic, [], ['class' => 'form-control input-sm']) !!}
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-3 control-label">Abstract Title</label>
        <div class="col-sm-9">
            {!! Form::text('abstract_title', '', ['class' => 'form-control input-sm']) !!}
        </div>
        <div class="col-sm-9 col-md-offset-3">
            <div class="checkbox">
                <label>
                    {!! Form::checkbox('sama', '', false,['id'=>'cektitle']) !!}
                    Check this option if you are sure that your abstract
                    title is the same as the title of your full paper
                </label>
            </div>
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-3 control-label">Paper Title</label>
        <div class="col-sm-9">
            {!! Form::text('paper_title', '', ['class' => 'form-control input-sm']) !!}
            {{-- <small class="text-mute">Paper Title must be the same as Abstract Title</small><br>
            <small class="text-mute">If you don't want to include a Full Paper, please keep the Paper Title filled with the same Title as the Abstract Title</small> --}}
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-3 control-label">Presentation Only</label>
        <div class="col-sm-9">
            <label class="radio-inline">
                {!! Form::radio('is_presentation', 1, false) !!} Yes</label>
            <label class="radio-inline">
                {!! Form::radio('is_presentation', 0, true) !!} No</label>
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-3 control-label">Your Abstract File</label>
        <div class="col-sm-9">
            {!! Form::file('abstract_file', ['class' => 'form-control input-sm']) !!}
            <div class="progress progress-sm progress-striped progress-half-rounded light active" style="margin-bottom: 0">
                <div id="bar-fileprogress" class="progress-bar progress-bar-info" role="progressbar" aria-valuenow="0"
                    aria-valuemin="0" aria-valuemax="100" style="width: 0%;">%
                </div>
            </div>
            <small>
                Only file with pdf,doc,docx,odt Extension allowed (max. 3 MB)
            </small>
        </div>
    </div>
@endsection
