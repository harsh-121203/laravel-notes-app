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
        $today = now()->toDateString();

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'note_content' => 'nullable|string',
            'task_date' => ['nullable', 'date', 'after_or_equal:' . $today],
            // parent_id must refer to a top‑level task (no parent) to avoid sub‑subtasks
            'parent_id' => ['nullable', 'exists:tasks,id', function ($attribute, $value, $fail) {
                if ($value) {
                    $parent = Task::find($value);
                    if ($parent && $parent->parent_id !== null) {
                        $fail('You cannot create a subtask of a subtask.');
                    }
                }
            }],
        ], [
            'task_date.after_or_equal' => 'You can only schedule tasks for today or a future date.',
        ]);

        $parentTask = null;
        if (!empty($validated['parent_id'])) {
            $parentTask = Task::find($validated['parent_id']);
        }

        // Subtasks inherit their parent task's date; root tasks use provided date or default to today
        $taskDate = $parentTask ? $parentTask->task_date?->format('Y-m-d') : ($validated['task_date'] ?? $today);

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

        $task = Task::create([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? $validated['note_content'] ?? null,
            'parent_id' => $validated['parent_id'] ?? null,
            'note_id' => $noteId,
            'task_date' => $taskDate,
            'is_completed' => false,
        ]);

        $message = empty($validated['parent_id']) ? 'Task created successfully!' : 'Subtask added successfully!';

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'task' => $task->load(['subtasks', 'note']),
            ]);
        }

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

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Task updated successfully!',
                'task' => $task,
            ]);
        }

        return redirect()->back()->with('success', 'Task updated successfully!');
    }

    /**
     * Toggle completion of task or subtask.
     */
    public function toggle(Request $request, Task $task)
    {
        $task->update([
            'is_completed' => !$task->is_completed,
        ]);

        $message = $task->is_completed ? 'Task completed!' : 'Task marked as pending!';

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'is_completed' => $task->is_completed,
                'message' => $message,
            ]);
        }

        return redirect()->back()->with('success', $message);
    }

    /**
     * Delete task and any cascading subtasks.
     */
    public function destroy(Request $request, Task $task)
    {
        $task->delete();

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Task removed!',
            ]);
        }

        return redirect()->back()->with('success', 'Task removed!');
    }
}
