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

    @include('partials.notes.note-composer')

    <!-- Notebook Deck Cards -->
    <div class="notebook-deck">
        @forelse($notes as $note)
            @include('partials.notes.note-card', ['note' => $note])
        @empty
            <div class="empty-slate">
                No notes in notebook yet. Write a note above to store key points.
            </div>
        @endforelse
    </div>
</div>
