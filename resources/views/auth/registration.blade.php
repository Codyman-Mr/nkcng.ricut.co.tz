<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>Registration</title>

        <link href={{asset('css/bootstrap.min.css')}} rel="stylesheet">
        <link href={{asset('font-awesome/css/font-awesome.css')}} rel="stylesheet">

        <link href={{asset('css/style.css')}} rel="stylesheet">
        <link href={{asset('css/animate.css')}} rel="stylesheet">

        <script src={{asset('js/jquery-3.1.1.min.js')}}></script>
        <script src={{asset('js/popper.min.js')}}></script>
        <script src={{asset('js/bootstrap.js')}}></script>
    </head>

    <body class="gray-bg">
        <div class="middle-box animated fadeInDown">
            <div class="p-4 p-md-0">
                <div class="row" style="justify-content:center;">
                    <img src="{{asset('/img/logo.png')}}" width="110px" height="110px" alt="">
                </div>

                <form class="m-t" role="form" action="{{route('create-user')}}"  method="POST">
                    @csrf
                    <div class="form-group">
                        @error('other_errors')
                            <p style="color:red; font-size:0.9rem; margin-top:8px; line-height:1rem;">{{$message}}</p>
                        @enderror

                        <label>First Name</label>
                        <input type="text" class="form-control" name="first_name" id="first_name" value="{{old('first_name')}}">

                        @error('first_name')
                            <p style="color:red; font-size:0.9rem; margin-top:8px; line-height:1rem;">{{$message}}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label>Last Name</label>
                        <input type="text" class="form-control" name="last_name" id="last_name" value="{{old('last_name')}}">

                        @error('last_name')
                            <p style="color:red; font-size:0.9rem; margin-top:8px; line-height:1rem;">{{$message}}</p>
                        @enderror
                    </div>


                    <div class="form-group">
                        <label>Phone Number</label>
                        <input type="text" class="form-control" name="phone_number" id="phone_number" value="{{old('phone_number')}}">

                        @error('phone_number')
                            <p style="color:red; font-size:0.9rem; margin-top:8px; line-height:1rem;">{{$message}}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" class="form-control" name="password" id="password" value="{{old('password')}}">

                        @error('password')
                            <p style="color:red; font-size:0.9rem; margin-top:8px; line-height:1rem;">{{$message}}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label>Confirm Password</label>
                        <input type="password" class="form-control @error('password_confirmation') border-red-500 @enderror" name="password_confirmation" id="password_confirmation" value="{{old('password_confirmation')}}">

                        @error('password_confirmation')
                            <p style="color:red; font-size:0.9rem; margin-top:8px; line-height:1rem;">{{$message}}</p>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary block full-width m-b">Register</button>
                    Already a member ? <a href="/login">Login</a>
                </form>
            </div>
        </div>
    </body>
</html>
