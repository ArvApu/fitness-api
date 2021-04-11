@extends('layouts.mail')

@section('content')
    <div class="content">

        <div class="header">
            Event reminder
        </div>

        <div class="text">
        <span>
            Do not forget that you have upcoming "{{ $title }}" event tomorrow. You can see it by pressing button below:
        </span>

        <span class="button-box">
            <a class="button" href="{{ $url }}">
                Go to event
            </a>
        </span>

        </div>

    </div>
@endsection
