// Modal functions
function openModal(modalId) {
    document.getElementById(modalId).style.display = 'block';
    document.body.style.overflow = 'hidden';
}

function closeModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
    document.body.style.overflow = 'auto';
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modals = document.querySelectorAll('.modal');
    modals.forEach(modal => {
        if (event.target === modal) {
            modal.style.display = 'none';
            document.body.style.overflow = 'auto';
        }
    });
}

class TaskManager {
    constructor() {
        this.tasks = [];
        this.currentFilter = 'all';
        this.editingTaskId = null;
        this.init();
    }

    init() {
        this.bindEvents();
        this.setDefaultDateTime();
        this.updateTaskDisplay();
    }

    bindEvents() {
        // Form submission
        document.getElementById('taskForm').addEventListener('submit', (e) => {
            e.preventDefault();
            if (this.editingTaskId) {
                this.updateTask();
            } else {
                this.addTask();
            }
        });

        // Filter tabs
        document.querySelectorAll('.filter-tab').forEach(tab => {
            tab.addEventListener('click', () => {
                document.querySelectorAll('.filter-tab').forEach(t => 
                    t.classList.remove('active'));
                tab.classList.add('active');
                this.currentFilter = tab.dataset.filter;
                this.updateTaskDisplay();
            });
        });
    }

    setDefaultDateTime() {
        const now = new Date();
        now.setHours(now.getHours() + 1);
        const formatted = now.toISOString().slice(0, 16);
        document.getElementById('taskDeadline').value = formatted;
    }

    addTask() {
        const title = document.getElementById('taskTitle').value.trim();
        const notes = document.getElementById('taskNotes').value.trim();
        const deadline = document.getElementById('taskDeadline').value;
        const priority = document.getElementById('taskPriority').value;
        const subtasksText = document.getElementById('taskSubtasks').value.trim();

        if (!title || !deadline) return;

        // Parse subtasks
        const subtasks = subtasksText 
            ? subtasksText.split('\n').filter(task => task.trim()).map(task => ({
                id: Date.now() + Math.random(),
                text: task.trim(),
                completed: false
            }))
            : [];

        const task = {
            id: Date.now(),
            title,
            description: notes,
            deadline: new Date(deadline),
            priority,
            subtasks,
            completed: false,
            createdAt: new Date()
        };

        this.tasks.push(task);
        this.clearForm();
        this.updateTaskDisplay();
        closeModal('taskModal');
    }

    editTask(taskId) {
        const task = this.tasks.find(t => t.id === taskId);
        if (!task) return;

        this.editingTaskId = taskId;

        // Fill form with task data
        document.getElementById('taskTitle').value = task.title;
        document.getElementById('taskNotes').value = task.description || '';
        document.getElementById('taskPriority').value = task.priority;
        
        // Format datetime for input
        const deadline = new Date(task.deadline);
        const formatted = deadline.toISOString().slice(0, 16);
        document.getElementById('taskDeadline').value = formatted;

        // Fill subtasks
        const subtasksText = task.subtasks.map(st => st.text).join('\n');
        document.getElementById('taskSubtasks').value = subtasksText;

        // Update modal title and button
        document.getElementById('modalTitle').innerHTML = '✏️ Edit Tugas';
        document.getElementById('submitBtn').innerHTML = '💾 Update Tugas';

        openModal('taskModal');
    }

    updateTask() {
        const task = this.tasks.find(t => t.id === this.editingTaskId);
        if (!task) return;

        const title = document.getElementById('taskTitle').value.trim();
        const notes = document.getElementById('taskNotes').value.trim();
        const deadline = document.getElementById('taskDeadline').value;
        const priority = document.getElementById('taskPriority').value;
        const subtasksText = document.getElementById('taskSubtasks').value.trim();

        if (!title || !deadline) return;

        // Parse subtasks - preserve completed status for existing subtasks
        const newSubtasks = subtasksText 
            ? subtasksText.split('\n').filter(taskText => taskText.trim()).map(taskText => {
                const trimmedText = taskText.trim();
                // Check if this subtask already exists
                const existingSubtask = task.subtasks.find(st => st.text === trimmedText);
                return existingSubtask || {
                    id: Date.now() + Math.random(),
                    text: trimmedText,
                    completed: false
                };
            })
            : [];

        // Update task properties
        task.title = title;
        task.description = notes;
        task.deadline = new Date(deadline);
        task.priority = priority;
        task.subtasks = newSubtasks;

        this.clearForm();
        this.updateTaskDisplay();
        closeModal('taskModal');
    }

    clearForm() {
        document.getElementById('taskTitle').value = '';
        document.getElementById('taskNotes').value = '';
        document.getElementById('taskSubtasks').value = '';
        document.getElementById('taskPriority').value = 'medium';
        this.setDefaultDateTime();
        
        // Reset modal to add mode
        this.editingTaskId = null;
        document.getElementById('modalTitle').innerHTML = '➕ Tambah Tugas Baru';
        document.getElementById('submitBtn').innerHTML = '✨ Tambah Tugas';
    }

    deleteTask(taskId) {
        this.tasks = this.tasks.filter(task => task.id !== taskId);
        this.updateTaskDisplay();
    }

    toggleComplete(taskId) {
        const task = this.tasks.find(t => t.id === taskId);
        if (task) {
            task.completed = !task.completed;
            this.updateTaskDisplay();
        }
    }

    toggleSubtask(taskId, subtaskId) {
        const task = this.tasks.find(t => t.id === taskId);
        if (task) {
            const subtask = task.subtasks.find(s => s.id === subtaskId);
            if (subtask) {
                subtask.completed = !subtask.completed;
                this.updateTaskDisplay();
            }
        }
    }

