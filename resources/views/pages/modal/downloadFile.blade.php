@extends('layouts.modal', ['modalTitle' => $title, 'idForm' => 'fo-user', 'isLarge' => true])

@section('modalBody')
     <table class="table table-sm table-striped table-bordered table-condensed">
        <thead>
            <tr>
                <th class="text-center" style="width: 10%">No</th>
                <th class="text-center" style="width: 35%">Abstract Title</th>
                <th class="text-center" style="width: 37%">Paper Title</th>
                <th class="text-center" style="width: 15%">Download File</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($abstract as $key => $f)
                {{-- @dd($f) --}}
                <tr>
                    <td class="text-center">{{ $key+1 }}</td>
                    <td>{{ $f->abstract_title }}</td>
                    <td>{{ $f->title }}</td>
                    <td class="text-center">
                        @if (!empty($f->abstract_id))
                        <a href="{{ route('abstract.show',['abstract' => $f->abstract_id??0 ]) }}" target="_blank"
                                title="Download Abstract file" class="btn btn-xs btn-info"><i class="fa fa-download"></i></a>
                        @endif
                        &nbsp;
                        @if (!empty($f->paper_id))
                        <a href="{{ route('fullpaper.show',['fullpaper' => $f->paper_id??0 ]) }}" target="_blank"
                            title="Download Paper file" class="btn btn-xs btn-success"><i class="fa fa-download"></i></a>
                        @endif

                    </td>
                </tr>
            @endforeach
        </tbody>
     </table>
@endsection
