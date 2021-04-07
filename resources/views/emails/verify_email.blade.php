@extends('layouts.mail')

@section('content')
<div class="content">

    <div class="header">
        Welcome!
    </div>

    <div class="text">
    <span>
        Yay you registered to use our app! All you need to do is verify your email address by pressing button below.
    </span>

    <span class="button-box">
        <a class="button" href="{{ $url }}">
            Verify email
        </a>
    </span>

    </div>
</div>
@endsection
