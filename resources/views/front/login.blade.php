@extends('layouts.front')

@section('title', 'Login ')

@section('content')
    <div class="center-sign">
        <h2 style="float: left;font-family: 'Noto Sans','Comic Sans MS';color: #0088CC;margin-top:30px;font-weight:bold">
            <span class="alternative-fonts">IcAUMS</span>
        </h2>
        <div class="panel panel-sign">
            <div class="panel-title-sign mt-xl text-right">
                <h2 class="title text-uppercase text-weight-bold m-none"><i class="fa fa-user mr-xs"></i> Sign In</h2>
            </div>
            <div class="panel-body">
                <x-auth-session-status class="alert alert-success" :status="$status??''" />

                <!-- Validation Errors -->
                <x-auth-validation-errors class="alert alert-danger" :errors="$errors" />
                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <div class="form-group mb-lg">
                        <label>Email</label>
                        <div class="input-group input-group-icon">
                            <input name="email" type="text" class="form-control input-lg" value="{{ old('email') }}" />
                            <span class="input-group-addon">
                                <span class="icon icon-lg">
                                    <i class="fa fa-user"></i>
                                </span>
                            </span>
                        </div>
                    </div>

                    <div class="form-group mb-lg">
                        <div class="clearfix">
                            <label class="pull-left">Password</label>
                            <a href="{{ route('password.request') }}" class="pull-right">Lost Password?</a>
                        </div>
                        <div class="input-group input-group-icon">
                            <input name="password" type="password" class="form-control input-lg" />
                            <span class="input-group-addon">
                                <span class="icon icon-lg">
                                    <i class="fa fa-lock"></i>
                                </span>
                            </span>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-8">
                            <div class="checkbox-custom checkbox-default">
                                <input id="RememberMe" name="remember" type="checkbox" />
                                <label for="RememberMe">Remember Me</label>
                            </div>
                        </div>
                        <div class="col-sm-4 text-right">
                            <button type="submit" class="btn btn-primary hidden-xs">Sign In</button>
                            <button type="submit" class="btn btn-primary btn-block btn-lg visible-xs mt-lg">Sign
                                In</button>
                        </div>
                    </div>

                    <span class="mt-lg mb-lg line-thru text-center text-uppercase">
                        <span>or</span>
                    </span>

                    <p class="text-center">Don't have an account yet? <a href="{{ route('register') }}">Sign Up!</a>
                </form>
            </div>
        </div>

        <p class="text-center text-muted mt-md mb-md">&copy; Copyright {{ date('Y') }}. All Rights Reserved.</p>
    </div>
@endsection
