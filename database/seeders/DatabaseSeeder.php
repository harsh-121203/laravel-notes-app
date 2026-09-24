<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create Sample Tasks & Subtasks
        $task1 = \App\Models\Task::create([
            'title' => 'Finalize Laravel Presentation Slides',
            'description' => 'Cover MVC architecture, Routing, Eloquent ORM, and Blade templates.',
            'is_completed' => false,
        ]);

        \App\Models\Task::create([
            'parent_id' => $task1->id,
            'title' => 'Export visual architecture diagram',
            'description' => 'Include HTTP request lifecycle diagram from browser to controller and view.',
            'is_completed' => true,
        ]);

        \App\Models\Task::create([
            'parent_id' => $task1->id,
            'title' => 'Rehearse 2-minute project elevator pitch',
            'description' => 'Explain why lightweight SQLite was picked over heavy MySQL servers.',
            'is_completed' => false,
        ]);

        $task2 = \App\Models\Task::create([
            'title' => 'Review Viva Cheat Sheet',
            'description' => 'Read ACADEMIC_VIVA_GUIDE.md covering CSRF tokens, RESTful routes, and method spoofing.',
            'is_completed' => true,
        ]);

        \App\Models\Task::create([
            'parent_id' => $task2->id,
            'title' => 'Understand CSRF @csrf mechanism',
            'is_completed' => true,
        ]);

        $task3 = \App\Models\Task::create([
            'title' => 'Submit Academic Code Repository',
            'description' => 'Ensure clean git history and clear setup steps in README.',
            'is_completed' => false,
        ]);

        // 2. Create Sample Notebook Notes
        \App\Models\Note::create([
            'title' => 'MVC Concept Summary',
            'content' => "• Model: Manages data and database interaction (Task.php, Note.php)\n• View: Presentation layer using Blade templates\n• Controller: Handles user request logic and bridges Model with View.",
            'color' => '#fef3c7', // Warm Amber
        ]);

        \App\Models\Note::create([
            'title' => 'Eloquent ORM Quick Reference',
            'content' => "Task::whereNull('parent_id')->with('subtasks')->get()\n- with('subtasks') solves the N+1 query issue by eager loading children in 2 queries instead of N+1.",
            'color' => '#dcfce7', // Sage / Mint
        ]);

        \App\Models\Note::create([
            'title' => 'Viva Checklist & Demo Steps',
            'content' => "1. Start server: php artisan serve\n2. Demonstrate creating task & subtask on the left\n3. Demonstrate notebook cards and search on the right.",
            'color' => '#e0e7ff', // Soft Lavender/Indigo
        ]);
    }
}