    getFilteredTasks() {
        const now = new Date();
        
        return this.tasks.filter(task => {
            switch (this.currentFilter) {
                case 'pending':
                    return !task.completed;
                case 'completed':
                    return task.completed;
                case 'overdue':
                    return !task.completed && task.deadline < now;
                default:
                    return true;
            }
        }).sort((a, b) => {
            // Sort by priority first, then by deadline
            const priorityOrder = { high: 3, medium: 2, low: 1 };
            const priorityDiff = priorityOrder[b.priority] - priorityOrder[a.priority];
            
            if (priorityDiff !== 0) return priorityDiff;
            return a.deadline - b.deadline;
        });
    }

    formatDeadline(deadline) {
        const now = new Date();
        const diff = deadline - now;
        const days = Math.floor(diff / (1000 * 60 * 60 * 24));
        const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));

        const formatted = deadline.toLocaleDateString('id-ID', {
            day: 'numeric',
            month: 'short',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });

        if (diff < 0) {
            return { text: `${formatted} (Terlambat)`, class: 'overdue' };
        } else if (days === 0 && hours <= 24) {
            return { text: `${formatted} (Hari ini)`, class: 'due-soon' };
        } else if (days === 1) {
            return { text: `${formatted} (Besok)`, class: 'due-soon' };
        } else {
            return { text: formatted, class: '' };
        }
    }

    updateTaskDisplay() {
        const container = document.getElementById('taskContainer');
        const filteredTasks = this.getFilteredTasks();

        if (filteredTasks.length === 0) {
            container.innerHTML = '<div class="empty-state">Tidak ada tugas untuk ditampilkan.</div>';
            return;
        }

        const tasksHTML = filteredTasks.map(task => {
            const deadlineInfo = this.formatDeadline(task.deadline);
            const priorityEmoji = task.priority === 'high' ? '🔥' : task.priority === 'medium' ? '⚡' : '🌱';
            const priorityText = task.priority === 'high' ? 'Tinggi' : task.priority === 'medium' ? 'Sedang' : 'Rendah';
            
            const subtasksHTML = task.subtasks.length > 0 ? `
                <div class="subtasks-list">
                    ${task.subtasks.map(subtask => `
                        <div class="subtask-item">
                            <div class="subtask-checkbox ${subtask.completed ? 'completed' : ''}" 
                                 onclick="taskManager.toggleSubtask(${task.id}, ${subtask.id})"></div>
                            <span style="${subtask.completed ? 'text-decoration: line-through; opacity: 0.6;' : ''}">${subtask.text}</span>
                        </div>
                    `).join('')}
                </div>
            ` : '';
            
            return `
                <div class="task-item priority-${task.priority} ${task.completed ? 'completed' : ''}">
                    <div class="task-actions">
                        <button class="task-action-btn complete-btn" onclick="taskManager.toggleComplete(${task.id})" title="${task.completed ? 'Tandai belum selesai' : 'Tandai selesai'}">
                            ${task.completed ? '↶' : '✓'}
                        </button>
                        <button class="task-action-btn edit-btn" onclick="taskManager.editTask(${task.id})" title="Edit tugas">
                            ✏️
                        </button>
                        <button class="task-action-btn delete-btn" onclick="taskManager.deleteTask(${task.id})" title="Hapus tugas">
                            ×
                        </button>
                    </div>
                    
                    <div class="task-header">
                        <div>
                            <div class="task-title">${task.title}</div>
                            ${task.description ? `<div class="task-description">${task.description}</div>` : ''}
                        </div>
                    </div>
                    
                    ${subtasksHTML}
                    
                    <div class="task-meta">
                        <div class="task-deadline ${deadlineInfo.class}">⏰ ${deadlineInfo.text}</div>
                        <div class="priority-badge ${task.priority}">${priorityEmoji} ${priorityText}</div>
                    </div>
                </div>
            `;
        }).join('');

        container.innerHTML = tasksHTML;
    }
}

// Initialize the task manager
const taskManager = new TaskManager();

// Add some sample tasks for demonstration
setTimeout(() => {
    const sampleTasks = [
        {
            id: 1,
            title: "Menyelesaikan laporan proyek",
            description: "Laporan akhir untuk proyek Q1 harus diselesaikan dan dikirim ke klien",
            deadline: new Date(Date.now() + 2 * 24 * 60 * 60 * 1000), // 2 days from now
            priority: "high",
            subtasks: [
                { id: 11, text: "Kumpulkan data penjualan", completed: true },
                { id: 12, text: "Analisis performa tim", completed: false },
                { id: 13, text: "Buat presentasi", completed: false }
            ],
            completed: false,
            createdAt: new Date()
        },
        {
            id: 2,
            title: "Meeting dengan tim marketing",
            description: "Diskusi strategi pemasaran untuk Q2",
            deadline: new Date(Date.now() + 1 * 24 * 60 * 60 * 1000), // Tomorrow
            priority: "medium",
            subtasks: [],
            completed: false,
            createdAt: new Date()
        },
        {
            id: 3,
            title: "Review kode program",
            description: "",
            deadline: new Date(Date.now() + 5 * 24 * 60 * 60 * 1000), // 5 days from now
            priority: "low",
            subtasks: [
                { id: 31, text: "Review backend API", completed: true },
                { id: 32, text: "Test frontend components", completed: true }
            ],
            completed: true,
            createdAt: new Date()
        }
    ];
    
    taskManager.tasks = sampleTasks;
    taskManager.updateTaskDisplay();
}, 100);