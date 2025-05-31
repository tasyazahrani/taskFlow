<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Task;
use Carbon\Carbon;

class TaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tasks = [
            [
                'title' => 'Review Project Documentation',
                'notes' => 'Review and update all project documentation for Q1 deliverables',
                'priority' => 'high',
                'deadline' => Carbon::now()->addDays(2),
                'status' => 'pending',
                'subtasks' => json_encode([
                    'Review technical specifications',
                    'Update user manual',
                    'Check API documentation'
                ])
            ]
        ];

        foreach ($tasks as $task) {
            Task::create($task);
        }
    }
}