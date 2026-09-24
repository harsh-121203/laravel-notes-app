<?php

namespace App\Http\Controllers;

use App\Models\Note;
use App\Models\Task;
use Illuminate\Http\Request;

class NoteController extends Controller
{
    /**
     * Unified Dashboard: Left side Tasks & Subtasks; Right side Notes Notebooks.
     */
    public function index(Request $request)
    {
        // 1. Fetch top-level Tasks with their eager-loaded Subtasks (prevents N+1 query problem)
        $tasksQuery = Task::whereNull('parent_id')->with('subtasks');

        // Optional filter for tasks
        if ($request->filled('task_status')) {
            if ($request->task_status === 'completed') {
                $tasksQuery->where('is_completed', true);
            } elseif ($request->task_status === 'pending') {
                $tasksQuery->where('is_completed', false);
            }
        }
        // Order tasks so pending items stay on top, completed items sink to the bottom
        $tasks = $tasksQuery->orderBy('is_completed', 'asc')
                            ->orderBy('updated_at', 'desc')
                            ->get();

        // 2. Fetch Notes with optional search query & color filter
        $notesQuery = Note::query();
        if ($request->filled('search')) {
            $term = $request->input('search');
            $notesQuery->where(function ($q) use ($term) {
                $q->where('title', 'like', "%{$term}%")
                  ->orWhere('content', 'like', "%{$term}%");
            });
        }
        if ($request->filled('color')) {
            $notesQuery->where('color', $request->color);
        }
        $notes = $notesQuery->latest()->get();

        // 3. Computed stats for header & productivity overview
        $totalRootTasks = Task::whereNull('parent_id')->count();
        $completedRootTasks = Task::whereNull('parent_id')->where('is_completed', true)->count();
        $totalNotes = Note::count();

        return view('dashboard', compact(
            'tasks',
            'notes',
            'totalRootTasks',
            'completedRootTasks',
            'totalNotes'
        ));
    }

    /**
     * Store a newly created note.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'color' => 'nullable|string|max:20',
        ]);

        Note::create([
            'title' => $validated['title'],
            'content' => $validated['content'] ?? null,
            'color' => $validated['color'] ?? '#ffffff',
            'is_completed' => false,
        ]);

        return redirect()->route('dashboard')->with('success', 'Note added to notebook!');
    }

    /**
     * Update an existing note.
     */
    public function update(Request $request, Note $note)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'color' => 'nullable|string|max:20',
        ]);

        $note->update($validated);

        return redirect()->route('dashboard')->with('success', 'Note updated!');
    }

    /**
     * Delete a note.
     */
    public function destroy(Note $note)
    {
        $note->delete();

        return redirect()->route('dashboard')->with('success', 'Note removed!');
    }
}
