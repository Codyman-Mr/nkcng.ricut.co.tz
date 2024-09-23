<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Welcome</title>
    <link href={{asset('css/bootstrap.min.css')}} rel="stylesheet">
    <link href={{asset('css/welcome.css')}} rel="stylesheet">
  </head>
  <body>
    <div class="navbar">
      <div class="navbar-contents">
        <img
          src="./img/logo.png"
          alt="Logo"
          width=""
          height="60px"
          id="logo"
        />
        <span class="company-title">NK CNG LOAN SYSTEM</span>
      </div>
    </div>
    <div class="container">
      <div class="welcome-card">
        <div class="card-contents">
          <div class="name">
            <h1>WELCOME <span>{{Auth::user()->first_name}},</span></h1>
            <div class="border"></div>
          </div>
          <span>Great choice!</span>
          <div class="word">
            <p>
              Please share more details to get your car ready for installation
              appointment.
            </p>
          </div>
        </div>
        <button class="get-started" onclick="window.location.href='/'">Get Started</button>
      </div>
    </div>

    <footer class="footer">
      <div class="footercontainer">
        <div class="footer-name">
          <span>NK CNG LOAN SYSTEM</span>
        </div>
        <div class="footer-details">
          <span>EASY CARE EASY LIFE</span>
        </div>
      </div>
    </footer>
  </body>
</html>
