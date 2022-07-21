@extends('layouts.modal', ['modalTitle' => $title, 'idForm' => 'fo-tes', 'isLarge' => true, 'isSubmit' => false])

@section('modalBody')
     <strong><h3>{{ $titleEmail }}</h3></strong>
     <br/>
     <br/>
     <div class="panel">
        {!! $body !!}
     </div>
@endsection
