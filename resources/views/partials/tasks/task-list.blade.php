<!-- ================= LEFT COLUMN: TASKS & SUBTASKS ================= -->
<div>
    <div class="column-header">
        <div>
            <h2 class="column-title">
                <span>Tasks</span>
                <span class="counter-pill">{{ $completedRootTasks }}/{{ $totalRootTasks }}</span>
            </h2>
            <div class="header-subtext">
                {{ $parsedSelectedDate->format('l, F j, Y') }} 
                @if($isToday) • (Today) @endif
            </div>
        </div>
        
        <div style="display: flex; gap: 0.3rem;">
            <a href="{{ route('dashboard', ['date' => $selectedDateStr]) }}" class="btn-press {{ !request('task_status') ? 'btn-primary-press' : '' }}" style="padding: 0.25rem 0.55rem; font-size: 0.75rem;">
                All
            </a>
            <a href="{{ route('dashboard', ['date' => $selectedDateStr, 'task_status' => 'pending']) }}" class="btn-press {{ request('task_status') == 'pending' ? 'btn-primary-press' : '' }}" style="padding: 0.25rem 0.55rem; font-size: 0.75rem;">
                Pending
            </a>
        </div>
    </div>

    @include('partials.tasks.task-composer')

    @php
        $activeTasks = $tasks->where('is_completed', false);
        $completedTasks = $tasks->where('is_completed', true);
    @endphp

    <!-- Tasks Stream List -->
    <div class="tasks-stream">
        @forelse($activeTasks as $task)
            @include('partials.tasks.task-item', ['task' => $task])
        @empty
            <div class="empty-slate">
                No active tasks for this day. Add one above!
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
                    @include('partials.tasks.task-item', ['task' => $task])
                @endforeach
            </div>
        </details>
    @endif
</div>
