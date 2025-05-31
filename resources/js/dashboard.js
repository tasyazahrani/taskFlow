// Initialize dashboard
document.addEventListener('DOMContentLoaded', function() {
    renderTasks();
    updateStats();
    updateProgress();
});

// Render tasks to the list
function renderTasks(filter = 'all') {
    const tasksList = document.getElementById('tasksList');
    if (!tasksList) return;

    let filteredTasks = tasks;
    
    // Apply filters
    if (filter === 'high') {
        filteredTasks = tasks.filter(task => task.priority === 'high');
    } else if (filter === 'medium') {
        filteredTasks = tasks.filter(task => task.priority === 'medium');
    } else if (filter === 'low') {
        filteredTasks = tasks.filter(task => task.priority === 'low');
    } else if (filter === 'today') {
        const today = new Date().toISOString().split('T')[0];
        filteredTasks = tasks.filter(task => task.deadline === today);
    }

    tasksList.innerHTML = '';

    filteredTasks.forEach(task => {
        const taskElement = createTaskElement(task);
        tasksList.appendChild(taskElement);
    });
}

// Create task element
function createTaskElement(task) {
    const taskDiv = document.createElement('div');
    taskDiv.className = `task-item priority-${task.priority}`;
    taskDiv.setAttribute('data-task-id', task.id);

    const deadlineClass = getDeadlineClass(task.deadline);
    const priorityLabel = getPriorityLabel(task.priority);

    taskDiv.innerHTML = `
        <div class="task-header">
            <div class="task-title">${task.title}</div>
            <button class="delete-btn" onclick="deleteTask(${task.id})">🗑️</button>
        </div>
        <div class="task-meta">
            <span class="priority-badge priority-${task.priority}">${priorityLabel}</span>
            <span class="deadline ${deadlineClass}">📅 ${formatDate(task.deadline)}</span>
        </div>
        <div class="task-progress">
            <div class="progress-bar" style="width: ${task.progress}%"></div>
        </div>
        <div class="subtasks">
            ${task.subtasks.map(subtask => `
                <div class="subtask">
                    <div class="checkbox ${subtask.completed ? 'checked' : ''}" 
                         onclick="toggleSubtask(${task.id}, ${subtask.id})">
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
    `;

    // Add click handler for expansion
    taskDiv.addEventListener('click', function(e) {
        if (!e.target.closest('.delete-btn') && !e.target.closest('.checkbox')) {
            taskDiv.classList.toggle('task-expanded');
        }
    });

    return taskDiv;
}

// Helper functions
function getDeadlineClass(deadline) {
    const today = new Date().toISOString().split('T')[0];
    const taskDate = new Date(deadline);
    const todayDate = new Date(today);
    
    if (deadline < today) return 'overdue';
    if (deadline === today) return 'today';
    return '';
}

function getPriorityLabel(priority) {
    const labels = {
        'high': 'Tinggi',
        'medium': 'Sedang',
        'low': 'Rendah'
    };
    return labels[priority] || priority;
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric'
    });
}

// Filter tasks
function filterTasks(filter) {
    // Update active filter button
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    event.target.classList.add('active');
    
    renderTasks(filter);
}

// Toggle subtask completion
function toggleSubtask(taskId, subtaskId) {
    const task = tasks.find(t => t.id === taskId);
    if (task) {
        const subtask = task.subtasks.find(s => s.id === subtaskId);
        if (subtask) {
            subtask.completed = !subtask.completed;
            
            // Update task progress
            const completedCount = task.subtasks.filter(s => s.completed).length;
            task.progress = Math.round((completedCount / task.subtasks.length) * 100);
            
            renderTasks();
            updateStats();
            updateProgress();
        }
    }
}

// Delete task
function deleteTask(taskId) {
    if (confirm('Apakah Anda yakin ingin menghapus tugas ini?')) {
        tasks = tasks.filter(t => t.id !== taskId);
        renderTasks();
        updateStats();
        updateProgress();
    }
}

// Update statistics
function updateStats() {
    const totalTasks = tasks.length;
    const completedTasks = tasks.filter(task => task.progress === 100).length;
    const pendingTasks = totalTasks - completedTasks;

    document.getElementById('totalTasks').textContent = totalTasks;
    document.getElementById('completedTasks').textContent = completedTasks;
    document.getElementById('pendingTasks').textContent = pendingTasks;
}

// Update progress circle
function updateProgress() {
    const totalTasks = tasks.length;
    const completedTasks = tasks.filter(task => task.progress === 100).length;
    const percentage = totalTasks > 0 ? Math.round((completedTasks / totalTasks) * 100) : 0;

    document.getElementById('progressPercentage').textContent = percentage + '%';
    document.getElementById('completedCount').textContent = completedTasks;
    document.getElementById('totalCount').textContent = totalTasks;

    // Update circular progress
    const circularProgress = document.getElementById('circularProgress');
    const degrees = (percentage / 100) * 360;
    circularProgress.style.background = `conic-gradient(#ED4A81 0deg, #ED4A81 ${degrees}deg, #e9ecef ${degrees}deg)`;
}

// Modal functions
function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.style.display = 'block';
    }
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.style.display = 'none';
    }
}

// Show progress function
function showProgress() {
    alert('Fitur laporan akan segera hadir! 📊');
}

// Close modal when clicking outside
window.addEventListener('click', function(event) {
    if (event.target.classList.contains('modal')) {
        event.target.style.display = 'none';
    }
});