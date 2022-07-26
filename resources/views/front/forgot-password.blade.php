@extends('layouts.front')

@section('title', 'Forgot Password | ICFMS '.date('Y'))

@section('content')
    <div class="center-sign">
        <a href="/" class="logo pull-left">
            <img src="{{ asset('img/logo.png') }}" height="54" alt="ICFMS 2022" />
        </a>

        <div class="panel panel-sign">
            <div class="panel-title-sign mt-xl text-right">
                <h2 class="title text-uppercase text-weight-bold m-none"><i class="fa fa-briefcase mr-xs"></i> Forgot Password</h2>
            </div>
            <div class="panel-body">
                <x-auth-session-status class="alert alert-success" :status="session('status')"/>

                <!-- Validation Errors -->
                <x-auth-validation-errors class="alert alert-danger" :errors="$errors" />
                <form method="POST" action="{{ route('password.email') }}" class="mb-lg">
                    @csrf
                    <div class="form-group mb-lg">
                        <label> Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one. </label>
                        <div class="input-group input-group-icon">
                            <input name="email" type="text" class="form-control input-lg" value="{{ old('email') }}" />
                            <span class="input-group-addon">
                                <span class="icon icon-lg">
                                    <i class="fa fa-email"></i>
                                </span>
                            </span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <button type="submit" class="btn btn-primary btn-block">{{ __('Email Password Reset Link') }}</button>
                        </div>
                    </div>
                </form>
                <p class="text-center">Back to <a href="{{ route('login') }}">Login</a>
            </div>
        </div>

        <p class="text-center text-muted mt-md mb-md">&copy; Copyright 2022. All Rights Reserved.</p>
    </div>
@endsection
