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
        $todayStr = now()->toDateString();
        $selectedDate = $request->query('date', $todayStr);

        // Validate selectedDate format, fallback to today if invalid
        try {
            $parsedSelectedDate = \Carbon\Carbon::parse($selectedDate)->startOfDay();
            $selectedDateStr = $parsedSelectedDate->toDateString();
        } catch (\Exception $e) {
            $parsedSelectedDate = now()->startOfDay();
            $selectedDateStr = $todayStr;
        }

        $isPastDate = $parsedSelectedDate->lt(now()->startOfDay());
        $isToday = $parsedSelectedDate->isToday();

        // 1. Fetch top-level Tasks for the selected date with eager-loaded subtasks and note
        $tasksQuery = Task::whereNull('parent_id')
            ->whereDate('task_date', $selectedDateStr)
            ->with(['subtasks', 'note']);

        // Optional filter for tasks by completion status
        if ($request->filled('task_status')) {
            if ($request->task_status === 'completed') {
                $tasksQuery->where('is_completed', true);
            } elseif ($request->task_status === 'pending') {
                $tasksQuery->where('is_completed', false);
            }
        }

        // Order tasks so pending items stay on top, completed items sink to bottom
        $tasks = $tasksQuery->orderBy('is_completed', 'asc')
                            ->orderBy('updated_at', 'desc')
                            ->get();

        // 2. Fetch task counts per day for the current visible calendar month to render indicators/dots
        $viewMonth = $parsedSelectedDate->copy();
        $startOfMonth = $viewMonth->copy()->startOfMonth()->subDays(7)->toDateString();
        $endOfMonth = $viewMonth->copy()->endOfMonth()->addDays(7)->toDateString();

        $taskCountsByDate = Task::whereNull('parent_id')
            ->whereBetween('task_date', [$startOfMonth, $endOfMonth])
            ->selectRaw('task_date, count(*) as count, sum(case when is_completed = 1 then 1 else 0 end) as completed_count')
            ->groupBy('task_date')
            ->get()
            ->keyBy(function($item) {
                return \Carbon\Carbon::parse($item->task_date)->toDateString();
            });

        // 3. Fetch Notes with optional search query & color filter
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

        // 4. Computed stats for selected day
        $totalRootTasks = Task::whereNull('parent_id')->whereDate('task_date', $selectedDateStr)->count();
        $completedRootTasks = Task::whereNull('parent_id')->whereDate('task_date', $selectedDateStr)->where('is_completed', true)->count();
        $totalNotes = Note::count();

        // Fetch all tasks for the attach dropdown
        $allTasks = Task::whereNull('parent_id')->orderBy('created_at', 'desc')->get();

        return view('dashboard', compact(
            'tasks',
            'allTasks',
            'notes',
            'totalRootTasks',
            'completedRootTasks',
            'totalNotes',
            'selectedDateStr',
            'parsedSelectedDate',
            'isPastDate',
            'isToday',
            'taskCountsByDate'
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
            'task_id' => 'nullable|exists:tasks,id',
        ]);

        $note = Note::create([
            'title' => $validated['title'],
            'content' => $validated['content'] ?? null,
            'color' => $validated['color'] ?? '#ffffff',
            'is_completed' => false,
        ]);

        if (!empty($validated['task_id'])) {
            $task = Task::find($validated['task_id']);
            if ($task) {
                $task->update(['note_id' => $note->id]);
            }
        }

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Note added to notebook!',
                'note' => $note,
            ]);
        }

        return redirect()->back()->with('success', 'Note added to notebook!');
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

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Note updated!',
                'note' => $note,
            ]);
        }

        return redirect()->back()->with('success', 'Note updated!');
    }

    /**
     * Delete a note.
     */
    public function destroy(Request $request, Note $note)
    {
        $note->delete();

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Note removed!',
            ]);
        }

        return redirect()->back()->with('success', 'Note removed!');
    }
}
