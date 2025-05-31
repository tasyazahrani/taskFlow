@extends('layouts.app') <!-- Menggunakan layout app.blade.php -->

@section('title', 'Dashboard') <!-- Mengatur judul halaman Dashboard -->

@section('content')
    <!-- Konten Dashboard -->
    <div class="header">
        <div class="welcome-text">
            <h1>Selamat Datang! 👋</h1>
            <p>Hari ini adalah hari yang tepat untuk menyelesaikan tugas-tugas penting Anda</p>
        </div>
        <div class="header-stats">
            <div class="stat-card">
                <div class="stat-number" id="totalTasks">12</div>
                <div class="stat-label">Total Tugas</div>
            </div>
            <div class="stat-card">
                <div class="stat-number" id="completedTasks">7</div>
                <div class="stat-label">Selesai</div>
            </div>
            <div class="stat-card">
                <div class="stat-number" id="pendingTasks">5</div>
                <div class="stat-label">Menunggu</div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="quick-actions">
        <h2 class="section-title">
            <span class="section-title-icon">⚡</span>
            Aksi Cepat
        </h2>
        <div class="action-buttons">
            <button class="action-btn" onclick="openModal('taskModal')">
                ➕ Tambah Tugas Baru
            </button>
            <button class="action-btn" onclick="filterTasks('today')">
                📅 Tugas Hari Ini
            </button>
            <button class="action-btn" onclick="filterTasks('high')">
                🔥 Prioritas Tinggi
            </button>
            <button class="action-btn" onclick="showProgress()">
                📊 Lihat Laporan
            </button>
        </div>
    </div>

    <!-- Tasks and Progress Section -->
    <div class="tasks-section">
        <!-- Tasks Container -->
        <div class="tasks-container">
            <h2 class="section-title" style="color: #333;">
                <span class="section-title-icon">📋</span>
                Daftar Tugas
            </h2>

            <div class="task-filters">
                <button class="filter-btn active" onclick="filterTasks('all')">Semua</button>
                <button class="filter-btn" onclick="filterTasks('high')">Prioritas Tinggi</button>
                <button class="filter-btn" onclick="filterTasks('medium')">Prioritas Sedang</button>
                <button class="filter-btn" onclick="filterTasks('low')">Prioritas Rendah</button>
                <button class="filter-btn" onclick="filterTasks('today')">Deadline Hari Ini</button>
            </div>

            <div class="tasks-list" id="tasksList">
                <!-- Tasks will be populated by JavaScript -->
            </div>
        </div>

        <!-- Progress Container -->
        <div class="progress-container">
            <h2 class="section-title" style="color: #333;">
                <span class="section-title-icon">📈</span>
                Kemajuan
            </h2>

            <div class="progress-overview">
                <div class="circular-progress" id="circularProgress">
                    <div class="progress-percentage" id="progressPercentage">58%</div>
                </div>
                <div class="progress-stats">
                    <div class="progress-stat">
                        <div class="progress-stat-number" id="completedCount">7</div>
                        <div class="progress-stat-label">Selesai</div>
                    </div>
                    <div class="progress-stat">
                        <div class="progress-stat-number" id="totalCount">12</div>
                        <div class="progress-stat-label">Total</div>
                    </div>
                </div>
            </div>

            <div class="notifications">
                <h3 style="color: #333; margin-bottom: 1rem;">🔔 Pengingat</h3>
                <div class="notification-item">
                    <div class="notification-icon">⏰</div>
                    <div class="notification-content">
                        <div class="notification-title">Deadline Tugas Akhir</div>
                        <div class="notification-time">2 jam lagi</div>
                    </div>
                </div>
                <div class="notification-item">
                    <div class="notification-icon">📝</div>
                    <div class="notification-content">
                        <div class="notification-title">Review Laporan</div>
                        <div class="notification-time">Besok pagi</div>
                    </div>
                </div>
                <div class="notification-item">
                    <div class="notification-icon">📞</div>
                    <div class="notification-content">
                        <div class="notification-title">Meeting dengan Dosen</div>
                        <div class="notification-time">3 hari lagi</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Task Modal -->
   <div class="modal" id="taskModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">➕ Tambah Tugas Baru</h2>
                <button class="close-btn" onclick="closeModal('taskModal')" style="position:absolute; top:10px; right:10px; font-size:24px; border:none; background:none;">&times;</button>
            </div>

            <form id="taskForm">
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
                    <textarea class="form-textarea" id="taskSubtasks" placeholder="Sub-tugas 1\nSub-tugas 2\nSub-tugas 3"></textarea>
                </div>

                <button type="submit" class="submit-btn">✨ Tambah Tugas</button>
            </form>
        </div>
    </div>
@endsection
