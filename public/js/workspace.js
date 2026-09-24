/**
 * Workspace SPA Engine & Dynamic Theme Studio Controller
 */

// ================= THEME STUDIO LOGIC =================
const THEME_PRESETS = {
    default: {
        name: 'Clean Paper',
        vars: {
            '--bg-canvas': '#fafafa',
            '--bg-surface': '#ffffff',
            '--border-line': '#e5e7eb',
            '--text-title': '#111827',
            '--text-body': '#374151',
            '--text-muted': '#6b7280',
            '--accent': '#111827'
        }
    },
    emerald: {
        name: 'Emerald Forest',
        vars: {
            '--bg-canvas': '#061a14',
            '--bg-surface': '#0d281f',
            '--border-line': '#1c4d3d',
            '--text-title': '#f0fdf4',
            '--text-body': '#bbf7d0',
            '--text-muted': '#86efac',
            '--accent': '#10b981'
        }
    },
    obsidian: {
        name: 'Obsidian Cyber',
        vars: {
            '--bg-canvas': '#09090b',
            '--bg-surface': '#141418',
            '--border-line': '#272732',
            '--text-title': '#fafafa',
            '--text-body': '#e4e4e7',
            '--text-muted': '#a1a1aa',
            '--accent': '#6366f1'
        }
    },
    parchment: {
        name: 'Vintage Parchment',
        vars: {
            '--bg-canvas': '#f6f0e2',
            '--bg-surface': '#fdfaf4',
            '--border-line': '#ded0b6',
            '--text-title': '#2c1e11',
            '--text-body': '#4a3824',
            '--text-muted': '#7a6245',
            '--accent': '#b45309'
        }
    },
    nordic: {
        name: 'Nordic Dusk',
        vars: {
            '--bg-canvas': '#f1f0f7',
            '--bg-surface': '#ffffff',
            '--border-line': '#d8d4ec',
            '--text-title': '#1e1b4b',
            '--text-body': '#312e81',
            '--text-muted': '#4f46e5',
            '--accent': '#4338ca'
        }
    }
};

function isColorDark(hexColor) {
    if (!hexColor || typeof hexColor !== 'string') return false;
    let hex = hexColor.trim().replace('#', '');
    if (hex.length === 3) {
        hex = hex.split('').map(c => c + c).join('');
    }
    if (hex.length !== 6) return false;
    const r = parseInt(hex.substr(0, 2), 16);
    const g = parseInt(hex.substr(2, 2), 16);
    const b = parseInt(hex.substr(4, 2), 16);
    if (isNaN(r) || isNaN(g) || isNaN(b)) return false;
    const lum = (0.299 * r + 0.587 * g + 0.114 * b) / 255;
    return lum < 0.5;
}

function deriveThemeSupportVariables(vars) {
    const root = document.documentElement;
    const canvas = vars['--bg-canvas'] || '#fafafa';
    const surface = vars['--bg-surface'] || '#ffffff';
    const accent = vars['--accent'] || '#111827';

    const isDarkTheme = isColorDark(canvas) || isColorDark(surface);
    const isAccentDark = isColorDark(accent);

    const supportVars = {
        '--color-scheme': isDarkTheme ? 'dark' : 'light',
        '--calendar-icon-filter': isDarkTheme ? 'invert(1)' : 'none',
        '--border-subtle': isDarkTheme ? 'rgba(255, 255, 255, 0.08)' : 'rgba(0, 0, 0, 0.06)',
        '--btn-hover-bg': isDarkTheme ? 'rgba(255, 255, 255, 0.08)' : 'rgba(0, 0, 0, 0.04)',
        '--item-hover-bg': isDarkTheme ? 'rgba(255, 255, 255, 0.04)' : 'rgba(0, 0, 0, 0.02)',
        '--badge-bg': isDarkTheme ? 'rgba(255, 255, 255, 0.08)' : 'rgba(0, 0, 0, 0.05)',
        '--accent-contrast': isAccentDark ? '#ffffff' : '#111827'
    };

    for (const [key, val] of Object.entries(supportVars)) {
        root.style.setProperty(key, val);
    }
    root.style.colorScheme = isDarkTheme ? 'dark' : 'light';
}

