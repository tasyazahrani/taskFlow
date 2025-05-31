<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Manager - Deadline & Prioritas</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('css/tugas.css') }}">
</head>
<body>
    <!-- Decorative background elements -->
    <div class="bg-decoration"></div>
    <div class="bg-decoration"></div>
    <div class="bg-decoration"></div>
    <div class="bg-decoration"></div>

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
        
        <ul class="nav-menu">
            <li class="nav-item">
                <a href="{{ route('dashboard') }}" class="nav-link">
                    <span class="nav-icon">🏠</span>
                    Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('tasks.index') }}" class="nav-link active">
                    <span class="nav-icon">📝</span>
                    Tugas
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

    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="header-content">
                <h1>Task Manager</h1>
                <p>Kelola tugas Anda dengan deadline dan prioritas</p>
            </div>
            <button class="add-task-btn" onclick="openModal('taskModal')">➕ Tambah Tugas</button>
        </div>

        <div class="main-content">
            <!-- Task List -->
            <div class="task-list">
                <div class="filter-tabs">
                    <div class="filter-tab {{ ($filter ?? 'all') === 'all' ? 'active' : '' }}" data-filter="all">
                        Semua <span class="count">({{ $taskCounts['all'] ?? 0 }})</span>
                    </div>
                    <div class="filter-tab {{ ($filter ?? '') === 'pending' ? 'active' : '' }}" data-filter="pending">
                        Aktif <span class="count">({{ $taskCounts['pending'] ?? 0 }})</span>
                    </div>
                    <div class="filter-tab {{ ($filter ?? '') === 'completed' ? 'active' : '' }}" data-filter="completed">
                        Selesai <span class="count">({{ $taskCounts['completed'] ?? 0 }})</span>
                    </div>
                    <div class="filter-tab {{ ($filter ?? '') === 'overdue' ? 'active' : '' }}" data-filter="overdue">
                        Terlambat <span class="count">({{ $taskCounts['overdue'] ?? 0 }})</span>
                    </div>
                </div>

                <h2 class="section-title">Daftar Tugas</h2>
                
                <div id="taskContainer">
                    @if(isset($tasks) && $tasks->isEmpty())
                        <div class="empty-state">
                            Belum ada tugas. Tambahkan tugas pertama Anda!
                        </div>
                    @else
                        @foreach($tasks ?? [] as $task)
                            <div class="task-item" data-id="{{ $task->id }}" data-status="{{ $task->status ?? 'pending' }}">
                                <div class="task-header">
                                    <div class="task-priority priority-{{ $task->priority ?? 'medium' }}">
                                        @switch($task->priority ?? 'medium')
                                            @case('high')
                                                🔥 Tinggi
                                                @break
                                            @case('low')
                                                🌱 Rendah
                                                @break
                                            @default
                                                ⚡ Sedang
                                        @endswitch
                                    </div>
                                    <div class="task-actions">
                                        <button class="action-btn edit-btn" onclick="editTask({{ $task->id }})" title="Edit">
                                            ✏️
                                        </button>
                                        <button class="action-btn complete-btn" onclick="toggleComplete({{ $task->id }})" title="{{ ($task->status ?? 'pending') === 'completed' ? 'Tandai belum selesai' : 'Tandai selesai' }}">
                                            {{ ($task->status ?? 'pending') === 'completed' ? '↩️' : '✅' }}
                                        </button>
                                        <button class="action-btn delete-btn" onclick="deleteTask({{ $task->id }})" title="Hapus">
                                            🗑️
                                        </button>
                                    </div>
                                </div>

                                <div class="task-content">
                                    <h3 class="task-title {{ ($task->status ?? 'pending') === 'completed' ? 'completed' : '' }}">
                                        <a href="{{ route('tasks.show', $task->id) }}" class="task-title-link">
                                            {{ $task->title ?? 'Untitled Task' }}
                                        </a>
                                    </h3>
                                    
                                    @if(!empty($task->notes))
                                        <p class="task-notes">{{ $task->notes }}</p>
                                    @endif

                                    @if(!empty($task->deadline))
                                        @php
                                            $deadline = is_string($task->deadline) ? \Carbon\Carbon::parse($task->deadline) : $task->deadline;
                                            $isOverdue = $deadline->isPast() && ($task->status ?? 'pending') !== 'completed';
                                        @endphp
                                        <div class="task-deadline {{ $isOverdue ? 'overdue' : '' }}">
                                            ⏰ {{ $deadline->format('d/m/Y H:i') }}
                                            @if($isOverdue)
                                                <span class="overdue-label">TERLAMBAT</span>
                                            @endif
                                        </div>
                                    @endif

                                    @if(!empty($task->subtasks))
                                        @php
                                            // Handle subtasks whether it's JSON string or array
                                            $subtasksList = [];
                                            if (is_string($task->subtasks)) {
                                                $decoded = json_decode($task->subtasks, true);
                                                $subtasksList = is_array($decoded) ? $decoded : explode("\n", $task->subtasks);
                                            } elseif (is_array($task->subtasks)) {
                                                $subtasksList = $task->subtasks;
                                            }
                                            $subtasksList = array_filter($subtasksList); // Remove empty items
                                        @endphp
                                        
                                        @if(count($subtasksList) > 0)
                                            <div class="subtasks">
                                                <div class="subtasks-header">Sub-tugas:</div>
                                                <ul class="subtasks-list">
                                                    @foreach($subtasksList as $subtask)
                                                        <li class="subtask-item">
                                                            {{ is_array($subtask) ? ($subtask['title'] ?? $subtask['name'] ?? 'Subtask') : trim($subtask) }}
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        @endif
                                    @endif
                                </div>

                                <div class="task-meta">
                                    <span class="task-status status-{{ $task->status ?? 'pending' }}">
                                        @switch($task->status ?? 'pending')
                                            @case('completed')
                                                ✅ Selesai
                                                @break
                                            @case('overdue')
                                                🔴 Terlambat
                                                @break
                                            @default
                                                🟡 Aktif
                                        @endswitch
                                    </span>
                                    <span class="task-date">
                                        Dibuat: {{ isset($task->created_at) ? $task->created_at->format('d/m/Y') : date('d/m/Y') }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Add/Edit Task Modal -->
    <div class="modal" id="taskModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title" id="modalTitle">➕ Tambah Tugas Baru</h2>
                <button class="close-btn" onclick="closeModal('taskModal')">&times;</button>
            </div>
            
            <form id="taskForm">
                @csrf
                <input type="hidden" id="taskId">
                <input type="hidden" id="formMethod" value="POST">
                
                <div class="form-group">
                    <label class="form-label">Judul Tugas</label>
                    <input type="text" class="form-input" id="taskTitle" placeholder="Masukkan judul tugas..." required>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Prioritas</label>
                        <select class="form-select" id="taskPriority">
                            <option value="high">🔥 Tinggi</option>
                            <option value="medium" selected>⚡ Sedang</option>
                            <option value="low">🌱 Rendah</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Deadline</label>
                        <input type="datetime-local" class="form-input" id="taskDeadline">
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Catatan</label>
                    <textarea class="form-textarea" id="taskNotes" placeholder="Tambahkan catatan atau rincian tugas..."></textarea>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Sub-tugas (pisahkan dengan enter)</label>
                    <textarea class="form-textarea" id="taskSubtasks" placeholder="Sub-tugas 1&#10;Sub-tugas 2&#10;Sub-tugas 3"></textarea>
                </div>
                
                <button type="submit" class="submit-btn" id="submitBtn">✨ Tambah Tugas</button>
            </form>
        </div>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="alert alert-success" id="alertMessage">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-error" id="alertMessage">
            {{ session('error') }}
        </div>
    @endif

    <script>
        // Set CSRF token for AJAX requests
        window.Laravel = {
            csrfToken: '{{ csrf_token() }}'
        };
        
        // Routes for JavaScript
        window.routes = {
            tasks: {
                store: '{{ route("tasks.store") }}',
                show: '{{ route("tasks.show", ":id") }}',
                update: '{{ route("tasks.update", ":id") }}',
                destroy: '{{ route("tasks.destroy", ":id") }}',
                toggleComplete: '{{ route("tasks.toggle-complete", ":id") }}',
                ajax: '{{ route("tasks.ajax") }}'
            }
        };
    </script>
    <script src="{{ asset('js/tugas.js') }}"></script>

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