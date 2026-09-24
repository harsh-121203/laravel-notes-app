<?php

namespace App\Http\Controllers;

use App\Models\Note;
use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    /**
     * Store a new task or subtask.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'note_content' => 'nullable|string',
            // parent_id must refer to a top‑level task (no parent) to avoid sub‑subtasks
            'parent_id' => ['nullable', 'exists:tasks,id', function ($attribute, $value, $fail) {
                if ($value) {
                    $parent = Task::find($value);
                    if ($parent && $parent->parent_id !== null) {
                        $fail('You cannot create a subtask of a subtask.');
                    }
                }
            }],
        ]);

        $noteId = null;
        if (!empty($validated['note_content'])) {
            $note = Note::create([
                'title' => 'Task Note: ' . $validated['title'],
                'content' => $validated['note_content'],
                'color' => '#ffffff', // Default color
                'is_completed' => false,
            ]);
            $noteId = $note->id;
        }

        Task::create([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? $validated['note_content'] ?? null,
            'parent_id' => $validated['parent_id'] ?? null,
            'note_id' => $noteId,
            'is_completed' => false,
        ]);

        $message = empty($validated['parent_id']) ? 'Task created successfully!' : 'Subtask added successfully!';
        return redirect()->back()->with('success', $message);
    }

    /**
     * Update an existing task or subtask.
     */
    public function update(Request $request, Task $task)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $task->update($validated);

        return redirect()->back()->with('success', 'Task updated successfully!');
    }

    /**
     * Toggle completion of task or subtask.
     */
    public function toggle(Task $task)
    {
        $task->update([
            'is_completed' => !$task->is_completed,
        ]);

        return redirect()->back()->with('success', $task->is_completed ? 'Task completed!' : 'Task marked as pending!');
    }

    /**
     * Delete task and any cascading subtasks.
     */
    public function destroy(Task $task)
    {
        $task->delete();

        return redirect()->back()->with('success', 'Task removed!');
    }
}
