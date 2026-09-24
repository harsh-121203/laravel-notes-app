<!-- ================= THEME STUDIO MODAL ================= -->
<div class="theme-modal-backdrop" id="themeModalBackdrop" onclick="if(event.target === this) closeThemeModal()">
    <div class="theme-modal-card">
        <div class="theme-modal-header">
            <h3 class="theme-modal-title">
                <span>🎨</span>
                <span>Theme Studio & Settings</span>
            </h3>
            <button type="button" class="btn-danger-icon" onclick="closeThemeModal()" style="font-size: 1.1rem; line-height: 1;" title="Close">✕</button>
        </div>

        <!-- Section 1: 5 Curated Theme Profiles -->
        <div class="theme-section-label">Preset Profiles</div>
        <div class="presets-grid" id="themePresetsContainer">
            <!-- 1. Default Paper -->
            <div class="preset-pill-btn" onclick="applyPresetTheme('default')" id="preset-btn-default">
                <div class="preset-swatches">
                    <span class="preset-swatch" style="background: #fafafa;"></span>
                    <span class="preset-swatch" style="background: #ffffff;"></span>
                    <span class="preset-swatch" style="background: #111827;"></span>
                </div>
                <span class="preset-name">Clean Paper</span>
            </div>

            <!-- 2. Emerald Forest -->
            <div class="preset-pill-btn" onclick="applyPresetTheme('emerald')" id="preset-btn-emerald">
                <div class="preset-swatches">
                    <span class="preset-swatch" style="background: #061a14;"></span>
                    <span class="preset-swatch" style="background: #0d281f;"></span>
                    <span class="preset-swatch" style="background: #10b981;"></span>
                </div>
                <span class="preset-name">Emerald</span>
            </div>

            <!-- 3. Obsidian Cyber -->
            <div class="preset-pill-btn" onclick="applyPresetTheme('obsidian')" id="preset-btn-obsidian">
                <div class="preset-swatches">
                    <span class="preset-swatch" style="background: #09090b;"></span>
                    <span class="preset-swatch" style="background: #141418;"></span>
                    <span class="preset-swatch" style="background: #6366f1;"></span>
                </div>
                <span class="preset-name">Obsidian</span>
            </div>

            <!-- 4. Vintage Parchment -->
            <div class="preset-pill-btn" onclick="applyPresetTheme('parchment')" id="preset-btn-parchment">
                <div class="preset-swatches">
                    <span class="preset-swatch" style="background: #f6f0e2;"></span>
                    <span class="preset-swatch" style="background: #fdfaf4;"></span>
                    <span class="preset-swatch" style="background: #b45309;"></span>
                </div>
                <span class="preset-name">Parchment</span>
            </div>

            <!-- 5. Nordic Dusk -->
            <div class="preset-pill-btn" onclick="applyPresetTheme('nordic')" id="preset-btn-nordic">
                <div class="preset-swatches">
                    <span class="preset-swatch" style="background: #f1f0f7;"></span>
                    <span class="preset-swatch" style="background: #ffffff;"></span>
                    <span class="preset-swatch" style="background: #4338ca;"></span>
                </div>
                <span class="preset-name">Nordic</span>
            </div>
        </div>

        <!-- Section 2: Creative Custom Color Controls -->
        <div class="theme-section-label">Custom Palette Controls</div>
        <div class="color-controls-grid">
            <div class="color-picker-row">
                <span class="color-picker-label">Page Canvas</span>
                <div class="color-picker-input-wrap">
                    <span class="color-hex-text" id="hex-bg-canvas">#fafafa</span>
                    <input type="color" class="color-picker-input" id="picker-bg-canvas" oninput="updateCustomColor('--bg-canvas', this.value, 'hex-bg-canvas')">
                </div>
            </div>

            <div class="color-picker-row">
                <span class="color-picker-label">Cards & Surface</span>
                <div class="color-picker-input-wrap">
                    <span class="color-hex-text" id="hex-bg-surface">#ffffff</span>
                    <input type="color" class="color-picker-input" id="picker-bg-surface" oninput="updateCustomColor('--bg-surface', this.value, 'hex-bg-surface')">
                </div>
            </div>

            <div class="color-picker-row">
                <span class="color-picker-label">Primary Text</span>
                <div class="color-picker-input-wrap">
                    <span class="color-hex-text" id="hex-text-title">#111827</span>
                    <input type="color" class="color-picker-input" id="picker-text-title" oninput="updateCustomColor('--text-title', this.value, 'hex-text-title')">
                </div>
            </div>

            <div class="color-picker-row">
                <span class="color-picker-label">Body Text</span>
                <div class="color-picker-input-wrap">
                    <span class="color-hex-text" id="hex-text-body">#374151</span>
                    <input type="color" class="color-picker-input" id="picker-text-body" oninput="updateCustomColor('--text-body', this.value, 'hex-text-body')">
                </div>
            </div>

            <div class="color-picker-row">
                <span class="color-picker-label">Borders & Lines</span>
                <div class="color-picker-input-wrap">
                    <span class="color-hex-text" id="hex-border-line">#e5e7eb</span>
                    <input type="color" class="color-picker-input" id="picker-border-line" oninput="updateCustomColor('--border-line', this.value, 'hex-border-line')">
                </div>
            </div>

            <div class="color-picker-row">
                <span class="color-picker-label">Accent Color</span>
                <div class="color-picker-input-wrap">
                    <span class="color-hex-text" id="hex-accent">#111827</span>
                    <input type="color" class="color-picker-input" id="picker-accent" oninput="updateCustomColor('--accent', this.value, 'hex-accent')">
                </div>
            </div>
        </div>

        <div class="theme-modal-footer">
            <button type="button" class="btn-press" onclick="resetThemeToDefault()">
                ↺ Reset Default
            </button>
            <button type="button" class="btn-press btn-primary-press" onclick="closeThemeModal()">
                Done
            </button>
        </div>
    </div>
</div>
