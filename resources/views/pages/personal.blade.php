@extends('layouts.master')

@section('title', 'Personal Detail | ICFMS ' . date('Y'))
@section('title2', 'Personal Detail')

@section('content')
    @php
        $title = ['Dr.', 'Prof.', 'Mr.', 'Mrs.'];
        $optTitle = array_combine($title, $title);
    @endphp
    <div class="row">
        <div class="panel">
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-10">
                        <x-auth-session-status class="alert alert-success" :status="$status ?? ''" />
                        <!-- Validation Errors -->
                        <x-auth-validation-errors class="alert alert-danger" :errors="$errors" />
                        <form action="{{ route('personal.store') }}" method="POST" class="form-horizontal">
                            @csrf
                            {!! Form::hidden('user_id', $user->user_id) !!}
                            <div class="form-group">
                                <label class="control-label col-sm-3">Title </label>
                                <div class="col-sm-3">
                                    {!! Form::select('title', $optTitle, $user->title, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-sm-3">Name</label>
                                <div class="col-sm-3">
                                    {!! Form::text('firstname', $user->firstname, ['placeholder' => 'First name', 'class' => 'form-control']) !!}
                                </div>
                                <div class="col-sm-3">
                                    {!! Form::text('midlename', $user->midlename, [
                                        'placeholder' => 'Middle name (Optional)',
                                        'class' => 'form-control',
                                    ]) !!}
                                </div>
                                <div class="col-sm-3">
                                    {!! Form::text('lastname', $user->lastname, ['placeholder' => 'Last name', 'class' => 'form-control']) !!}
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-sm-3">Affiliation</label>
                                <div class="col-sm-6">
                                    @php
                                        $valueAffiliation   = $user->affiliation;
                                        $isHidden           = 'hidden';
                                        $valueAnother       = '';
                                        if ($isOtherAffiliation OR old('affiliation') == 'Another') {
                                            $isHidden           = '';
                                            $valueAffiliation   = 'Another';
                                            $valueAnother       = old('affiliation') == 'Another' ? '' : $user->affiliation;
                                        }
                                    @endphp
                                    {!! Form::select('affiliation', $affiliations, $valueAffiliation , ['class' => 'form-control input-sm']) !!}
                                    {!! Form::text('another_affiliation', $valueAnother, [
                                        'class' => 'form-control input-sm ' . $isHidden,
                                        'style' => 'margin-top:5px',
                                        'placeholder' => 'Input Another Affiliation Name',
                                    ]) !!}
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-sm-3">Address (Optional)</label>
                                <div class="col-sm-6">
                                    <textarea name="address" class="form-control">{{ $user->address }}</textarea>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-sm-3">Country</label>
                                <div class="col-sm-6">
                                    @php
                                        $listCountry = array_combine($country, $country);
                                        array_unshift($listCountry, '-- Choose Country --');
                                    @endphp
                                    {!! Form::select('country', $listCountry, $user->country, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-sm-3">Main Email</label>
                                <div class="col-sm-6">
                                    <input type="email" name="email" class="form-control"
                                        value="{{ Auth::user()->email }}">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-sm-3">2<sup>nd</sup> Email (Optional)</label>
                                <div class="col-sm-6">
                                    <input type="email" name="secondemail" class="form-control"
                                        value="{{ $user->secondemail }}">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-sm-3">Contact Number</label>
                                <div class="col-sm-3">
                                    {!! Form::text('mobilenumber', $user->mobilenumber, [
                                        'placeholder' => 'Mobile Number',
                                        'class' => 'form-control',
                                    ]) !!}
                                </div>
                                <div class="col-sm-3">
                                    {!! Form::text('phonenumber', $user->phonenumber, ['placeholder' => 'Phone Number', 'class' => 'form-control']) !!}
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-9 text-right">
                                    <button type="submit" class="btn btn-sm btn-success"><i
                                            class="fa fa-save"></i>&nbsp;Save</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('js')
    <script type="text/javascript">
        $(document).on('change', 'select[name="affiliation"]', function(params) {
            let c = $(this),
                anotherInput = c.siblings('input[name="another_affiliation"]');
            if (c.val() == 'Another') {
                anotherInput.removeClass('hidden');
            } else {
                anotherInput.addClass('hidden');
            }
        });
    </script>
@endpush
