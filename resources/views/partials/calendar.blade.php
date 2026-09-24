<!-- ================= TOP CALENDAR BAR ================= -->
<div class="calendar-widget-card" id="calendarWidget">
    <div class="calendar-header-row">
        <div class="calendar-current-label">
            <span>🗓️ {{ $parsedSelectedDate->format('F Y') }}</span>
            @if($isToday)
                <span class="counter-pill status-badge-today">TODAY</span>
            @elseif($isPastDate)
                <span class="counter-pill status-badge-past">PAST LOG</span>
            @else
                <span class="counter-pill status-badge-upcoming">UPCOMING</span>
            @endif
        </div>

        <div class="calendar-nav-group">
            <div class="calendar-quick-pills">
                <a href="{{ route('dashboard', ['date' => now()->toDateString()]) }}" class="btn-press {{ $isToday ? 'btn-primary-press' : '' }}" style="padding: 0.25rem 0.6rem; font-size: 0.75rem;">
                    Today
                </a>
                <a href="{{ route('dashboard', ['date' => now()->addDay()->toDateString()]) }}" class="btn-press {{ $selectedDateStr == now()->addDay()->toDateString() ? 'btn-primary-press' : '' }}" style="padding: 0.25rem 0.6rem; font-size: 0.75rem;">
                    Tomorrow
                </a>
            </div>

            <!-- Previous Day Navigation -->
            <a href="{{ route('dashboard', ['date' => $parsedSelectedDate->copy()->subDay()->toDateString()]) }}" class="btn-press" title="Previous Day" style="padding: 0.25rem 0.55rem; font-size: 0.8rem;">
                ◀
            </a>

            <!-- Native HTML5 Date Picker for Quick Jump to Any Date -->
            <input 
                type="date" 
                value="{{ $selectedDateStr }}" 
                onchange="navigateWorkspace('{{ route('dashboard') }}?date=' + this.value)"
                class="btn-press" 
                style="padding: 0.2rem 0.45rem; font-size: 0.775rem; cursor: pointer; outline: none;"
                title="Jump to date"
            >

            <!-- Next Day Navigation -->
            <a href="{{ route('dashboard', ['date' => $parsedSelectedDate->copy()->addDay()->toDateString()]) }}" class="btn-press" title="Next Day" style="padding: 0.25rem 0.55rem; font-size: 0.8rem;">
                ▶
            </a>
        </div>
    </div>

    <!-- 7-Day Rolling Day Strip around Selected Date -->
    <div class="week-strip-grid">
        @for($i = -3; $i <= 3; $i++)
            @php
                $dayCursor = $parsedSelectedDate->copy()->addDays($i);
                $dayCursorStr = $dayCursor->toDateString();
                $isCellSelected = ($dayCursorStr === $selectedDateStr);
                $isCellToday = $dayCursor->isToday();
                $isCellPast = $dayCursor->lt(now()->startOfDay());
                $dayData = $taskCountsByDate->get($dayCursorStr);
                $totalCount = $dayData ? $dayData->count : 0;
                $doneCount = $dayData ? $dayData->completed_count : 0;
            @endphp
            <a 
                href="{{ route('dashboard', ['date' => $dayCursorStr]) }}" 
                class="day-pill-cell {{ $isCellSelected ? 'is-selected-day' : '' }} {{ $isCellToday ? 'is-today-indicator' : '' }} {{ $isCellPast ? 'is-past-day' : '' }}"
                title="{{ $dayCursor->format('l, M d, Y') }}"
            >
                <span class="day-pill-name">{{ $dayCursor->format('D') }}</span>
                <span class="day-pill-num">{{ $dayCursor->format('j') }}</span>
                <div class="day-pill-dots">
                    @if($totalCount > 0)
                        @for($d = 0; $d < min($totalCount, 3); $d++)
                            <span class="task-dot {{ $d < $doneCount ? 'done' : '' }}"></span>
                        @endfor
                    @endif
                </div>
            </a>
        @endfor
    </div>
</div>
