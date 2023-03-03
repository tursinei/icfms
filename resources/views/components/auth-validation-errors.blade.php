@props(['errors'])

@if ($errors->any())
    <div {{ $attributes }}>
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
        <div class="font-medium text-red-600">
            <strong>{{ __('Whoops! Something went wrong.') }}</strong>
        </div>
        <p>{!! $errors->first() !!}</p>
        {{-- <ul class="mt-3 list-disc list-inside text-sm text-red-600">

            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul> --}}
    </div>
@endif
