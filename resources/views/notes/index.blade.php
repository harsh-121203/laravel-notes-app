@extends('layouts.app')

@section('title', 'All Notes & Tasks - Notes App')

@section('styles')
<style>
    /* Stats Bar */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 1rem;
        margin-bottom: 2rem;
    }
    .stat-card {
        background: var(--card-bg);
        border: 1px solid var(--border);
        border-radius: var(--radius-md);
        padding: 1rem 1.25rem;
        box-shadow: var(--shadow-sm);
    }
    .stat-label {
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: var(--text-muted);
        font-weight: 600;
    }
    .stat-value {
        font-size: 1.75rem;
        font-weight: 700;
        margin-top: 0.25rem;
        color: var(--text-main);
    }

    /* Filter & Search Bar */
    .controls-wrapper {
        background: var(--card-bg);
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        padding: 1rem 1.25rem;
        margin-bottom: 2rem;
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
        align-items: center;
        justify-content: space-between;
        box-shadow: var(--shadow-sm);
    }
    .search-form {
        display: flex;
        gap: 0.5rem;
        flex: 1;
        min-width: 260px;
    }
    .search-input {
        flex: 1;
        padding: 0.55rem 0.85rem;
        border: 1px solid var(--border);
        border-radius: var(--radius-md);
        font-size: 0.9rem;
        outline: none;
        transition: border 0.15s;
    }
    .search-input:focus {
        border-color: var(--primary);
    }
    .filter-tabs {
        display: flex;
        gap: 0.35rem;
        background: #f1f5f9;
        padding: 0.25rem;
        border-radius: var(--radius-md);
    }
    .filter-tab {
        padding: 0.35rem 0.85rem;
        font-size: 0.825rem;
        font-weight: 600;
        color: var(--text-muted);
        text-decoration: none;
        border-radius: 8px;
        transition: all 0.15s;
    }
    .filter-tab.active {
        background: #ffffff;
        color: var(--primary);
        box-shadow: var(--shadow-sm);
    }

    /* Notes Grid */
    .notes-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 1.25rem;
    }
    .note-card {
        background: var(--card-bg);
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        padding: 1.25rem;
        box-shadow: var(--shadow-sm);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        transition: transform 0.15s, box-shadow 0.15s;
        position: relative;
        overflow: hidden;
    }
    .note-card:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }
    .note-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 5px;
        background-color: var(--note-accent, #e2e8f0);
    }
    .note-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 0.5rem;
        margin-bottom: 0.75rem;
    }
    .note-title {
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--text-main);
        word-break: break-word;
    }
    .note-title.completed {
        text-decoration: line-through;
        color: var(--text-muted);
    }
    .note-content {
        color: #475569;
        font-size: 0.925rem;
        white-space: pre-wrap;
        word-break: break-word;
        margin-bottom: 1.25rem;
        flex: 1;
    }
    .note-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        border-top: 1px solid #f1f5f9;
        padding-top: 0.75rem;
        margin-top: auto;
    }
    .note-date {
        font-size: 0.75rem;
        color: var(--text-muted);
    }
    .note-actions {
        display: flex;
        align-items: center;
        gap: 0.4rem;
    }
    .toggle-btn {
        background: none;
        border: none;
        cursor: pointer;
        padding: 0.3rem 0.5rem;
        font-size: 0.75rem;
        font-weight: 600;
        border-radius: 6px;
        transition: background 0.15s;
    }
    .toggle-btn.done {
        color: var(--success);
        background: #dcfce7;
    }
    .toggle-btn.undone {
        color: #d97706;
        background: #fef3c7;
    }
    .empty-state {
        text-align: center;
        padding: 4rem 1.5rem;
        background: var(--card-bg);
        border: 2px dashed var(--border);
        border-radius: var(--radius-lg);
        grid-column: 1 / -1;
    }
</style>
@endsection

@section('content')
    <!-- Quick Stats -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-label">Total Notes</div>
            <div class="stat-value">{{ $totalCount }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Active / Pending</div>
            <div class="stat-value" style="color: #4f46e5;">{{ $pendingCount }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Completed</div>
            <div class="stat-value" style="color: #16a34a;">{{ $completedCount }}</div>
        </div>
    </div>

    <!-- Search & Filter Controls -->
    <div class="controls-wrapper">
        <form action="{{ route('notes.index') }}" method="GET" class="search-form">
            @if(request('status'))
                <input type="hidden" name="status" value="{{ request('status') }}">
            @endif
            <input 
                type="text" 
                name="search" 
                class="search-input" 
                placeholder="Search notes by keyword..." 
                value="{{ request('search') }}"
            >
            <button type="submit" class="btn btn-outline">Search</button>
            @if(request('search'))
                <a href="{{ route('notes.index', ['status' => request('status')]) }}" class="btn btn-outline">Clear</a>
            @endif
        </form>

        <div class="filter-tabs">
            <a href="{{ route('notes.index', array_filter(['search' => request('search')])) }}" 
               class="filter-tab {{ !request('status') ? 'active' : '' }}">
               All
            </a>
            <a href="{{ route('notes.index', array_filter(['status' => 'pending', 'search' => request('search')])) }}" 
               class="filter-tab {{ request('status') == 'pending' ? 'active' : '' }}">
               Pending
            </a>
            <a href="{{ route('notes.index', array_filter(['status' => 'completed', 'search' => request('search')])) }}" 
               class="filter-tab {{ request('status') == 'completed' ? 'active' : '' }}">
               Completed
            </a>
        </div>
    </div>

    <!-- Notes Grid Listing -->
    <div class="notes-grid">
        @forelse($notes as $note)
            <div class="note-card" style="--note-accent: {{ $note->color ?? '#6366f1' }};">
                <div>
                    <div class="note-header">
                        <h3 class="note-title {{ $note->is_completed ? 'completed' : '' }}">
                            {{ $note->title }}
                        </h3>
                    </div>
                    @if($note->content)
                        <div class="note-content">{{ $note->content }}</div>
                    @else
                        <div class="note-content" style="color: #94a3b8; font-style: italic;">No additional notes.</div>
                    @endif
                </div>

                <div class="note-footer">
                    <span class="note-date">{{ $note->created_at->format('M d, Y') }}</span>
                    
                    <div class="note-actions">
                        <!-- Toggle Status Form -->
                        <form action="{{ route('notes.toggle', $note) }}" method="POST" style="display: inline;">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="toggle-btn {{ $note->is_completed ? 'done' : 'undone' }}">
                                {{ $note->is_completed ? '✓ Done' : '○ Mark Done' }}
                            </button>
                        </form>

                        <!-- Edit Button -->
                        <a href="{{ route('notes.edit', $note) }}" class="btn btn-outline" style="padding: 0.25rem 0.5rem; font-size: 0.75rem;">
                            Edit
                        </a>

                        <!-- Delete Form -->
                        <form action="{{ route('notes.destroy', $note) }}" method="POST" onsubmit="return confirm('Delete this note?');" style="display: inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-danger" title="Delete note">
                                ✕
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="empty-state">
                <div style="font-size: 2.5rem; margin-bottom: 0.75rem;">📝</div>
                <h3 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 0.4rem;">No notes found</h3>
                <p style="color: var(--text-muted); margin-bottom: 1.25rem;">
                    @if(request('search') || request('status'))
                        Try clearing your search filters.
                    @else
                        You haven't created any notes or tasks yet.
                    @endif
                </p>
                <a href="{{ route('notes.create') }}" class="btn btn-primary">+ Create First Note</a>
            </div>
        @endforelse
    </div>
@endsection
