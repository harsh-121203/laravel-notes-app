@if($isPastDate)
    <!-- Read-Only Past Date Lock Notice -->
    <div class="past-date-lock-banner">
        <span>🔒</span>
        <span>
            Viewing past archive for <strong>{{ $parsedSelectedDate->format('M j, Y') }}</strong>. Past tasks can only be checked off or deleted. New tasks cannot be scheduled in the past.
        </span>
    </div>
@else
    <!-- Tactile Pressable Task Composer with Smooth Slide-Out Drawer -->
    <form action="{{ route('tasks.store') }}" method="POST" class="task-composer-card" id="taskComposerCard">
        @csrf
        <!-- Enforce task scheduling on selected future/today date -->
        <input type="hidden" name="task_date" value="{{ $selectedDateStr }}">

        <div style="position: relative;">
            <span class="quick-input-symbol">&gt;</span>
            <input 
                type="text" 
                name="title" 
                id="taskTitleInput"
                class="quick-task-input" 
                placeholder="Task for {{ $isToday ? 'Today' : $parsedSelectedDate->format('M j') }} (e.g. Complete assignment)..." 
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
                    <span class="composer-hint">Target Date: {{ $parsedSelectedDate->format('M j, Y') }}</span>
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
@endif
