@extends('layouts.app')

@section('title', 'Edit Note - Notes App')

@section('styles')
<style>
    .form-card {
        background: var(--card-bg);
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        padding: 2rem;
        max-width: 600px;
        margin: 0 auto;
        box-shadow: var(--shadow-md);
    }
    .form-group {
        margin-bottom: 1.5rem;
    }
    .form-label {
        display: block;
        font-weight: 600;
        font-size: 0.875rem;
        margin-bottom: 0.4rem;
        color: var(--text-main);
    }
    .form-control {
        width: 100%;
        padding: 0.65rem 0.85rem;
        border: 1px solid var(--border);
        border-radius: var(--radius-md);
        font-size: 0.95rem;
        font-family: inherit;
        outline: none;
        transition: border 0.15s;
    }
    .form-control:focus {
        border-color: var(--primary);
    }
    .color-picker-group {
        display: flex;
        gap: 0.75rem;
        align-items: center;
        margin-top: 0.5rem;
    }
    .color-option {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        cursor: pointer;
        border: 2px solid transparent;
        display: inline-block;
        transition: transform 0.15s;
    }
    .color-option:hover {
        transform: scale(1.1);
    }
    .color-option.selected {
        border-color: #0f172a;
    }
    .error-text {
        color: var(--danger);
        font-size: 0.8rem;
        margin-top: 0.35rem;
    }
    .form-actions {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 0.75rem;
        margin-top: 2rem;
    }
</style>
@endsection

@section('content')
<div class="form-card">
    <h2 style="font-size: 1.4rem; font-weight: 700; margin-bottom: 0.5rem;">Edit Note</h2>
    <p style="color: var(--text-muted); font-size: 0.875rem; margin-bottom: 1.75rem;">Modify the details of your note.</p>

    <form action="{{ route('notes.update', $note) }}" method="POST">
        @csrf
        @method('PUT')

        <!-- Title -->
        <div class="form-group">
            <label class="form-label" for="title">Title <span style="color: var(--danger);">*</span></label>
            <input 
                type="text" 
                id="title" 
                name="title" 
                class="form-control" 
                value="{{ old('title', $note->title) }}" 
                required
            >
            @error('title')
                <div class="error-text">{{ $message }}</div>
            @enderror
        </div>

        <!-- Content -->
        <div class="form-group">
            <label class="form-label" for="content">Description / Details (Optional)</label>
            <textarea 
                id="content" 
                name="content" 
                class="form-control" 
                rows="4"
            >{{ old('content', $note->content) }}</textarea>
            @error('content')
                <div class="error-text">{{ $message }}</div>
            @enderror
        </div>

        <!-- Color Tag Selection -->
        <div class="form-group">
            <label class="form-label">Note Accent Color</label>
            <div class="color-picker-group">
                @php
                    $colors = [
                        '#6366f1' => 'Indigo',
                        '#3b82f6' => 'Blue',
                        '#10b981' => 'Emerald',
                        '#f59e0b' => 'Amber',
                        '#ec4899' => 'Pink',
                        '#8b5cf6' => 'Purple'
                    ];
                    $currentColor = old('color', $note->color ?? '#6366f1');
                @endphp
                @foreach($colors as $hex => $label)
                    <label style="cursor: pointer;">
                        <input type="radio" name="color" value="{{ $hex }}" {{ $currentColor === $hex ? 'checked' : '' }} style="display: none;" onchange="updateColorSelected(this)">
                        <span class="color-option {{ $currentColor === $hex ? 'selected' : '' }}" style="background: {{ $hex }};" title="{{ $label }}"></span>
                    </label>
                @endforeach
            </div>
        </div>

        <!-- Submit & Cancel Buttons -->
        <div class="form-actions">
            <a href="{{ route('notes.index') }}" class="btn btn-outline">Cancel</a>
            <button type="submit" class="btn btn-primary">Update Note</button>
        </div>
    </form>
</div>

<script>
    function updateColorSelected(radio) {
        document.querySelectorAll('.color-option').forEach(el => el.classList.remove('selected'));
        radio.nextElementSibling.classList.add('selected');
    }
</script>
@endsection
