@extends('layouts.front')

@section('title', 'Login ICFMS 2022')

@section('content')
    <div class="center-sign">
        <a href="/" class="logo pull-left">
            <img src="{{ asset('img/logo.png') }}" height="54" alt="ICFMS 2022" />
        </a>

        <div class="panel panel-sign">
            <div class="panel-title-sign mt-xl text-right">
                <h2 class="title text-uppercase text-weight-bold m-none"><i class="fa fa-key mr-xs"></i> Reset Password</h2>
            </div>
            <div class="panel-body">
                <!-- Session Status -->
                <x-auth-session-status class="alert alert-success" :status="0" />

                <!-- Validation Errors -->
                <x-auth-validation-errors class="alert alert-danger" :errors="$errors" />
                <form method="POST" action="{{ route('password.update') }}">
                    @csrf
                    @form_hidden('token',$request->route('token'))
                    @form_hidden('email',$request->email??old('email'))
                    <div class="form-group mb-lg">
                        <div class="input-group input-group-icon">
                            <input name="password" placeholder="New Password" type="password" class="form-control input-lg" required />
                            <span class="input-group-addon">
                                <span class="icon icon-lg">
                                    <i class="fa fa-key"></i>
                                </span>
                            </span>
                        </div>
                    </div>

                    <div class="form-group mb-lg">
                        <div class="input-group input-group-icon">
                            <input name="password_confirmation" placeholder="Re-type New Password" type="password" class="form-control input-lg" required/>
                            <span class="input-group-addon">
                                <span class="icon icon-lg">
                                    <i class="fa fa-lock"></i>
                                </span>
                            </span>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-12 text-right">
                            <button type="submit" class="btn btn-primary hidden-xs">{{ __('Reset Password') }}</button>
                            <button type="submit" class="btn btn-primary btn-block btn-lg visible-xs mt-lg">{{ __('Reset Password') }}</button>
                        </div>
                    </div>

                    <span class="mt-lg mb-lg line-thru text-center text-uppercase">
                        <span>or</span>
                    </span>

                    <p class="text-center">Don't have an account yet? <a href="{{ route('register') }}">Sign Up!</a>
                </form>
            </div>
        </div>

        <p class="text-center text-muted mt-md mb-md">&copy; Copyright 2022. All Rights Reserved.</p>
    </div>
@endsection
