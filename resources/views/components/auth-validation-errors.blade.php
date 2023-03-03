@props(['errors'])

@if ($errors->any())
    <div {{ $attributes }}>
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
        <div class="font-medium text-red-600">
            <h4>{{ __('Whoops! Something went wrong.') }}</h4>
        </div>
        <p>{!! $errors->first() !!}</p>
        {{-- <ul class="mt-3 list-disc list-inside text-sm text-red-600">

            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul> --}}
    </div>
@endif
