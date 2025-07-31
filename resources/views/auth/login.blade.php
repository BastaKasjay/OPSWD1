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
  </style>
</head>
<body class="d-flex justify-content-center align-items-center vh-100">

  <div class="container">
    <div class="row justify-content-center">
      <div class="col-lg-10 shadow-lg rounded-4 overflow-hidden bg-white d-flex p-0">

        <!-- Left side (form) -->
        <div class="col-md-6 p-5 d-flex flex-column justify-content-center align-items-center">
          <div class="text-center mb-4">
            <h2 class="text-green fw-bold">LOGIN</h2>
            <p class="text-muted small">Office of the Provincial Social Welfare & Development</p>
          </div>

          <form class="w-100" style="max-width: 300px;" method="POST" action="{{ route('login') }}">
            @csrf

            <!-- Username -->
            <div class="mb-3">
              <input type="text" name="username" id="username" class="form-control" placeholder="Username"
                     value="{{ old('username') }}" required autofocus autocomplete="off" />
              @error('username')
                <p class="text-danger small mt-1">{{ $message }}</p>
              @enderror
              @if($errors->has('login'))
                <p class="text-danger small mt-1">{{ $errors->first('login') }}</p>
              @endif
            </div>

            <!-- Password -->
            <div class="mb-3">
              <input type="password" name="password" id="password" class="form-control" placeholder="Password" required />
              @error('password')
                <p class="text-danger small mt-1">{{ $message }}</p>
              @enderror
            </div>

            <!-- Forgot Password -->
            <div class="mb-3 text-muted small">
              <label class="me-2">
                <input type="checkbox" name="remember" class="form-check-input me-1"> Remember me
              </label>
              @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="text-green text-decoration-none">Forgot password?</a>
              @endif
            </div>

            <!-- Submit Button -->
            <div class="d-grid">
              <button type="submit" class="btn btn-green fw-bold">Login</button>
            </div>
          </form>
        </div>

        <!-- Right side (image) -->
        <div class="col-md-6 p-0">
          <img src="dji_fly_20250527_9291 AM_372_1748314528355_photo.jpg"
               alt="People Receiving Aid"
               class="img-fluid h-100 w-100 object-fit-cover" />
        </div>

      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
