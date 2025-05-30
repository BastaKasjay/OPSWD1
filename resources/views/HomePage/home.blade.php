<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Home</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            display: flex;
        }

        .sidebar {
            width: 180px;
            height: 100vh;
            background-color:rgb(10, 90, 40);
            padding-top: 1rem;
        }

        .sidebar a {
            color: #fff;
            padding: 10px;
            text-decoration: none;
            display: block;
        }

        .sidebar a:hover {
            background-color: #53e28b;
        }

        .dropdown-menu {
            background-color: #53e28b;
            border: none;
        }

        .dropdown-menu a {
            color: #fff;
        }

        .main {
            flex-grow: 1;
            padding: 20px;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <h5 class="text-white text-center">ADMIN</h5>

        <div class="dropdown">
            <a href="{{ route('client.create') }}" class="">
                Client Management
            </a>
            
        </div>

        <div class="dropdown">
            <a href="" class="">
                System Management
            </a>
            
        </div>

        <div class="dropdown">
            <a href="" class="">
                User Management
            </a>
            
        </div>

        <div class="dropdown">
            <a class="dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                Reports
            </a>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="#">Clients with complete requirements</a></li>
                <li><a class="dropdown-item" href="#">Clients with incomplete requirements</a></li>
            </ul>
        </div>
        
        

        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-danger w-100 mt-3">
                Logout
            </button>
        </form>
    </div>

    <div class="main">
        <h1>Welcome to OPSWD</h1>
        <p>This is your dashboard homepage.</p>
    </div>

    <!-- Bootstrap JS (includes Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
