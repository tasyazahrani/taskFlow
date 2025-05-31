<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;

class TaskController extends Controller
{
    /**
     * Display task management page
     */
    public function index(Request $request)
    {
        $filter = $request->get('filter', 'all');
        
        $query = Task::query()->orderBy('created_at', 'desc');
        
        // Apply filter
        if ($filter !== 'all') {
            $query->byStatus($filter);
        }
        
        $tasks = $query->get();
        
        // Update overdue tasks
        $this->updateOverdueTasks();
        
        // Get task counts for tabs
        $taskCounts = [
            'all' => Task::count(),
            'pending' => Task::pending()->count(),
            'completed' => Task::completed()->count(),
            'overdue' => Task::overdue()->count(),
        ];
        
        // If it's an AJAX request, return JSON
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'tasks' => $tasks,
                'counts' => $taskCounts
            ]);
        }
        
        return view('tasks.index', compact('tasks', 'filter', 'taskCounts'));
    }

    /**
     * Store a new task
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'notes' => 'nullable|string',
            'priority' => 'required|in:high,medium,low',
            'deadline' => 'nullable|date',
            'subtasks' => 'nullable|string'
        ]);

        // Process subtasks
        $subtasks = null;
        if ($request->subtasks) {
            $subtasks = array_filter(
                array_map('trim', explode("\n", $request->subtasks)),
                fn($item) => !empty($item)
            );
        }

        $task = Task::create([
            'title' => $request->title,
            'notes' => $request->notes,
            'priority' => $request->priority,
            'deadline' => $request->deadline ? Carbon::parse($request->deadline) : null,
            'subtasks' => $subtasks,
            'status' => 'pending'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Tugas berhasil ditambahkan!',
            'task' => $task->fresh()
        ]);
    }

    /**
     * Update task
     */
    public function update(Request $request, Task $task): JsonResponse
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'notes' => 'nullable|string',
            'priority' => 'required|in:high,medium,low',
            'deadline' => 'nullable|date',
            'subtasks' => 'nullable|string'
        ]);

        // Process subtasks
        $subtasks = null;
        if ($request->subtasks) {
            $subtasks = array_filter(
                array_map('trim', explode("\n", $request->subtasks)),
                fn($item) => !empty($item)
            );
        }

        $task->update([
            'title' => $request->title,
            'notes' => $request->notes,
            'priority' => $request->priority,
            'deadline' => $request->deadline ? Carbon::parse($request->deadline) : null,
            'subtasks' => $subtasks
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Tugas berhasil diperbarui!',
            'task' => $task->fresh()
        ]);
    }

    /**
     * Toggle task completion
     */
    public function toggleComplete(Task $task): JsonResponse
    {
        $newStatus = $task->status === 'completed' ? 'pending' : 'completed';
        $task->update(['status' => $newStatus]);

        return response()->json([
            'success' => true,
            'message' => $newStatus === 'completed' ? 'Tugas telah diselesaikan!' : 'Tugas dikembalikan ke status aktif',
            'status' => $newStatus
        ]);
    }

    /**
     * Delete task
     */
    public function destroy(Task $task): JsonResponse
    {
        $task->delete();

        return response()->json([
            'success' => true,
            'message' => 'Tugas berhasil dihapus!'
        ]);
    }

    /**
     * Get task data for editing
     */
    public function show(Task $task): JsonResponse
    {
        $taskData = $task->toArray();
        
        // Format deadline for datetime-local input
        if ($task->deadline) {
            $taskData['deadline'] = $task->deadline->format('Y-m-d\TH:i');
        }
        
        // Format subtasks for textarea
        if ($task->subtasks) {
            $taskData['subtasks'] = implode("\n", $task->subtasks);
        }

        return response()->json($taskData);
    }

    /**
     * Get tasks data for AJAX
     */
    public function getTasks(Request $request): JsonResponse
    {
        $filter = $request->get('filter', 'all');
        
        $query = Task::query()->orderBy('created_at', 'desc');
        
        if ($filter !== 'all') {
            $query->byStatus($filter);
        }
        
        $tasks = $query->get();
        
        // Update overdue tasks before returning
        $this->updateOverdueTasks();
        
        return response()->json([
            'tasks' => $tasks,
            'counts' => [
                'all' => Task::count(),
                'pending' => Task::pending()->count(),
                'completed' => Task::completed()->count(),
                'overdue' => Task::overdue()->count(),
            ]
        ]);
    }

    /**
     * Update overdue tasks
     */
    private function updateOverdueTasks()
    {
        Task::where('deadline', '<', now())
            ->where('status', 'pending')
            ->update(['status' => 'overdue']);
    }
}