function openThemeModal() {
    const modal = document.getElementById('themeModalBackdrop');
    if (modal) {
        modal.classList.add('is-open');
        syncThemeInputsFromDOM();
    }
}

function closeThemeModal() {
    const modal = document.getElementById('themeModalBackdrop');
    if (modal) {
        modal.classList.remove('is-open');
    }
}

function applyThemeVariables(vars, presetKey = null) {
    const root = document.documentElement;
    for (const [key, val] of Object.entries(vars)) {
        root.style.setProperty(key, val);
    }
    deriveThemeSupportVariables(vars);

    const themeData = {
        preset: presetKey,
        vars: vars
    };
    localStorage.setItem('workspace_custom_theme', JSON.stringify(themeData));

    // Update active preset button highlight
    document.querySelectorAll('.preset-pill-btn').forEach(btn => btn.classList.remove('active'));
    if (presetKey && presetKey !== 'custom') {
        const activeBtn = document.getElementById('preset-btn-' + presetKey);
        if (activeBtn) activeBtn.classList.add('active');
    }
    syncThemeInputsFromDOM();
}

function applyPresetTheme(presetKey) {
    if (THEME_PRESETS[presetKey]) {
        applyThemeVariables(THEME_PRESETS[presetKey].vars, presetKey);
        showToast('Switched to ' + THEME_PRESETS[presetKey].name + ' theme');
    }
}

function updateCustomColor(varName, colorHex, hexTextId) {
    document.documentElement.style.setProperty(varName, colorHex);
    const hexEl = document.getElementById(hexTextId);
    if (hexEl) hexEl.textContent = colorHex;

    const computed = getComputedStyle(document.documentElement);
    const currentVars = {
        '--bg-canvas': computed.getPropertyValue('--bg-canvas').trim(),
        '--bg-surface': computed.getPropertyValue('--bg-surface').trim(),
        '--border-line': computed.getPropertyValue('--border-line').trim(),
        '--text-title': computed.getPropertyValue('--text-title').trim(),
        '--text-body': computed.getPropertyValue('--text-body').trim(),
        '--text-muted': computed.getPropertyValue('--text-muted').trim(),
        '--accent': computed.getPropertyValue('--accent').trim(),
    };
    currentVars[varName] = colorHex;
    deriveThemeSupportVariables(currentVars);

    document.querySelectorAll('.preset-pill-btn').forEach(btn => btn.classList.remove('active'));
    localStorage.setItem('workspace_custom_theme', JSON.stringify({
        preset: 'custom',
        vars: currentVars
    }));
}

function resetThemeToDefault() {
    applyPresetTheme('default');
}

function syncThemeInputsFromDOM() {
    const computed = getComputedStyle(document.documentElement);
    const map = [
        { var: '--bg-canvas', input: 'picker-bg-canvas', hex: 'hex-bg-canvas' },
        { var: '--bg-surface', input: 'picker-bg-surface', hex: 'hex-bg-surface' },
        { var: '--text-title', input: 'picker-text-title', hex: 'hex-text-title' },
        { var: '--text-body', input: 'picker-text-body', hex: 'hex-text-body' },
        { var: '--border-line', input: 'picker-border-line', hex: 'hex-border-line' },
        { var: '--accent', input: 'picker-accent', hex: 'hex-accent' },
    ];

    map.forEach(item => {
        let val = computed.getPropertyValue(item.var).trim();
        if (val) {
            if (val.startsWith('#') && (val.length === 7 || val.length === 4)) {
                const inputEl = document.getElementById(item.input);
                const hexEl = document.getElementById(item.hex);
                if (inputEl) inputEl.value = val;
                if (hexEl) hexEl.textContent = val;
            }
        }
    });
}

