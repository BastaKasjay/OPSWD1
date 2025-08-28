<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>OPSWD Login</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #639D7F;
      font-family: "Inter", sans-serif;
      min-height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
    }
    .text-green {
      color: #639D7F !important;
    }
    .btn-green {
      background-color: #639D7F;
      color: #fff;
    }
    .btn-green:hover {
      background-color: #4A8B57;
      color: #fff;
    }
    .form-control:focus {
      border-color: #639D7F;
      box-shadow: 0 0 0 0.2rem rgba(99, 157, 127, 0.25);
    }
    .login-card {
      max-width: 400px;
      width: 100%;
      background: #fff;
      border-radius: 15px;
      padding: 2rem;
      box-shadow: 0 4px 15px rgba(0,0,0,0.2);
      margin: 0 auto;
    }
    
    .card {
      max-width: 400px;
      width: 100%;
      background: #fff;
      border-radius: 15px;
      padding: 2rem;
      box-shadow: 0 4px 15px rgba(0,0,0,0.2);
      margin: 0 auto; 
    }
    
    .form-check-input {
        width: 18px;
        height: 18px;
        accent-color: #198754; 
        border: 2px solid #198754;
    }

    .custom-input {
        border: 2px solid #3eb489;   
        border-radius: 10px;         
        padding: 10px;
        background-color: #f8fffc;   
        transition: all 0.3s ease;
    }

    .custom-input:focus {
        border-color: #2fa176;      
        box-shadow: 0 0 6px rgba(62, 180, 137, 0.6);
        outline: none;
    }
  </style>
</head>
<body>
<div class="card shadow-lg p-4 mx-auto" style="max-width: 700px; border-radius: 15px;">
  <div class="text-center">

    <div class="mb-5 text-center">
  <div class="d-flex justify-content-center align-items-center mb-4" style="gap: 50px;">
    <img src="{{ asset('image/OPSWD-LOGO.png') }}" 
         alt="OPSWD Logo" 
         class="img-fluid" 
         style="max-height: 100px;">
    <img src="{{ asset('image/benguet-logo.png') }}" 
         alt="Benguet Logo" 
         class="img-fluid" 
         style="max-height: 120px;"> <!-- provincial logo slightly bigger -->
    <img src="{{ asset('image/it-logo.png') }}" 
         alt="IT Logo" 
         class="img-fluid" 
         style="max-height: 120px;">
  </div>

  <h2 class="fw-bold text-dark mb-1">BAICS Management System</h2>
  <p class="mb-0">OPSWD</p>
  <p class="mb-0">Office of the Provincial Social Welfare & Development</p>
</div>



    <!-- Login Form Card -->
    <div class="login-card">

      <h3 class="text-center mb-4">Login</h3>
      <form method="POST" action="{{ route('login') }}" autocomplete="off">
        @csrf
        <div class="mb-3">
          <input type="text" name="username" class="form-control" placeholder="Username" required autofocus>
        </div>
        <div class="mb-3">
          <input type="password" name="password" class="form-control" placeholder="Password" required>
        </div>
        <!-- Remember Me -->
      <div class="mb-3 form-check">
          <input type="checkbox" class="form-check-input" id="remember" name="remember">
          <label class="form-check-label" for="remember">
              Remember Me
          </label>
      </div>
        <button type="submit" class="btn btn-green w-100">Login</button>
      </form>
    </div>

  </div>
</div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
