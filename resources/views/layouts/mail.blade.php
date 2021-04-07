<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Mail')</title>
</head>
<body>
    <div class="container">
        @yield('content')
    </div>
</body>
</html>

<style>
    body {
        background: #e7e7e7;
        display: flex;
        justify-content: center;
        align-items: center;
        font-family: "Abyssinica SIL", sans-serif;
    }

    .container {
        display: flex;
        width: 800px;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        min-height: 500px;
    }

    .content {
        background: #ffffff;
        width: 100%;
        min-height: 350px;
        border-radius: 10px;
        padding: 20px;
        box-shadow: rgb(38, 57, 77) 0 20px 30px -10px;
    }

    .header {
        text-transform: capitalize;
        font-size: 55px;
        display: flex;
        justify-content: center;
        align-items: center;
        margin-bottom: 35px;
    }

    .text {
        font-size: 18px;
    }

    .button-box {
        display: flex;
        justify-content: center;
        align-items: center;
        margin-top: 25px;
        margin-bottom: 35px;
    }

    .button {
        text-decoration: none;
        display: block;
        height: 25px;
        background: #118dde;
        padding: 15px;
        text-align: center;
        border-radius: 5px;
        color: white;
        line-height: 25px;
        transition: background 500ms;
    }

    .button:hover {
        background: #00568b;
    }

    .meta-text {
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        height: 55px;
    }

    @media only screen and (max-width: 900px) {
        .container {
            width: 400px;
        }

        .header {
            text-transform: capitalize;
            font-size: 35px;
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 15px;
        }

        .text {
            font-size: 15px;
        }

        .meta-text {
            font-size: 14px;
            height: 90px;
        }
    }

    @media only screen and (max-width: 460px) {
        .container {
            width: 300px;
        }

        .content {
            width: 90%;
        }
    }
</style>

