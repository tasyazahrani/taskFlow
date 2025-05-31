// Sample tasks data - TAMBAHKAN INI
let tasks = [
    {
        id: 1,
        title: "Menyelesaikan Laporan Akhir",
        priority: "high",
        deadline: "2025-05-31",
        notes: "Perlu review dari dosen pembimbing sebelum submission final",
        subtasks: [
            { text: "Menulis bab pendahuluan", completed: true },
            { text: "Analisis data penelitian", completed: true },
            { text: "Menyusun kesimpulan", completed: false },
            { text: "Proofreading dan editing", completed: false }
        ],
        completed: false
    },
    {
        id: 2,
        title: "Presentasi Proposal Skripsi",
        priority: "medium",
        deadline: "2025-06-05",
        notes: "Persiapkan slide presentasi dan latihan public speaking",
        subtasks: [
            { text: "Buat outline presentasi", completed: true },
            { text: "Desain slide PowerPoint", completed: false },
            { text: "Latihan presentasi", completed: false }
        ],
        completed: false
    },
    {
        id: 3,
        title: "Mengumpulkan Data Survei",
        priority: "low",
        deadline: "2025-06-10",
        notes: "Survei online untuk 100 responden minimum",
        subtasks: [
            { text: "Buat kuesioner Google Forms", completed: true },
            { text: "Distribusi ke responden", completed: false },
            { text: "Analisis hasil survei", completed: false }
        ],
        completed: false
    }
];

let currentFilter = 'all';
let expandedTaskId = null;

// DOM Elements
let tasksList, taskModal, taskForm;

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    // Get DOM elements
    tasksList = document.getElementById('tasksList');
    taskModal = document.getElementById('taskModal');
    taskForm = document.getElementById('taskForm');
    
    // Check if elements exist
    if (!tasksList) {
        console.error('Element tasksList tidak ditemukan!');
        return;
    }
    
    renderTasks();
    updateStats();
    updateProgress();
});

// Render tasks
function renderTasks(filter = 'all') {
    if (!tasksList) {
        console.error('tasksList element not found');
        return;
    }
    
    currentFilter = filter;
    let filteredTasks = tasks;
    
    // Update filter buttons
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    
    if (filter === 'high' || filter === 'medium' || filter === 'low') {
        filteredTasks = tasks.filter(task => task.priority === filter);
    } else if (filter === 'today') {
        const today = new Date().toDateString();
        filteredTasks = tasks.filter(task => {
            const taskDate = new Date(task.deadline).toDateString();
            return taskDate === today;
        });
    }

    tasksList.innerHTML = filteredTasks.map(task => {
        const completedSubtasks = task.subtasks.filter(st => st.completed).length;
        const totalSubtasks = task.subtasks.length;
        const progressPercentage = totalSubtasks > 0 ? (completedSubtasks / totalSubtasks) * 100 : 0;
        
        const deadlineDate = new Date(task.deadline);
        const now = new Date();
        const isOverdue = deadlineDate < now && !task.completed;
        const isToday = deadlineDate.toDateString() === now.toDateString();
        const isExpanded = expandedTaskId === task.id;
        
        return `
            <div class="task-item priority-${task.priority} ${isExpanded ? 'task-expanded' : ''}" onclick="expandTask(${task.id})">
                <div class="task-header">
                    <div class="task-title">${task.title}</div>
                    <button class="delete-btn" onclick="deleteTask(${task.id}, event)">🗑️</button>
                </div>
                
                <div class="task-meta">
                    <span class="priority-badge priority-${task.priority}">
                        ${task.priority === 'high' ? '🔥 Tinggi' : 
                          task.priority === 'medium' ? '⚡ Sedang' : '🌱 Rendah'}
                    </span>
                    <span class="deadline ${isOverdue ? 'overdue' : isToday ? 'today' : ''}">
                        📅 ${formatDeadline(task.deadline)}
                    </span>
                </div>
                
                <div class="task-progress">
                    <div class="progress-bar" style="width: ${progressPercentage}%"></div>
                </div>
                
                <div style="font-size: 0.9rem; color: #666; margin-bottom: 1rem;">
                    ${completedSubtasks}/${totalSubtasks} sub-tugas selesai (${Math.round(progressPercentage)}%)
                </div>
                
                ${isExpanded ? `
                    <div class="subtasks">
                        <h4 style="color: #333; margin-bottom: 0.5rem;">Sub-tugas:</h4>
                        ${task.subtasks.map((subtask, index) => `
                            <div class="subtask">
                                <div class="checkbox ${subtask.completed ? 'checked' : ''}" 
                                     onclick="toggleSubtask(${task.id}, ${index}, event)">
                                    ${subtask.completed ? '✓' : ''}
                                </div>
                                <span class="subtask-text ${subtask.completed ? 'completed' : ''}">${subtask.text}</span>
                            </div>
                        `).join('')}
                    </div>
                    
                    ${task.notes ? `
                        <div class="task-notes">
                            <div class="notes-title">📝 Catatan:</div>
                            <div class="notes-content">${task.notes}</div>
                        </div>
                    ` : ''}
                ` : ''}
            </div>
        `;
    }).join('');
}

