<!-- ================= TOP CALENDAR BAR ================= -->
<div class="calendar-widget-card" id="calendarWidget">
    <div class="calendar-header-row">
        <div class="calendar-current-label">
            <span>🗓️ {{ $parsedSelectedDate->format('M Y') }}</span>
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

    <!-- Monthly Calendar Grid -->
    <div class="monthly-calendar-grid">
        <div class="cal-day-header">Su</div>
        <div class="cal-day-header">Mo</div>
        <div class="cal-day-header">Tu</div>
        <div class="cal-day-header">We</div>
        <div class="cal-day-header">Th</div>
        <div class="cal-day-header">Fr</div>
        <div class="cal-day-header">Sa</div>

        @php
            $startOfMonth = $parsedSelectedDate->copy()->startOfMonth();
            $startDate = $startOfMonth->copy()->startOfWeek(\Carbon\Carbon::SUNDAY);
        @endphp

        @for($i = 0; $i < 42; $i++)
            @php
                $dayCursor = $startDate->copy()->addDays($i);
                $dayCursorStr = $dayCursor->toDateString();
                $isCellSelected = ($dayCursorStr === $selectedDateStr);
                $isCellToday = $dayCursor->isToday();
                $isCellPast = $dayCursor->lt(now()->startOfDay());
                $isCurrentMonth = $dayCursor->month === $parsedSelectedDate->month;
                $dayData = $taskCountsByDate->get($dayCursorStr);
                $totalCount = $dayData ? $dayData->count : 0;
                $doneCount = $dayData ? $dayData->completed_count : 0;
            @endphp
            <a 
                href="{{ route('dashboard', ['date' => $dayCursorStr]) }}" 
                class="cal-day-cell {{ $isCellSelected ? 'is-selected' : '' }} {{ $isCellToday ? 'is-today' : '' }} {{ !$isCurrentMonth ? 'is-other-month' : '' }} {{ $isCellPast ? 'is-past' : '' }}"
                title="{{ $dayCursor->format('l, M d, Y') }}"
            >
                <span class="cal-day-num">{{ $dayCursor->format('j') }}</span>
                @if($totalCount > 0)
                    <div class="cal-day-dot {{ $doneCount >= $totalCount && $totalCount > 0 ? 'done' : '' }}"></div>
                @endif
            </a>
        @endfor
    </div>
</div>
