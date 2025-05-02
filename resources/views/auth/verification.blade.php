<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Verification</title>
    <link href={{asset('css/bootstrap.min.css')}} rel="stylesheet">
    <link href={{asset('css/verification.css')}} rel="stylesheet">

    <script src={{asset('js/jquery-3.1.1.min.js')}}></script>
  </head>
  <body>
    <div class="container height-100">
        <div class="position-relative">
            <div class="card p-2 text-center m-4">
                <div class="row">
                    <img src="{{asset('/img/logo.png')}}" width="110px" height="110px" alt="">
                </div>

                <span>To complete the registration process, please enter the verification code sent to {{$user->phone_number}}</span>

                <form action="{{route('verify.otp',$user)}}" method="POST">
                    @csrf
                    <div id="otp" class="inputs d-flex flex-row justify-content-center mt-2">
                        <input class="m-2 text-center form-control rounded" type="text" name="otp[]"  id="first" maxlength="1" />
                        <input class="m-2 text-center form-control rounded" type="text" name="otp[]"  id="second" maxlength="1" />
                        <input class="m-2 text-center form-control rounded" type="text" name="otp[]"  id="third" maxlength="1" />
                        <input class="m-2 text-center form-control rounded" type="text" name="otp[]"  id="fourth" maxlength="1" />
                    </div>

                    @error('verification_error')
                        <span class="text-danger font-weight-normal">{{$message}}</span>
                    @enderror

                    <div class="mt-4">

                    <button class="btn btn-sm btn-outline-info validate" type="submit">Verify</button> </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            function OTPInput() {
                const inputs = $("#otp > *[id]");
                inputs.each(function (index) {
                    $(this).on("keydown", function (event) {
                        if (event.key === "Backspace") {
                            $(this).val("");
                            if (index !== 0) $(inputs[index - 1]).focus();
                        } else {
                            if (index === inputs.length - 1 && $(this).val() !== "") {
                                return true;
                            } else if (event.keyCode > 47 && event.keyCode < 58) {
                                $(this).val(event.key);
                                if (index !== inputs.length - 1)
                                    $(inputs[index + 1]).focus();
                                    event.preventDefault();
                            } else if (event.keyCode > 64 && event.keyCode < 91) {
                                $(this).val(String.fromCharCode(event.keyCode));
                                if (index !== inputs.length - 1)
                                    $(inputs[index + 1]).focus();
                                    event.preventDefault();
                            }
                        }
                    });
                });
            }

            OTPInput();
        });
    </script>
  </body>
</html>