function loadSavedTheme() {
    try {
        const saved = localStorage.getItem('workspace_custom_theme');
        if (saved) {
            const parsed = JSON.parse(saved);
            if (parsed && parsed.vars) {
                applyThemeVariables(parsed.vars, parsed.preset);
            }
        } else {
            deriveThemeSupportVariables(THEME_PRESETS.default.vars);
        }
    } catch (e) {
        console.error('Theme load error:', e);
    }
}

// ================= NOTE COLOR PALETTE =================
function selectPalette(radio) {
    document.querySelectorAll('.palette-circle').forEach(el => el.classList.remove('active'));
    radio.nextElementSibling.classList.add('active');
}

function scrollToNote(noteId) {
    const noteEl = document.getElementById('note-' + noteId);
    if (noteEl) {
        noteEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
        noteEl.classList.remove('note-highlight');
        void noteEl.offsetWidth; // Trigger reflow
        noteEl.classList.add('note-highlight');
    }
}

// Floating Toast Notification System
function showToast(message, type = 'success') {
    let slot = document.querySelector('.toast-slot');
    if (!slot) {
        slot = document.createElement('div');
        slot.className = 'toast-slot';
        document.body.appendChild(slot);
    }
    const toast = document.createElement('div');
    toast.className = 'toast-msg';
    if (type === 'error') toast.style.background = '#dc2626';
    toast.innerHTML = `<span>${message}</span><span style="cursor:pointer;opacity:0.7;margin-left:auto;" onclick="this.parentElement.remove();">&times;</span>`;
    slot.appendChild(toast);
    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transform = 'translateY(10px)';
        toast.style.transition = 'all 0.25s ease';
        setTimeout(() => toast.remove(), 250);
    }, 2800);
}

// Fast, Smooth SPA Navigation (No Full Page Reloads)
let isFetching = false;
function navigateWorkspace(url, pushState = true) {
    if (isFetching) return;
    isFetching = true;

    const root = document.getElementById('workspaceRoot');

    fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(res => {
            if (!res.ok) throw new Error('Network error');
            return res.text();
        })
        .then(html => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const newRoot = doc.getElementById('workspaceRoot');
            if (newRoot && root) {
                root.innerHTML = newRoot.innerHTML;
                if (pushState) history.pushState(null, '', url);
                bindInteractiveEvents();
            }
        })
        .catch(err => {
            window.location.href = url; // Fallback
        })
        .finally(() => {
            isFetching = false;
        });
}

// Support browser Back / Forward buttons without reload
window.addEventListener('popstate', () => {
    navigateWorkspace(window.location.href, false);
});

