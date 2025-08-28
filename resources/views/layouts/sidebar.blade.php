<aside class="sidebar">
    <div class="logo-container text-center">
        <div class="logo-circle">
            <svg class="logo-svg" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 3c1.66 0 3 1.34 3 3s-1.34 
                3-3 3-3-1.34-3-3 1.34-3 3-3zm0 14.2c-2.5 0-4.71-1.28-6-3.22.03-1.99 4-3.08 6-3.08 
                1.99 0 5.97 1.09 6 3.08-1.29 1.94-3.5 3.22-6 3.22z"/>
            </svg>
        </div>
        <div class="employee-name mt-2 fw-semibold">
            @auth
                @if(auth()->user()->employee)
                    {{ auth()->user()->employee->first_name }} {{ auth()->user()->employee->last_name }}
                @else
                    {{ auth()->user()->name ?? 'User' }}
                @endif
            @else
                Guest
            @endauth
        </div>
    </div>

    <nav class="nav-list">
        <ul>
            <li class="nav-item">
                <a href="{{ route('home') }}" class="nav-link">
                    <i class="fas fa-tachometer-alt"></i> <span>Dashboard</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('clients.index') }}" class="nav-link">
                    <i class="fas fa-users"></i> <span>Client Management</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('clients.assistance') }}" class="nav-link">
                    <i class="fas fa-hands-helping"></i> <span>Assistance Management</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('claims.grouped') }}" class="nav-link">
                    <i class="fas fa-hand-holding-usd"></i> <span>Payout Management</span>
                </a>
            </li>

            {{-- Admin-only links --}}
            @if(auth()->user()->hasRole('admin'))
                <li class="nav-item">
                    <a href="{{ route('employees.index') }}" class="nav-link">
                        <i class="fas fa-chart-bar"></i> <span>Employee Management</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('users.index') }}" class="nav-link">
                        <i class="fas fa-user-cog"></i> <span>User Management</span>
                    </a>
                </li>
            @endif

            <li class="nav-item">
                <a href="{{ route('reports.index') }}" class="nav-link">
                    <i class="fas fa-chart-bar"></i> <span>Reports</span>
                </a>
            </li>
        </ul>
    </nav>

    <div class="logout-container">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="logout-button">
                <i class="fas fa-sign-out-alt"></i>
                <span>Log out</span>
            </button>
        </form>
    </div>
</aside>
