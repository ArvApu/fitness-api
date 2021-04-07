@extends('layouts.mail')

@section('content')
<div class="content">

    <div class="header">
        An invitation
    </div>

    <div class="text">
    <span>
        You have been invited by {{ $inviter }} to train using our app. Press button below to join.
    </span>

        <span class="button-box">
        <a class="button" href="{{ $url }}">
            Join!
        </a>
    </span>

    </div>
</div>
@endsection
