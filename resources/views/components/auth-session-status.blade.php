
@if (session('success'))
<div {{ $attributes }}>
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
    {{ session('success') }}
</div>
@endif

@props(['status'])

@if ($status)
<div {{ $attributes }}>
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
    {{ $status }}
</div>
@endif
