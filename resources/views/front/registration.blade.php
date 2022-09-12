@extends('layouts.front')

@section('title', 'Registration ICFMS 2022')

@section('content')
    <div class="center-sign">
        <a href="/" class="logo pull-left">
            <h3>Registration Form</h3>
        </a>
        <div class="panel panel-sign">
            <div class="panel-title-sign mt-xl text-right">
                <h2 class="title text-uppercase text-weight-bold m-none"><i class="fa fa-user mr-xs"></i> Sign Up</h2>
            </div>
            <div class="panel-body">
                <x-auth-validation-errors class="alert alert-danger" :errors="$errors" />
                <form id="fo-register" action="{{ route('register') }}" method="post">
                    @csrf
                    @php
                        $title = ['Dr.', 'Prof.', 'Mr.','Mrs.'];
                        $optTitle = array_combine($title, $title);
                    @endphp
                    <fieldset>
                        <legend>Personal Details</legend>
                        <div class="form-group">
                            <label>Title</label>
                            {!! Form::select('title', $optTitle,old('title'),['class' => 'form-control input-sm']) !!}
                        </div>
                        <div class="form-group">
                            <label>First Name</label>
                            <input name="firstname" type="text" class="form-control input-sm" value="{{ old('firstname') }}" required>
                        </div>
                        <div class="form-group">
                            <label>Midle Name (Optional)</label>
                            <input name="midlename" type="text" class="form-control input-sm" value="{{ old('midlename') }}">
                        </div>
                        <div class="form-group">
                            <label>Last Name</label>
                            <input name="lastname" type="text" class="form-control input-sm" value="{{ old('lastname') }}" required>
                        </div>
                    </fieldset>
                    <fieldset class="mt-lg mb-lg">
                        <legend>Contact Details</legend>
                        <div class="form-group">
                            <label>Main Email</label>
                            <input type="text" class="form-control input-sm" name="email" value="{{ old('email') }}">
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Password</label>
                                    <input name="password" type="password" class="form-control input-sm" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Re-Type Password</label>
                                    <input name="password_confirmation" id="re-password" type="password" class="form-control input-sm">
                                </div>
                            </div>
                        </div>
                        <div class="form-group mt-sm">
                            <label>2<sup>nd</sup>Email (optional)</label>
                            <input name="secondemail" type="email" class="form-control input-sm" value="{{ old('secondemail') }}">
                        </div>
                        <div class="form-group">
                            <label>Affiliation</label>
                            @form_select('affiliation',$afiliations,old('affiliation'),['class' => 'form-control input-sm'])
                            {{-- <input name="affiliation" type="text" class="form-control input-sm" value="{{ old('affiliation') }}"> --}}
                        </div>
                        <div class="form-group">
                            <label>Address (Optional)</label>
                            <textarea name="address" id="" class="form-control">{{ old('address') }}</textarea>
                        </div>
                        <div class="form-group">
                            <label>Country</label>
                            @php
                                $country = array_combine($negara, $negara);
                                $country = array_merge(['' => '-- Choose Country --'],$country);
                            @endphp
                            {!! Form::select('country', $country, old('country'), ['class' =>'form-control']) !!}
                        </div>
                        <div class="row mb-md">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Mobile Number</label>
                                    <input value="{{ old('mobilenumber') }}" name="mobilenumber" type="text" class="form-control input-sm">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Phone Number</label>
                                    <input name="phonenumber" value="{{ old('phonenumber') }}" type="text" class="form-control input-sm">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-offset-8 col-md-4 text-right">
                                <button type="button" id="btn-submit" class="btn btn-primary">Sign Up</button>
                            </div>
                        </div>
                    </fieldset>

                    <p class="text-center">Already have an account? <a href="/">Sign In!</a></p>
                </form>
            </div>
        </div>

        <p class="text-center text-muted mt-md mb-md">&copy; Copyright 2022. All Rights Reserved.</p>
    </div>
@endsection
@once
@push('js')
    <script type="text/javascript">
        $(document).on('click','#btn-submit',function(evt) {
            let re = $('#re-password'), pas = $('input[name="password"]');
            if(pas.val() == ''){
                alert('Password Still Empty');
                pas.focus();
            }else if(pas.val() != re.val()){
                alert('Password not match');
                re.val('');
                pas.focus();
            }else{
                $('form#fo-register').submit();
            }
        });
    </script>
@endpush
@endonce