// Bind all dynamic interactive events
function bindInteractiveEvents() {
    const composerCard = document.getElementById('taskComposerCard');
    const taskTitleInput = document.getElementById('taskTitleInput');
    const taskNoteInput = document.getElementById('taskNoteInput');

    window.expandTaskComposer = function() {
        if (composerCard) composerCard.classList.add('is-expanded');
    };

    window.collapseTaskComposer = function() {
        if (composerCard) {
            if (taskTitleInput) taskTitleInput.value = '';
            if (taskNoteInput) taskNoteInput.value = '';
            composerCard.classList.remove('is-expanded');
        }
    };

    if (taskTitleInput) {
        taskTitleInput.addEventListener('focus', window.expandTaskComposer);
        taskTitleInput.addEventListener('input', function() {
            if (this.value.trim().length > 0) window.expandTaskComposer();
        });
    }

    const noteComposerCard = document.getElementById('noteComposerCard');
    const noteTitleInput = document.getElementById('noteTitleInput');
    const noteBodyInput = document.getElementById('noteBodyInput');

    window.expandNoteComposer = function() {
        if (noteComposerCard) noteComposerCard.classList.add('is-expanded');
    };

    window.collapseNoteComposer = function() {
        if (noteComposerCard) {
            if (noteTitleInput) noteTitleInput.value = '';
            if (noteBodyInput) noteBodyInput.value = '';
            const noteTaskId = document.getElementById('noteTaskId');
            if (noteTaskId) noteTaskId.value = '';
            const noteTaskIndicator = document.getElementById('noteTaskIndicator');
            if (noteTaskIndicator) noteTaskIndicator.style.display = 'none';
            noteComposerCard.classList.remove('is-expanded');
        }
    };

    if (noteTitleInput) {
        noteTitleInput.addEventListener('focus', window.expandNoteComposer);
        noteTitleInput.addEventListener('input', function() {
            if (this.value.trim().length > 0) window.expandNoteComposer();
        });
    }

    window.selectPalette = function(radio) {
        // Remove active class from all circles in this list
        const list = radio.closest('.palette-list');
        if (list) {
            list.querySelectorAll('.palette-circle').forEach(c => c.classList.remove('active'));
        }
        // Add active class to the selected circle
        const circle = radio.nextElementSibling;
        if (circle) circle.classList.add('active');

        // Apply background to the note composer card
        const card = radio.closest('.note-composer-card');
        if (card) {
            card.style.backgroundColor = radio.value;
        }
    };

    window.createNoteForTask = function(taskId) {
        if (window.expandNoteComposer) window.expandNoteComposer();
        const noteTaskId = document.getElementById('noteTaskId');
        if (noteTaskId) noteTaskId.value = taskId;
        
        const noteTaskIndicator = document.getElementById('noteTaskIndicator');
        if (noteTaskIndicator) noteTaskIndicator.style.display = 'inline-block';
        
        const noteTitleInput = document.getElementById('noteTitleInput');
        if (noteTitleInput) {
            noteTitleInput.focus();
            noteTitleInput.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    };

    // Intercept all calendar navigation links and filter pills
    document.querySelectorAll('.cal-day-cell, .calendar-quick-pills a, .calendar-nav-group a.btn-press, .column-header a.btn-press').forEach(el => {
        el.addEventListener('click', function(e) {
            e.preventDefault();
            navigateWorkspace(this.href);
        });
    });

    // Intercept Task Composer submission via AJAX
    if (composerCard) {
        composerCard.onsubmit = function(e) {
            e.preventDefault();
            const submitBtn = this.querySelector('button[type="submit"]');
            if (submitBtn) submitBtn.disabled = true;

            fetch(this.action, {
                method: 'POST',
                body: new FormData(this),
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showToast(data.message);
                    navigateWorkspace(window.location.href, false);
                } else if (data.errors) {
                    showToast(Object.values(data.errors)[0][0], 'error');
                }
            })
            .catch(err => {
                showToast('Could not save task.', 'error');
            })
            .finally(() => {
                if (submitBtn) submitBtn.disabled = false;
            });
        };
    }

    // Intercept Subtask Form submissions via AJAX
    document.querySelectorAll('.add-subtask-form').forEach(form => {
        form.onsubmit = function(e) {
            e.preventDefault();
            const submitBtn = this.querySelector('button[type="submit"]');
            if (submitBtn) submitBtn.disabled = true;

            fetch(this.action, {
                method: 'POST',
                body: new FormData(this),
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showToast(data.message);
                    navigateWorkspace(window.location.href, false);
                }
            })
            .catch(err => {
                showToast('Could not add subtask.', 'error');
            })
            .finally(() => {
                if (submitBtn) submitBtn.disabled = false;
            });
        };
    });

    // Intercept Checkbox toggle forms with instant optimistic update
    document.querySelectorAll('.task-main-row form[action*="/toggle"], .subtask-leaf form[action*="/toggle"]').forEach(form => {
        const checkbox = form.querySelector('.round-checkbox');
        if (checkbox) {
            checkbox.onchange = function(e) {
                e.preventDefault();
                // Optimistic UI toggle
                const titleText = form.closest('.task-main-row')?.querySelector('.task-headline') || form.closest('.subtask-leaf')?.querySelector('.subtask-leaf-title');
                if (titleText) titleText.classList.toggle('is-done');

                fetch(form.action, {
                    method: 'POST',
                    body: new FormData(form),
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                })
                .then(res => res.json())
                .then(data => {
                    showToast(data.message);
                    navigateWorkspace(window.location.href, false);
                })
                .catch(err => {
                    checkbox.checked = !checkbox.checked;
                    if (titleText) titleText.classList.toggle('is-done');
                    showToast('Toggle failed.', 'error');
                });
            };
        }
    });

    // Intercept Delete forms for Tasks and Subtasks
    document.querySelectorAll('.task-tail-actions form[action*="/tasks/"], .subtask-leaf form[action*="/tasks/"]').forEach(form => {
        form.onsubmit = function(e) {
            e.preventDefault();
            if (!confirm('Delete this task?')) return false;

            const row = form.closest('.task-entry') || form.closest('.subtask-leaf');
            if (row) {
                row.style.opacity = '0.3';
                row.style.pointerEvents = 'none';
            }

            fetch(form.action, {
                method: 'POST',
                body: new FormData(form),
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            })
            .then(res => res.json())
            .then(data => {
                showToast(data.message);
                navigateWorkspace(window.location.href, false);
            })
            .catch(err => {
                if (row) {
                    row.style.opacity = '1';
                    row.style.pointerEvents = 'auto';
                }
                showToast('Delete failed.', 'error');
            });
        };
    });

    // Intercept Note Creator Form via AJAX
    const noteComposer = document.querySelector('.note-composer-card');
    if (noteComposer) {
        noteComposer.onsubmit = function(e) {
            e.preventDefault();
            const submitBtn = this.querySelector('button[type="submit"]');
            if (submitBtn) submitBtn.disabled = true;

            fetch(this.action, {
                method: 'POST',
                body: new FormData(this),
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            })
            .then(res => res.json())
            .then(data => {
                showToast(data.message);
                navigateWorkspace(window.location.href, false);
            })
            .catch(err => {
                showToast('Could not save note.', 'error');
            })
            .finally(() => {
                if (submitBtn) submitBtn.disabled = false;
            });
        };
    }

    // Intercept Note Delete forms
    document.querySelectorAll('.sheet-header form[action*="/notes/"]').forEach(form => {
        form.onsubmit = function(e) {
            e.preventDefault();
            if (!confirm('Delete this note?')) return false;

            fetch(form.action, {
                method: 'POST',
                body: new FormData(form),
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            })
            .then(res => res.json())
            .then(data => {
                showToast(data.message);
                navigateWorkspace(window.location.href, false);
            })
            .catch(err => {
                showToast('Delete note failed.', 'error');
            });
        };
    });
}

// Collapse composer if clicked outside
document.addEventListener('click', function(e) {
    const composerCard = document.getElementById('taskComposerCard');
    const taskTitleInput = document.getElementById('taskTitleInput');
    const taskNoteInput = document.getElementById('taskNoteInput');
    if (composerCard && !composerCard.contains(e.target)) {
        if (taskTitleInput && taskNoteInput && !taskTitleInput.value.trim() && !taskNoteInput.value.trim()) {
            composerCard.classList.remove('is-expanded');
        }
    }

    const noteComposerCard = document.getElementById('noteComposerCard');
    const noteTitleInput = document.getElementById('noteTitleInput');
    const noteBodyInput = document.getElementById('noteBodyInput');
    if (noteComposerCard && !noteComposerCard.contains(e.target)) {
        if (noteTitleInput && noteBodyInput && !noteTitleInput.value.trim() && !noteBodyInput.value.trim()) {
            noteComposerCard.classList.remove('is-expanded');
        }
    }
});

// Load saved theme immediately
loadSavedTheme();

// Immediate or DOMContentLoaded execution
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', bindInteractiveEvents);
} else {
    bindInteractiveEvents();
}
