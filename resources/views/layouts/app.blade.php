<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'TaskFlow Dashboard')</title>
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
</head>
<body>
    <div class="dashboard">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="logo-section">
                <div class="logo">
                    <svg viewBox="0 0 24 24">
                        <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/>
                    </svg>
                </div>
                <div class="app-name">TaskFlow</div>
            </div>

            <!-- Sidebar Menu -->
            <ul class="nav-menu">
                <li class="nav-item">
                    <a href="{{ route('dashboard') }}" class="nav-link active">
                        <span class="nav-icon">🏠</span>
                        Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('tasks.index') }}" 
                    class="nav-link {{ request()->routeIs('tasks.*') ? 'active' : '' }}"
                    data-page="tasks">
                        <span class="nav-icon">📝</span>
                        <span class="nav-text">Tugas</span>
                        @if(isset($taskCounts))
                            <span class="nav-badge">{{ $taskCounts['pending'] ?? 0 }}</span>
                        @endif
                    </a>
                </li>
                <li class="nav-item">
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="nav-link logout-btn">
                            <span class="nav-icon">🚪</span>
                            Logout
                        </button>
                    </form>
                </li>
            </ul>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            @yield('content') <!-- Bagian konten yang akan diisi oleh halaman lain -->
        </div>
    </div>

    <script src="{{ asset('js/dashboard.js') }}"></script>

    <!-- CSS untuk logout button -->
    <style>
        .logout-btn {
            background: none;
            border: none;
            cursor: pointer;
            color: inherit;
            text-decoration: none;
            padding: 0.75rem 1rem;
            font: inherit;
            display: flex;
            align-items: center;
            width: 100%;
            text-align: left;
            transition: background-color 0.3s ease;
        }

        .logout-btn:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }

        .task-title-link {
            color: #666;
            text-decoration: none;
        }

    </style>
</body>
</html>
