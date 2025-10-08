<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Login - BluePlanet</title>

  <!-- Feather Icons CSS (or load your own if hosted locally) -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.css">

  <style>
    body {
      margin: 0;
      background: linear-gradient(90deg, rgb(217 235 254) 0%, rgb(255 237 231) 50%, #fce8d2 100%);
    }

    .login-wrapper {
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .login-card {
      width: 60%;
      max-width: 820px;
      border-radius: 10px;
      overflow: hidden;
      background-color: #fff;
      box-shadow: 0px 2px 30px #ccc6;
    }

    .left-panel {
      padding: 3.5rem 3.3rem 4.3rem 3.3rem;
      border-right: 1px solid #e0e0e0;
    }

    .right-panel {
      background-color: #003e7e;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 1rem;
    }

    .login-logo {
      width: 130px;
      margin-bottom: 1rem;
    }

    .form-control {
      border-radius: 8px;
      height: 48px;
      background-color: #f7f7f7;
    }

    .btn-warning {
      background-color: #ff9900;
      border: none;
    }

    .btn-warning:hover {
      background-color: #e28a00;
    }

    .toggle-password {
      position: absolute;
      top: 50%;
      right: 1rem;
      transform: translateY(-50%);
      background: none;
      border: none;
      cursor: pointer;
    }

    @media (max-width: 767px) {
      .right-panel {
        display: none;
      }
    }

    .animated-lines {
      position: fixed;
      width: 100%;
      height: 100%;
      z-index: -1;
      overflow: hidden;
    }

    .line {
      position: absolute;
      width: 1px;
      height: 100%;
      background: rgba(0, 0, 0, 0.05);
      animation: fall 2s linear infinite;
    }

    @keyframes fall {
      0% { transform: translateY(-100%); }
      100% { transform: translateY(100%); }
    }

    .custom-dots button {
      width: 40px;
      height: 6px;
      background-color: transparent;
      border: 2px dotted #ff9800;
      border-radius: 10px;
      margin: 0 4px;
      transition: border-color 0.3s, background-color 0.3s;
    }

    .custom-dots button.active {
      border-style: solid;
      background-color: #ff9800;
    }

    .carousel-indicators [data-bs-target] { background-color: #ccc; }
    .carousel-indicators { top: 96%; }

    #footer {
      width: 100%;
      height: 20px;
      font-size: 14px;
      color: #727272;
      position: absolute;
      left: 0px;
      right: 0px;
      margin: 20px auto;
      text-align: center;
      bottom: 0px;
    }
  </style>
</head>

<body>

<!-- Animated Background Lines -->
<div class="animated-lines">
  <div class="line" style="left: 10%; animation-delay: 0s;"></div>
  <div class="line" style="left: 20%; animation-delay: 1s;"></div>
  <div class="line" style="left: 35%; animation-delay: 0.5s;"></div>
  <div class="line" style="left: 50%; animation-delay: 0.8s;"></div>
  <div class="line" style="left: 70%; animation-delay: 1.2s;"></div>
  <div class="line" style="left: 80%; animation-delay: 2s;"></div>
  <div class="line" style="left: 90%; animation-delay: 1s;"></div>
</div>

<div class="login-wrapper">
  <div class="row login-card">
    <!-- Left Panel -->
    <div class="col-md-7 left-panel text-center">
      <img src="assets/images/logo.png" class="login-logo" alt="Logo">
      <h2 class="mb-0"><b>Welcome</b></h2>
      <p class="mb-4 mt-1">Enter your credentials to continue</p>

      <form>
        <div class="mb-2">
          <input type="text" class="form-control" id="user_name" placeholder="Username" required />
        </div>

        <div class="mb-2 position-relative">
          <input type="password" class="form-control" id="password" placeholder="Password" />
          <button type="button" class="toggle-password" onclick="togglePassword()">
            <i class="fe-eye password-eye text-muted"></i>
          </button>
        </div>

        <button type="button" onclick="login()" class="btn btn-warning w-100 py-2 mt-2">Log In</button>
      </form>
    </div>

    <!-- Right Panel with Image Carousel -->
    <div class="col-md-5 right-panel d-flex align-items-center justify-content-center">
      <div id="imageCarousel" class="carousel slide carousel-fade w-100" data-bs-ride="carousel">
        <div class="carousel-indicators custom-dots mb-3">
          <button type="button" data-bs-target="#imageCarousel" data-bs-slide-to="0" class="active" aria-current="true"></button>
          <button type="button" data-bs-target="#imageCarousel" data-bs-slide-to="1"></button>
        </div>

        <div class="carousel-inner rounded overflow-hidden">
          <div class="carousel-item active">
            <img src="assets/images/BP1.png" class="d-block w-100" alt="Slide 1" />
          </div>
          <div class="carousel-item">
            <img src="assets/images/BP2.png" class="d-block w-100" alt="Slide 2" />
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<footer id="footer">
  <span>© 2025 Blueplanet. All Rights Reserved.</span>
</footer>

<!-- Bootstrap JS Bundle (if needed) -->
<!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script> -->

<script>
function togglePassword() {
  const passwordInput = document.getElementById("password");
  const eyeIcon = document.querySelector(".password-eye");

  if (passwordInput.type === "password") {
    passwordInput.type = "text";
    eyeIcon.classList.remove("fe-eye");
    eyeIcon.classList.add("fe-eye-off");
  } else {
    passwordInput.type = "password";
    eyeIcon.classList.remove("fe-eye-off");
    eyeIcon.classList.add("fe-eye");
  }
}
</script>
</body>
</html>
