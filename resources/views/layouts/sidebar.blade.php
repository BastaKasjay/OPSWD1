
<div class="sidebar bg-success text-white d-flex flex-column justify-between" style="width: 300px; height: 100vh; padding: 1rem; background-color: #639D7F;">
    <div>
        <!-- Logo -->
        <div class="text-center mb-4">
            <svg class="text-white" width="80" height="80" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 
                         10-4.48 10-10S17.52 2 12 2zm0 3c1.66 0 
                         3 1.34 3 3s-1.34 3-3 3-3-1.34-3-3 
                         1.34-3 3-3zm0 14.2c-2.5 0-4.71-1.28-6-3.22.03-1.99 
                         4-3.08 6-3.08 1.99 0 5.97 1.09 6 
                         3.08-1.29 1.94-3.5 3.22-6 3.22z"/>
            </svg>
        </div>

        <!-- Navigation Links -->
        <a href="{{ route('home') }}" class="btn btn-outline-light w-100 text-start mb-2">
            <i class="fas fa-tachometer-alt me-2"></i> Dashboard
        </a>
        <a href="{{ route('clients.index') }}" class="btn btn-outline-light w-100 text-start mb-2">
            <i class="fas fa-users me-2"></i> Client Management
        </a>
        <a href="{{ route('clients.assistance') }}" class="btn btn-outline-light w-100 text-start mb-2">
            <i class="fas fa-info-circle me-2"></i> Assistance Management
        </a>
        <a href="{{ route('users.index') }}" class="btn btn-outline-light w-100 text-start mb-2">
            <i class="fas fa-user-cog me-2"></i> User Management
        </a>
        <a href="{{ route('reports.index') }}" class="btn btn-outline-light w-100 text-start mb-2">
            <i class="fas fa-book-open me-2"></i> Reports
        </a>
    </div>

    <!-- Logout Button -->
     <div class="Logout_btn">
        <form action="{{ route('logout') }}" method="POST" class="mt-auto">
            @csrf
            <button type="submit" class="btn btn-light w-100 mt-3">
                <i class="fas fa-sign-out-alt me-2"></i> Logout
            </button>
        </form>
    </div>
</div>
