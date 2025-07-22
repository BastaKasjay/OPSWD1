<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Financial Assistance System</title>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">


    <!-- Your Custom CSS -->
    <link rel="stylesheet" href="{{ asset('main/css/styles.css') }}">
    <link rel="stylesheet" href="{{ asset('main/css/assistance-table.css') }}">


    <!-- Tailwind CSS via CDN
    <script src="https://cdn.tailwindcss.com"></script> -->
    <!-- <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'mint-green': {
                            50: '#F8FCFA',
                            100: '#EEF6F0',
                            200: '#DDEEE1',
                            300: '#CDE5D3',
                            400: '#BCE0C5',
                            500: '#aee0c1',
                            600: '#9CD1B5',
                            700: '#89C0A3',
                            800: '#76AE91',
                            900: '#639D7F',
                            DEFAULT: '#aee0c1',
                        },
                    }
                }
            }
        }
    </script> -->

    
   
</head>
<body class="d-flex">

    {{-- Sidebar stays on the left --}}
    @include('layouts.sidebar')

    {{-- Main content area grows beside sidebar --}}
    <div class="main flex-grow-1">
        @yield('content')
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    @yield('scripts')
</body>

</html>
