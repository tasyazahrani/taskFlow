<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'notes',
        'priority',
        'deadline',
        'status',
        'subtasks'
    ];

    protected $casts = [
        'deadline' => 'datetime',
        'subtasks' => 'array'
    ];

    /**
     * Check if task is overdue
     */
    public function getIsOverdueAttribute()
    {
        return $this->deadline && $this->deadline->isPast() && $this->status !== 'completed';
    }

    /**
     * Get priority badge color
     */
    public function getPriorityColorAttribute()
    {
        return match($this->priority) {
            'high' => '#ff4757',
            'medium' => '#ffa502',
            'low' => '#2ed573',
            default => '#ffa502'
        };
    }

    /**
     * Get priority icon
     */
    public function getPriorityIconAttribute()
    {
        return match($this->priority) {
            'high' => '🔥',
            'medium' => '⚡',
            'low' => '🌱',
            default => '⚡'
        };
    }

    /**
     * Get priority label
     */
    public function getPriorityLabelAttribute()
    {
        return match($this->priority) {
            'high' => 'Tinggi',
            'medium' => 'Sedang',
            'low' => 'Rendah',
            default => 'Sedang'
        };
    }

    /**
     * Update task status based on deadline
     */
    public function updateStatus()
    {
        if ($this->status !== 'completed' && $this->is_overdue) {
            $this->update(['status' => 'overdue']);
        }
    }

    /**
     * Scope for filtering tasks
     */
    public function scopeByStatus($query, $status)
    {
        if ($status === 'all') {
            return $query;
        }
        return $query->where('status', $status);
    }

    /**
     * Scope for pending tasks
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for completed tasks
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope for overdue tasks
     */
    public function scopeOverdue($query)
    {
        return $query->where('status', 'overdue');
    }

    public function subtasks()
    {
        return $this->hasMany(Subtask::class); // atau relasi yang sesuai
    }
}