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
            background-color: #81f1ad;
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
        <h5 class="text-white text-center">Dashboard</h5>

        <div class="dropdown">
            <a class>
                Manage clients
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
        
        <div class="dropdown">
            <a class>
                Settings
            </a>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="#">Feature 1</a></li>
                <li><a class="dropdown-item" href="#">Feature 2</a></li>
            </ul>
        </div>

        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">
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
