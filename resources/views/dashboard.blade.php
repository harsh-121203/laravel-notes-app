@extends('layouts.app')

@section('title', 'Workspace • Tasks & Notes')

@section('styles')
<style>
    /* Two Column Split Architecture (Linux App / Todoist Clean aesthetic) */
    .columns-container {
        display: grid;
        grid-template-columns: 1fr 1.05fr;
        gap: 2.5rem;
        align-items: start;
    }

    @media (max-width: 950px) {
        .columns-container {
            grid-template-columns: 1fr;
            gap: 2rem;
        }
    }

    /* Column Headers */
    .column-header {
        display: flex;
        align-items: baseline;
        justify-content: space-between;
        margin-bottom: 1.25rem;
        padding-bottom: 0.5rem;
        border-bottom: 1px solid var(--border-line);
    }

    .column-title {
        font-size: 1.25rem;
        font-weight: 700;
        letter-spacing: -0.02em;
        color: var(--text-title);
        display: flex;
        align-items: center;
        gap: 0.6rem;
    }

    .counter-pill {
        font-size: 0.725rem;
        font-weight: 600;
        color: var(--text-muted);
        background: #f0f0f0;
        padding: 0.15rem 0.5rem;
        border-radius: var(--radius-sm);
    }

    .header-subtext {
        font-size: 0.8rem;
        color: var(--text-muted);
    }

    /* ================= LEFT SIDE: TASKS & SUBTASKS ================= */

    /* Task Composer Card */
    .task-composer-card {
        background: var(--bg-surface);
        border: 1px solid var(--border-line);
        border-radius: var(--radius-md);
        padding: 0.85rem;
        box-shadow: 0 1px 4px rgba(0,0,0,0.03);
        margin-bottom: 1.5rem;
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }
    .task-composer-card:focus-within,
    .task-composer-card.is-expanded {
        border-color: #171717;
        box-shadow: 0 4px 12px rgba(0,0,0,0.06);
    }

    .quick-task-input {
        width: 100%;
        padding: 0.65rem 0.85rem 0.65rem 2.2rem;
        font-family: inherit;
        font-size: 0.9rem;
        font-weight: 600;
        color: var(--text-title);
        background: #fcfcfc;
        border: 1px solid var(--border-line);
        border-radius: var(--radius-sm);
        outline: none;
        transition: border-color 0.12s ease, background 0.12s ease;
    }
    .quick-task-input:focus {
        border-color: #171717;
        background: #ffffff;
    }

    .quick-input-symbol {
        position: absolute;
        left: 0.75rem;
        top: 50%;
        transform: translateY(-50%);
        font-size: 1rem;
        color: var(--text-muted);
        pointer-events: none;
        user-select: none;
        font-family: monospace;
    }

    /* Expandable Slide-Out Drawer */
    .task-composer-drawer {
        display: grid;
        grid-template-rows: 0fr;
        opacity: 0;
        transition: grid-template-rows 0.28s cubic-bezier(0.16, 1, 0.3, 1), opacity 0.22s ease, margin-top 0.22s ease;
        margin-top: 0;
    }
    .task-composer-drawer-inner {
        overflow: hidden;
        display: flex;
        flex-direction: column;
        gap: 0.6rem;
    }
    .task-composer-card.is-expanded .task-composer-drawer {
        grid-template-rows: 1fr;
        opacity: 1;
        margin-top: 0.65rem;
    }

    .task-note-textarea {
        width: 100%;
        padding: 0.65rem 0.85rem;
        font-family: inherit;
        font-size: 0.85rem;
        color: var(--text-body);
        background: #fafafa;
        border: 1px solid var(--border-line);
        border-radius: var(--radius-sm);
        outline: none;
        resize: vertical;
        min-height: 85px;
        line-height: 1.45;
        transition: border-color 0.12s ease, background 0.12s ease;
    }
    .task-note-textarea:focus {
        border-color: #171717;
        background: #ffffff;
    }

    .task-composer-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding-top: 0.2rem;
    }

    .composer-hint {
        font-size: 0.75rem;
        color: var(--text-muted);
    }

    /* Clean Tree / List UI for Tasks */
    .tasks-stream {
        display: flex;
        flex-direction: column;
        border: 1px solid var(--border-line);
        background: var(--bg-surface);
        border-radius: var(--radius-md);
        box-shadow: 0 1px 3px rgba(0,0,0,0.02);
        overflow: hidden;
    }

    .task-entry {
        border-bottom: 1px solid var(--border-subtle);
        transition: background-color 0.12s ease, opacity 0.3s ease, filter 0.3s ease;
    }
    .task-entry:last-child {
        border-bottom: none;
    }
    .task-entry:hover {
        background-color: #fafafa;
    }
    .task-entry.is-completed-entry {
        opacity: 0.55;
        background-color: #fbfbfb;
        filter: grayscale(0.3);
    }
    .task-entry.is-completed-entry:hover {
        opacity: 0.85;
        filter: grayscale(0);
    }

    .subtask-leaf.is-completed-leaf {
        opacity: 0.5;
    }

    /* Completed Tasks Dedicated Collapsible Section */
    .completed-section-details {
        user-select: none;
    }
    .completed-summary-bar {
        font-size: 0.8rem;
        font-weight: 600;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.04em;
        cursor: pointer;
        padding: 0.4rem 0.2rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        list-style: none;
    }
    .completed-summary-bar::-webkit-details-marker {
        display: none;
    }
    .completed-summary-bar .toggle-icon {
        font-size: 0.7rem;
        transition: transform 0.15s ease;
    }
    .completed-section-details[open] .completed-summary-bar .toggle-icon {
        transform: rotate(180deg);
    }

    .task-main-row {
        display: flex;
        align-items: flex-start;
        padding: 0.85rem 1rem;
        gap: 0.75rem;
    }

    /* Clean Round GTK/Todoist Checkbox */
    .round-checkbox {
        appearance: none;
        width: 18px;
        height: 18px;
        border: 1.5px solid #a3a3a3;
        border-radius: 50%;
        cursor: pointer;
        display: grid;
        place-content: center;
        margin-top: 2px;
        flex-shrink: 0;
        transition: all 0.1s ease;
        background: #ffffff;
    }
    .round-checkbox:hover {
        border-color: #171717;
        transform: scale(1.05);
    }
    .round-checkbox:active {
        transform: scale(0.92);
    }
    .round-checkbox:checked {
        background-color: var(--forest);
        border-color: var(--forest);
    }
    .round-checkbox:checked::before {
        content: "";
        width: 4px;
        height: 8px;
        border: solid white;
        border-width: 0 1.5px 1.5px 0;
        transform: rotate(45deg);
        margin-bottom: 2px;
    }

    .task-content-block {
        flex: 1;
        min-width: 0;
    }

    .task-headline {
        font-size: 0.9rem;
        font-weight: 500;
        color: var(--text-title);
        line-height: 1.4;
        word-break: break-word;
    }
    .task-headline.is-done {
        text-decoration: line-through;
        color: var(--text-faint);
    }

    .task-description {
        font-size: 0.8rem;
        color: var(--text-muted);
        margin-top: 0.2rem;
        line-height: 1.4;
    }

    .task-tail-actions {
        display: flex;
        align-items: center;
        gap: 0.25rem;
        opacity: 0.6;
        transition: opacity 0.12s ease;
    }
    .task-entry:hover .task-tail-actions {
        opacity: 1;
    }

    /* Subtasks Indented Tree Line */
    .subtasks-branch {
        padding-left: 2.85rem;
        padding-right: 1rem;
        padding-bottom: 0.65rem;
        position: relative;
    }
    .subtasks-branch::before {
        content: "";
        position: absolute;
        left: 1.55rem;
        top: 0;
        bottom: 1.25rem;
        width: 1px;
        background: #e5e5e5;
    }

    .subtask-leaf {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0.35rem 0;
        font-size: 0.825rem;
    }
    .subtask-leaf-title {
        color: var(--text-body);
        font-weight: 400;
    }
    .subtask-leaf-title.is-done {
        text-decoration: line-through;
        color: var(--text-faint);
    }

    /* Inline Add Subtask trigger and input */
    .add-subtask-form {
        display: flex;
        gap: 0.4rem;
        margin-top: 0.4rem;
    }
    .inline-subtask-input {
        flex: 1;
        padding: 0.3rem 0.55rem;
        font-size: 0.775rem;
        border: 1px solid var(--border-line);
        border-radius: var(--radius-xs);
        outline: none;
        background: #fbfbfb;
        font-family: inherit;
        transition: border-color 0.1s ease, background 0.1s ease;
    }
    .inline-subtask-input:focus {
        border-color: #171717;
        background: #ffffff;
    }

    /* ================= RIGHT SIDE: NOTEBOOKS ================= */
    .notebooks-wrap {
        display: flex;
        flex-direction: column;
        gap: 1.25rem;
    }

    .notes-toolbar {
        display: flex;
        gap: 0.5rem;
    }
    .notes-search-box {
        flex: 1;
        padding: 0.55rem 0.85rem;
        font-size: 0.825rem;
        border: 1px solid var(--border-line);
        border-radius: var(--radius-sm);
        background: var(--bg-surface);
        outline: none;
        font-family: inherit;
        transition: border 0.1s ease;
    }
    .notes-search-box:focus {
        border-color: #171717;
    }

    /* Notebook Composer */
    .note-composer-card {
        background: var(--bg-surface);
        border: 1px solid var(--border-line);
        border-radius: var(--radius-md);
        padding: 1.15rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.02);
    }
    .note-input-title {
        width: 100%;
        border: none;
        border-bottom: 1px solid var(--border-subtle);
        padding: 0.4rem 0.2rem 0.6rem;
        font-family: inherit;
        font-size: 0.925rem;
        font-weight: 600;
        outline: none;
        color: var(--text-title);
        margin-bottom: 0.65rem;
    }
    .note-input-title:focus {
        border-bottom-color: #171717;
    }

    .note-input-body {
        width: 100%;
        border: none;
        padding: 0.4rem 0.2rem;
        font-family: inherit;
        font-size: 0.85rem;
        outline: none;
        color: var(--text-body);
        resize: vertical;
        min-height: 110px;
        line-height: 1.5;
    }

    .composer-bottom {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-top: 0.65rem;
        padding-top: 0.65rem;
        border-top: 1px solid var(--border-subtle);
    }

    .palette-list {
        display: flex;
        align-items: center;
        gap: 0.4rem;
    }
    .palette-circle {
        width: 16px;
        height: 16px;
        border-radius: 50%;
        cursor: pointer;
        border: 1.5px solid transparent;
        transition: transform 0.1s ease;
    }
    .palette-circle:hover {
        transform: scale(1.18);
    }
    .palette-circle.active {
        border-color: #171717;
    }

    /* Handcrafted Paper Notebook Cards */
    .notebook-deck {
        display: grid;
        grid-template-columns: 1fr;
        gap: 0.85rem;
    }

    .sheet-card {
        background: var(--sheet-bg, #ffffff);
        border: 1px solid var(--border-line);
        border-radius: var(--radius-md);
        padding: 1.15rem;
        position: relative;
        transition: border-color 0.12s ease, box-shadow 0.12s ease;
    }
    .sheet-card:hover {
        border-color: #d4d4d4;
        box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    }

    .sheet-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 0.5rem;
        margin-bottom: 0.45rem;
    }
    .sheet-title {
        font-size: 0.95rem;
        font-weight: 600;
        color: var(--text-title);
    }

    .sheet-text {
        font-size: 0.825rem;
        color: var(--text-body);
        white-space: pre-line;
        line-height: 1.5;
        margin-bottom: 0.75rem;
    }

    .sheet-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-size: 0.725rem;
        color: var(--text-muted);
        border-top: 1px solid rgba(0,0,0,0.05);
        padding-top: 0.5rem;
    }

    .empty-slate {
        text-align: center;
        padding: 2.5rem 1rem;
        color: var(--text-muted);
        font-size: 0.825rem;
        border: 1px dashed var(--border-line);
        border-radius: var(--radius-md);
        background: var(--bg-surface);
    }
    
    @keyframes noteHighlight {
        0% { box-shadow: 0 0 0 3px var(--primary, #171717); transform: scale(1.02); }
        100% { box-shadow: 0 1px 3px rgba(0,0,0,0.02); transform: scale(1); }
    }
    .note-highlight {
        animation: noteHighlight 1.5s ease-out;
    }
</style>
@endsection

@section('content')
<div class="columns-container">

    <!-- ================= LEFT COLUMN: TASKS & SUBTASKS ================= -->
    <div>
        <div class="column-header">
            <div>
                <h2 class="column-title">
                    <span>Tasks</span>
                    <span class="counter-pill">{{ $completedRootTasks }}/{{ $totalRootTasks }}</span>
                </h2>
                <div class="header-subtext">Today's workflow and subtask breakdown</div>
            </div>
            
            <div style="display: flex; gap: 0.3rem;">
                <a href="{{ route('dashboard') }}" class="btn-press {{ !request('task_status') ? 'btn-primary-press' : '' }}" style="padding: 0.25rem 0.55rem; font-size: 0.75rem;">
                    All
                </a>
                <a href="{{ route('dashboard', ['task_status' => 'pending']) }}" class="btn-press {{ request('task_status') == 'pending' ? 'btn-primary-press' : '' }}" style="padding: 0.25rem 0.55rem; font-size: 0.75rem;">
                    Pending
                </a>
            </div>
        </div>

        <!-- Tactile Pressable Task Composer with Smooth Slide-Out Drawer -->
        <form action="{{ route('tasks.store') }}" method="POST" class="task-composer-card" id="taskComposerCard">
            @csrf
            <div style="position: relative;">
                <span class="quick-input-symbol">&gt;</span>
                <input 
                    type="text" 
                    name="title" 
                    id="taskTitleInput"
                    class="quick-task-input" 
                    placeholder="Task title (e.g. Complete Web Dev Assignment)..." 
                    required 
                    autocomplete="off"
                >
            </div>
            
            <div class="task-composer-drawer" id="taskComposerDrawer">
                <div class="task-composer-drawer-inner">
                    <textarea 
                        name="note_content" 
                        id="taskNoteInput"
                        class="task-note-textarea" 
                        placeholder="Attach a detailed note or summary to this task (optional)..." 
                        rows="3"
                    ></textarea>

                    <div class="task-composer-footer">
                        <span class="composer-hint">Attach notes seamlessly to tasks</span>
                        <div style="display: flex; gap: 0.4rem;">
                            <button type="button" class="btn-press" onclick="collapseTaskComposer()" style="padding: 0.35rem 0.65rem; font-size: 0.775rem;">
                                Cancel
                            </button>
                            <button type="submit" class="btn-press btn-primary-press">
                                + Save Task
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        @php
            $activeTasks = $tasks->where('is_completed', false);
            $completedTasks = $tasks->where('is_completed', true);
        @endphp

        <!-- Tasks Stream List -->
        <div class="tasks-stream">
            @forelse($activeTasks as $task)
                <div class="task-entry">
                    <div class="task-main-row">
                        <!-- Round Checkbox with instant POST/PATCH -->
                        <form action="{{ route('tasks.toggle', $task) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <input 
                                type="checkbox" 
                                class="round-checkbox" 
                                {{ $task->is_completed ? 'checked' : '' }} 
                                onchange="this.form.submit()"
                                title="Toggle complete"
                            >
                        </form>

                        <div class="task-content-block">
                            <div class="task-headline">
                                {{ $task->title }}
                            </div>  
                            @if($task->description)
                                <div class="task-description">{{ $task->description }}</div>
                            @endif
                        </div>

                        <div class="task-tail-actions">
                            @if($task->note_id)
                                <button type="button" onclick="scrollToNote({{ $task->note_id }})" class="btn-press" style="padding: 0.2rem 0.4rem; font-size: 0.75rem; border: none; box-shadow: none; background: transparent; color: var(--text-muted);" title="View Attached Note">
                                    📎 Note
                                </button>
                            @endif
                            <form action="{{ route('tasks.destroy', $task) }}" method="POST" onsubmit="return confirm('Delete task and subtasks?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-danger-icon" title="Delete">✕</button>
                            </form>
                        </div>
                    </div>

                    <!-- Subtasks Indented Tree Branch -->
                    <div class="subtasks-branch">
                        @if($task->subtasks->count() > 0)
                            <div style="display: flex; flex-direction: column; gap: 0.15rem; margin-bottom: 0.4rem;">
                                @foreach($task->subtasks as $subtask)
                                    <div class="subtask-leaf {{ $subtask->is_completed ? 'is-completed-leaf' : '' }}">
                                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                                            <form action="{{ route('tasks.toggle', $subtask) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <input 
                                                    type="checkbox" 
                                                    class="round-checkbox" 
                                                    style="width: 15px; height: 15px;" 
                                                    {{ $subtask->is_completed ? 'checked' : '' }} 
                                                    onchange="this.form.submit()"
                                                >
                                            </form>
                                            <span class="subtask-leaf-title {{ $subtask->is_completed ? 'is-done' : '' }}">
                                                {{ $subtask->title }}
                                            </span>
                                        </div>

                                        <form action="{{ route('tasks.destroy', $subtask) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-danger-icon" style="font-size: 0.75rem; padding: 0.15rem 0.3rem;">✕</button>
                                        </form>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        <!-- Minimal Inline Add Subtask Input -->
                        <form action="{{ route('tasks.store') }}" method="POST" class="add-subtask-form">
                            @csrf
                            <input type="hidden" name="parent_id" value="{{ $task->id }}">
                            <input 
                                type="text" 
                                name="title" 
                                class="inline-subtask-input" 
                                placeholder="+ Add subtask" 
                                required 
                                autocomplete="off"
                            >
                            <button type="submit" class="btn-press" style="padding: 0.2rem 0.5rem; font-size: 0.725rem;">
                                Add
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="empty-slate">
                    No active tasks. Add one above!
                </div>
            @endforelse
        </div>

        <!-- Separate Dedicated Completed Section at Bottom -->
        @if($completedTasks->count() > 0)
            <details class="completed-section-details" open style="margin-top: 1.5rem;">
                <summary class="completed-summary-bar">
                    <span>Completed Tasks ({{ $completedTasks->count() }})</span>
                    <span class="toggle-icon">▼</span>
                </summary>

                <div class="tasks-stream completed-stream" style="margin-top: 0.75rem;">
                    @foreach($completedTasks as $task)
                        <div class="task-entry is-completed-entry">
                            <div class="task-main-row">
                                <form action="{{ route('tasks.toggle', $task) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <input 
                                        type="checkbox" 
                                        class="round-checkbox" 
                                        checked 
                                        onchange="this.form.submit()"
                                        title="Mark incomplete"
                                    >
                                </form>

                                <div class="task-content-block">
                                    <div class="task-headline is-done">
                                        {{ $task->title }}
                                    </div>
                                    @if($task->description)
                                        <div class="task-description">{{ $task->description }}</div>
                                    @endif
                                </div>

                                <div class="task-tail-actions">
                                    @if($task->note_id)
                                        <button type="button" onclick="scrollToNote({{ $task->note_id }})" class="btn-press" style="padding: 0.2rem 0.4rem; font-size: 0.75rem; border: none; box-shadow: none; background: transparent; color: var(--text-muted);" title="View Attached Note">
                                            📎 Note
                                        </button>
                                    @endif
                                    <form action="{{ route('tasks.destroy', $task) }}" method="POST" onsubmit="return confirm('Delete task and subtasks?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-danger-icon" title="Delete">✕</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </details>
        @endif
    </div>


    <!-- ================= RIGHT SIDE: NOTEBOOKS ================= -->
    <div class="notebooks-wrap">
        <div class="column-header">
            <div>
                <h2 class="column-title">
                    <span>Notebooks</span>
                    <span class="counter-pill">{{ $totalNotes }}</span>
                </h2>
                <div class="header-subtext">Lecture points, viva prep & notes</div>
            </div>
        </div>

        <!-- Notes Search Filter -->
        <form action="{{ route('dashboard') }}" method="GET" class="notes-toolbar">
            <input 
                type="text" 
                name="search" 
                class="notes-search-box" 
                placeholder="Search notes..." 
                value="{{ request('search') }}"
            >
            <button type="submit" class="btn-press">Search</button>
            @if(request('search'))
                <a href="{{ route('dashboard') }}" class="btn-press">Reset</a>
            @endif
        </form>

        <!-- Clean Notebook Sheet Composer -->
        <form action="{{ route('notes.store') }}" method="POST" class="note-composer-card">
            @csrf
            <input 
                type="text" 
                name="title" 
                class="note-input-title" 
                placeholder="Note title (e.g. CSRF Token Concept)" 
                required 
                autocomplete="off"
            >
            <textarea 
                name="content" 
                class="note-input-body" 
                placeholder="Write quick points or summary..."
            ></textarea>

            <div class="composer-bottom">
                <div class="palette-list">
                    @php
                        $palettes = [
                            '#ffffff' => 'Clean White',
                            '#fffbeb' => 'Warm Cream',
                            '#f0fdf4' => 'Pale Mint',
                            '#f5f3ff' => 'Soft Lilac',
                            '#fef2f2' => 'Pale Rose'
                        ];
                    @endphp
                    @foreach($palettes as $hex => $label)
                        <label style="cursor: pointer; line-height: 0;">
                            <input type="radio" name="color" value="{{ $hex }}" {{ $loop->first ? 'checked' : '' }} style="display: none;" onchange="selectPalette(this)">
                            <span class="palette-circle {{ $loop->first ? 'active' : '' }}" style="background: {{ $hex }}; border: 1px solid #d4d4d4;" title="{{ $label }}"></span>
                        </label>
                    @endforeach
                </div>

                <button type="submit" class="btn-press btn-primary-press">
                    Save Note
                </button>
            </div>
        </form>

        <!-- Notebook Deck Cards -->
        <div class="notebook-deck">
            @forelse($notes as $note)
                <div class="sheet-card" id="note-{{ $note->id }}" style="--sheet-bg: {{ $note->color ?? '#ffffff' }};">
                    <div class="sheet-header">
                        <div class="sheet-title">{{ $note->title }}</div>
                        <form action="{{ route('notes.destroy', $note) }}" method="POST" onsubmit="return confirm('Delete this note?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-danger-icon" title="Delete note">✕</button>
                        </form>
                    </div>

                    @if($note->content)
                        <div class="sheet-text">{{ $note->content }}</div>
                    @endif

                    <div class="sheet-footer">
                        <span>{{ $note->created_at->format('M d, Y') }}</span>
                        <span>NOTE</span>
                    </div>
                </div>
            @empty
                <div class="empty-slate">
                    No notes in notebook yet. Write a note above to store key points.
                </div>
            @endforelse
        </div>
    </div>

</div>
@endsection

@section('scripts')
<script>
    function selectPalette(radio) {
        document.querySelectorAll('.palette-circle').forEach(el => el.classList.remove('active'));
        radio.nextElementSibling.classList.add('active');
    }

    function scrollToNote(noteId) {
        const noteEl = document.getElementById('note-' + noteId);
        if (noteEl) {
            noteEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
            noteEl.classList.remove('note-highlight');
            // Trigger reflow
            void noteEl.offsetWidth;
            noteEl.classList.add('note-highlight');
        }
    }

    // Dynamic Task Composer Expand / Slide-out Logic
    const composerCard = document.getElementById('taskComposerCard');
    const taskTitleInput = document.getElementById('taskTitleInput');
    const taskNoteInput = document.getElementById('taskNoteInput');

    function expandTaskComposer() {
        if (composerCard) {
            composerCard.classList.add('is-expanded');
        }
    }

    function collapseTaskComposer() {
        if (composerCard) {
            // Only collapse if both fields are empty
            if (!taskTitleInput.value.trim() && !taskNoteInput.value.trim()) {
                composerCard.classList.remove('is-expanded');
            } else {
                taskTitleInput.value = '';
                taskNoteInput.value = '';
                composerCard.classList.remove('is-expanded');
            }
        }
    }

    if (taskTitleInput) {
        // Expand when user focuses or types in the task name
        taskTitleInput.addEventListener('focus', expandTaskComposer);
        taskTitleInput.addEventListener('input', function() {
            if (this.value.trim().length > 0) {
                expandTaskComposer();
            }
        });
    }

    // Collapse if clicked outside and inputs are empty
    document.addEventListener('click', function(e) {
        if (composerCard && !composerCard.contains(e.target)) {
            if (!taskTitleInput.value.trim() && !taskNoteInput.value.trim()) {
                composerCard.classList.remove('is-expanded');
            }
        }
    });
</script>
@endsection