// Format deadline
function formatDeadline(deadline) {
    const date = new Date(deadline);
    const now = new Date();
    const diffTime = date - now;
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
    
    if (diffDays < 0) {
        return `Terlambat ${Math.abs(diffDays)} hari`;
    } else if (diffDays === 0) {
        return 'Hari ini';
    } else if (diffDays === 1) {
        return 'Besok';
    } else {
        return `${diffDays} hari lagi`;
    }
}

// Expand/collapse task
function expandTask(taskId) {
    if (expandedTaskId === taskId) {
        expandedTaskId = null;
    } else {
        expandedTaskId = taskId;
    }
    renderTasks(currentFilter);
}

// Toggle subtask completion
function toggleSubtask(taskId, subtaskIndex, event) {
    event.stopPropagation();
    const task = tasks.find(t => t.id === taskId);
    if (task) {
        task.subtasks[subtaskIndex].completed = !task.subtasks[subtaskIndex].completed;
        renderTasks(currentFilter);
        updateStats();
        updateProgress();
    }
}

// Delete task
function deleteTask(taskId, event) {
    event.stopPropagation();
    if (confirm('Apakah Anda yakin ingin menghapus tugas ini?')) {
        tasks = tasks.filter(task => task.id !== taskId);
        renderTasks(currentFilter);
        updateStats();
        updateProgress();
    }
}

// Filter tasks
function filterTasks(filter) {
    // Update filter button active state
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.classList.remove('active');
        if (btn.textContent.toLowerCase().includes(filter) || 
            (filter === 'all' && btn.textContent === 'Semua') ||
            (filter === 'high' && btn.textContent === 'Prioritas Tinggi') ||
            (filter === 'medium' && btn.textContent === 'Prioritas Sedang') ||
            (filter === 'low' && btn.textContent === 'Prioritas Rendah') ||
            (filter === 'today' && btn.textContent === 'Deadline Hari Ini')) {
            btn.classList.add('active');
        }
    });
    
    renderTasks(filter);
}

// Update statistics
function updateStats() {
    const total = tasks.length;
    const completed = tasks.filter(task => {
        const completedSubtasks = task.subtasks.filter(st => st.completed).length;
        return completedSubtasks === task.subtasks.length && task.subtasks.length > 0;
    }).length;
    const pending = total - completed;

    const totalTasksEl = document.getElementById('totalTasks');
    const completedTasksEl = document.getElementById('completedTasks');
    const pendingTasksEl = document.getElementById('pendingTasks');

    if (totalTasksEl) totalTasksEl.textContent = total;
    if (completedTasksEl) completedTasksEl.textContent = completed;
    if (pendingTasksEl) pendingTasksEl.textContent = pending;
}

