<div class="task-entry {{ $task->is_completed ? 'is-completed-entry' : '' }}">
    <div class="task-main-row">
        <!-- Round Checkbox with AJAX Toggle -->
        <form action="{{ route('tasks.toggle', $task) }}" method="POST">
            @csrf
            @method('PATCH')
            <input 
                type="checkbox" 
                class="round-checkbox" 
                {{ $task->is_completed ? 'checked' : '' }} 
                title="{{ $task->is_completed ? 'Mark pending' : 'Mark complete' }}"
            >
        </form>

        <div class="task-content-block">
            <div class="task-headline {{ $task->is_completed ? 'is-done' : '' }}">
                {{ $task->title }}
            </div>  
            @if($task->description)
                <div class="task-description">{{ $task->description }}</div>
            @endif
        </div>

        <div class="task-tail-actions">
            @if($task->note_id)
                <button type="button" onclick="scrollToNote({{ $task->note_id }})" class="btn-press" style="padding: 0.2rem 0.4rem; font-size: 0.75rem; border: none; box-shadow: none; background: transparent; color: var(--text-muted);" title="View Attached Note">
                    📎 View Note
                </button>
            @else
                <button type="button" onclick="createNoteForTask({{ $task->id }})" class="btn-press" style="padding: 0.2rem 0.4rem; font-size: 0.75rem; border: none; box-shadow: none; background: transparent; color: var(--text-muted);" title="Create Note for Task">
                    📝 Add Note
                </button>
            @endif
            <form action="{{ route('tasks.destroy', $task) }}" method="POST">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-danger-icon" title="Delete">✕</button>
            </form>
        </div>
    </div>

    <!-- Subtasks Tree Branch -->
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

        @if(!$isPastDate)
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
        @endif
    </div>
</div>
