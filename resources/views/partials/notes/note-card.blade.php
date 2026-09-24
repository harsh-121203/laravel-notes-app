@php
    $hasCustomColor = !empty($note->color) && $note->color !== '#ffffff';
@endphp
<div class="sheet-card" id="note-{{ $note->id }}" @if($hasCustomColor) data-has-custom-color="true" style="--sheet-bg: {{ $note->color }};" @endif>
    <div class="sheet-header">
        <div class="sheet-title">{{ $note->title }}</div>
        <form action="{{ route('notes.destroy', $note) }}" method="POST">
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
