
<!DOCTYPE html>
<html lang="en">

<head>
    <title>OPSWD Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet" />
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        govgreen: "#4A8B57",
                        lightgreen: "#F0F7F1",
                    },
                },
            },
        };
    </script>
    <style>
        body {
            font-family: "Inter", sans-serif;
        }
    </style>
</head>

<body class="bg-lightgreen">
    <div class="min-h-screen flex items-center justify-center p-4">
        <div class="bg-white p-10 rounded-xl shadow-lg w-full max-w-md border border-gray-100">
            <div class="text-center mb-10">
                <h1 class="text-3xl font-bold text-govgreen mb-2 tracking-wide">
                    LOGIN
                </h1>
                <p class="text-gray-600 text-sm">
                    <span class="font-bold">OPSWD</span> - Office of the
                    Provincial Social Welfare & Development
                </p>
            </div>
            <form method="POST" action="{{ route('login.submit') }}">
                @csrf
                <div class="space-y-6">
                    <div class="relative">
                        <input type="text" name="username" id="username" placeholder="Username"
                            class="peer h-14 w-full px-4 rounded-xl border border-gray-300 bg-white placeholder-transparent focus:border-govgreen focus:ring-2 focus:ring-govgreen/30 outline-none transition-colors" />
                        <label for="username"
                            class="absolute left-4 -top-3.5 bg-white p-1 text-sm transition-all peer-placeholder-shown:text-base peer-placeholder-shown:top-4 peer-placeholder-shown:bg-transparent peer-placeholder-shown:p-0 peer-focus:-top-3.5 peer-focus:text-sm peer-focus:text-govgreen peer-focus:bg-white peer-focus:p-1 text-gray-600">
                            Username
                        </label>
                    </div>
                    <div class="relative">
                        <input type="password" name="password" id="password" placeholder="Password"
                            class="peer h-14 w-full px-4 rounded-xl border border-gray-300 bg-white placeholder-transparent focus:border-govgreen focus:ring-2 focus:ring-govgreen/30 outline-none transition-colors" />
                        <label for="password"
                            class="absolute left-4 -top-3.5 bg-white p-1 text-sm transition-all peer-placeholder-shown:text-base peer-placeholder-shown:top-4 peer-placeholder-shown:bg-transparent peer-placeholder-shown:p-0 peer-focus:-top-3.5 peer-focus:text-sm peer-focus:text-govgreen peer-focus:bg-white peer-focus:p-1 text-gray-600">
                            Password
                        </label>
                    </div>
                    <div class="flex items-center justify-between">
                        <label class="flex items-center gap-2">
                            <input type="checkbox"
                                class="w-4 h-4 border-gray-300 rounded text-govgreen focus:ring-govgreen/30"
                                name="remember" />
                            <span class="text-sm text-gray-600">Remember me</span>
                        </label>
                        <a href="#" class="text-sm text-govgreen hover:text-govgreen/80 transition-colors">
                            Forgot password?
                        </a>
                    </div>
                    <button
                        class="w-full bg-gradient-to-tr from-govgreen to-govgreen/70 text-white py-3 px-4 rounded-lg hover:bg-govgreen/90 transition-all active:scale-[0.98] font-medium h-14">
                        LOG IN
                    </button>

                     {{-- Display login error --}}
                    @if($errors->has('login'))
                    <p class="text-red-500 text-sm mt-4 text-center">{{ $errors->first('login') }}</p>
                    @endif

                </div>
            </form>
        </div>
    </div>
</body>

</html>
