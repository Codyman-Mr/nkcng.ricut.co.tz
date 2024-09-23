<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>Login</title>

        <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
        <link href="{{ asset('font-awesome/css/font-awesome.css') }}" rel="stylesheet">
        <link href="{{ asset('css/style.css') }}" rel="stylesheet">
        <link href="{{ asset('css/animate.css') }}" rel="stylesheet">
    </head>

    <body class="gray-bg">
        <div class="middle-box animated fadeInDown">
            <div class="p-4 p-md-0">
                <div>
                    <h1 class="logo-name text-center" style="color:#F3F3F4;">
                        <img src="{{ asset('/img/logo.png') }}" width="150px" height="150px" alt="Logo">
                    </h1>
                </div>

                <form class="m-t" role="form" action="/login" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="phone_number" class="sr-only">Phone Number</label>
                        <input type="text" class="form-control col-md-12" name="phone_number" id="phone_number" placeholder="Phone Number" value="{{ old('phone_number') }}" autocomplete="off">

                        @error('phone_number')
                            <p style="color:red; font-size:0.9rem; margin-top:8px; line-height:1rem;">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="password" class="sr-only">Password</label>
                        <input type="password" class="form-control col-md-12" name="password" id="password" placeholder="Password" value="{{ old('password') }}" autocomplete="off">

                        @error('password')
                            <p style="color:red; font-size:0.9rem; margin-top:8px; line-height:1rem;">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary block m-b col-md-12">Login</button>
                    Don't have an account ? <a href="/registration">Create Account</a>
                </form>
            </div>
        </div>

        <script src="{{ asset('js/jquery-3.1.1.min.js') }}"></script>
        <script src="{{ asset('js/popper.min.js') }}"></script>
        <script src="{{ asset('js/bootstrap.js') }}"></script>
    </body>
</html>
