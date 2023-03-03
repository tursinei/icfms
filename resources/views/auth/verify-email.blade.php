@extends('layouts.front')

@section('title', 'Verification Email ')

@section('content')
    <div class="center-sign">
        {{-- <a href="/" class="logo pull-left">
            <h3>Verification Email</h3>
        </a> --}}
        <h2 style="float: left;font-family: 'Noto Sans','Comic Sans MS';color: #0088CC;margin-top:30px;font-weight:bold;">
            <a style="text-decoration:none;" href="{{ route('login') }}"><span class="alternative-fonts">IcAUMS</span></a>
        </h2>
        <div class="panel panel-sign">
            <div class="panel-title-sign mt-xl text-right">
                <h2 class="title text-uppercase text-weight-bold m-none"><i class="fa fa-envelope-o mr-xs"></i> Verification Email</h2>
            </div>
            <div class="panel-body">
                <div class="alert alert-info" >
                     {{ __('Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn\'t receive the email, we will gladly send you another.') }}
                </div>
                 @if (session('status') == 'verification-link-sent')
                    <div class="alert alert-success">
                        {{ __('A new verification link has been sent to the email address you provided during registration.') }}
                    </div>
                @endif
                <form method="POST" action="{{ route('verification.send') }}">
                    @csrf
                    <button class="btn btn-block btn-primary btn-md pt-sm pb-sm text-md">
                        {{ __('Resend Verification Email') }}
                    </button>
                </form>
            </div>
        </div>

        <p class="text-center text-muted mt-md mb-md">&copy; Copyright {{ date('Y') }}. All Rights Reserved.</p>
    </div>
@endsection
