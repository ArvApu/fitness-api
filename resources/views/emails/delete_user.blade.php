@extends('layouts.mail')

@section('content')
<div class="content">

    <div class="header">
        Account deleted
    </div>

    <div class="text">
        <span>
            We inform you that you can no longer use {{ $app }}, because your account has been deleted.
        </span>
    </div>

</div>
@endsection
