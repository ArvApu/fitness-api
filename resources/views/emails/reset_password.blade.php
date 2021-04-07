@extends('layouts.mail')

@section('content')
<div class="content">

    <div class="header">
        Password reset
    </div>

    <div class="text">
        <span>
            You recently requested to reset the password for your {{ $app }} account. Click the button below to proceed.
        </span>

            <span class="button-box">
            <a class="button" href="{{ $url }}">
                Reset my password
            </a>
        </span>

        <span class="meta-text">
        <span>If you did not request a password reset, please ignore this email.</span>
        <span>This password reset link is only valid for the next {{ $expiration }} minutes.</span>
    </span>

    </div>
</div>
@endsection