// Update progress
function updateProgress() {
    const totalSubtasks = tasks.reduce((sum, task) => sum + task.subtasks.length, 0);
    const completedSubtasks = tasks.reduce((sum, task) => 
        sum + task.subtasks.filter(st => st.completed).length, 0);
    
    const percentage = totalSubtasks > 0 ? Math.round((completedSubtasks / totalSubtasks) * 100) : 0;
    
    const progressPercentageEl = document.getElementById('progressPercentage');
    const completedCountEl = document.getElementById('completedCount');
    const totalCountEl = document.getElementById('totalCount');
    const circularProgressEl = document.getElementById('circularProgress');

    if (progressPercentageEl) progressPercentageEl.textContent = `${percentage}%`;
    if (completedCountEl) completedCountEl.textContent = completedSubtasks;
    if (totalCountEl) totalCountEl.textContent = totalSubtasks;
    
    // Update circular progress
    if (circularProgressEl) {
        const degrees = (percentage / 100) * 360;
        circularProgressEl.style.background = 
            `conic-gradient(#ED4A81 0deg, #ED4A81 ${degrees}deg, #e9ecef ${degrees}deg)`;
    }
}

// Modal functions
function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.style.display = 'block';
        console.log('Modal opened:', modalId);
    } else {
        console.error('Modal not found:', modalId);
    }
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.style.display = 'none';
        if (modalId === 'taskModal' && taskForm) {
            taskForm.reset();
        }
    }
}

// Show progress alert
function showProgress() {
    const totalSubtasks = tasks.reduce((sum, task) => sum + task.subtasks.length, 0);
    const completedSubtasks = tasks.reduce((sum, task) => 
        sum + task.subtasks.filter(st => st.completed).length, 0);
    const percentage = totalSubtasks > 0 ? Math.round((completedSubtasks / totalSubtasks) * 100) : 0;
    
    alert(`📊 Laporan Kemajuan:\n\n` +
          `✅ Sub-tugas selesai: ${completedSubtasks}\n` +
          `📝 Total sub-tugas: ${totalSubtasks}\n` +
          `📈 Persentase kemajuan: ${percentage}%\n\n` +
          `${percentage >= 80 ? '🎉 Kerja bagus! Anda hampir selesai!' : 
            percentage >= 50 ? '💪 Terus semangat! Anda sudah setengah jalan!' : 
            '🚀 Ayo mulai menyelesaikan tugas-tugas Anda!'}`);
}

// Handle form submission - PERBAIKI INI
document.addEventListener('DOMContentLoaded', function() {
    const taskForm = document.getElementById('taskForm');
    if (taskForm) {
        taskForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const title = document.getElementById('taskTitle').value;
            const priority = document.getElementById('taskPriority').value;
            const deadline = document.getElementById('taskDeadline').value;
            const notes = document.getElementById('taskNotes').value;
            const subtasksElement = document.getElementById('taskSubtasks');
            
            // Default subtasks jika tidak ada element subtasks
            let subtasks = [{ text: 'Selesaikan tugas ini', completed: false }];
            
            if (subtasksElement && subtasksElement.value.trim()) {
                subtasks = subtasksElement.value.split('\n')
                    .filter(line => line.trim())
                    .map(text => ({ text: text.trim(), completed: false }));
            }
            
            const newTask = {
                id: Date.now(),
                title,
                priority,
                deadline,
                notes,
                subtasks,
                completed: false
            };
            
            tasks.push(newTask);
            renderTasks(currentFilter);
            updateStats();
            updateProgress();
            closeModal('taskModal');
            
            // Show success message
            alert('✨ Tugas berhasil ditambahkan!');
        });
    }
});

// Close modal when clicking outside
window.addEventListener('click', function(e) {
    if (e.target.classList.contains('modal')) {
        e.target.style.display = 'none';
    }
});

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        document.querySelectorAll('.modal').forEach(modal => {
            modal.style.display = 'none';
        });
    }
    if (e.ctrlKey && e.key === 'n') {
        e.preventDefault();
        openModal('taskModal');
    }
});

// Auto-update time-sensitive elements every minute
setInterval(() => {
    renderTasks(currentFilter);
}, 60000);