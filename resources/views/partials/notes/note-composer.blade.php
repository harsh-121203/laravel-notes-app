<!-- Clean Notebook Sheet Composer -->
<form action="{{ route('notes.store') }}" method="POST" class="note-composer-card" id="noteComposerCard">
    @csrf
    <div style="position: relative;">
        <input 
            type="text" 
            name="title" 
            id="noteTitleInput"
            class="note-input-title" 
            placeholder="Note title (e.g. CSRF Token Concept)" 
            required 
            autocomplete="off"
        >
    </div>

    <div class="note-composer-drawer" id="noteComposerDrawer">
        <div class="note-composer-drawer-inner">
            <textarea 
                name="content" 
                id="noteBodyInput"
                class="note-input-body" 
                placeholder="Write quick points or summary..."
            ></textarea>

            <input type="hidden" name="task_id" id="noteTaskId" value="">
            <div id="noteTaskIndicator" style="display: none; font-size: 0.725rem; font-weight: 600; color: var(--accent); margin-bottom: 0.65rem; background: var(--bg-canvas); padding: 0.2rem 0.5rem; border-radius: var(--radius-xs); align-self: flex-start; display: none;">
                📎 Attaching to Task
            </div>

            <div class="composer-bottom">
                <div class="palette-list">
                    @php
                        $palettes = [
                            '#ffffff' => 'Clean White',
                            '#fde68a' => 'Warm Cream',
                            '#a7f3d0' => 'Pale Mint',
                            '#ddd6fe' => 'Soft Lilac',
                            '#fecdd3' => 'Pale Rose'
                        ];
                    @endphp
                    @foreach($palettes as $hex => $label)
                        <label style="cursor: pointer; line-height: 0;">
                            <input type="radio" name="color" value="{{ $hex }}" {{ $loop->first ? 'checked' : '' }} style="display: none;" onchange="selectPalette(this)">
                            <span class="palette-circle {{ $loop->first ? 'active' : '' }}" style="background: {{ $hex }};" title="{{ $label }}"></span>
                        </label>
                    @endforeach
                </div>

                <div style="display: flex; gap: 0.4rem;">
                    <button type="button" class="btn-press" onclick="collapseNoteComposer()" style="padding: 0.35rem 0.65rem; font-size: 0.775rem;">
                        Cancel
                    </button>
                    <button type="submit" class="btn-press btn-primary-press">
                        Save Note
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>
