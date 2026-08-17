// ==============================================================================
// 2026 DARK ELITE - SETTINGS MODAL COMPONENT (⚙️ PREFERENCES & FORMATS)
// Odds Format, Haptic Feedback, Push Notifications, Screen Awake, Language
// ==============================================================================

window.EliteSettingsModal = {
    props: {
        show: { type: Boolean, default: false }
    },
    emits: ['close'],
    template: `
        <transition name="fade">
            <div v-if="show" class="elite-modal-overlay" @click.self="$emit('close')">
                <div class="elite-sheet-container">
                    <div class="elite-sheet-header">
                        <div style="display:flex; align-items:center; gap:8px;">
                            <span style="font-size:18px;">⚙️</span>
                            <h3 style="margin:0; font-size:16px; font-weight:800;">Preferences & Settings</h3>
                        </div>
                        <button class="elite-header-btn elite-tap" @click="$emit('close')" style="width:34px; height:34px;">✕</button>
                    </div>

                    <div class="elite-sheet-body" style="padding-top:10px;">
                        <!-- Odds Format Switcher -->
                        <div class="settings-group">
                            <label class="settings-label">ODDS FORMAT</label>
                            <div class="odds-format-grid">
                                <div class="odds-format-card elite-tap" 
                                     :class="{ active: oddsFormat === 'decimal' }"
                                     @click="setOddsFormat('decimal')">
                                    <strong>Decimal</strong>
                                    <span>e.g. 1.75</span>
                                </div>
                                <div class="odds-format-card elite-tap" 
                                     :class="{ active: oddsFormat === 'fractional' }"
                                     @click="setOddsFormat('fractional')">
                                    <strong>Fractional</strong>
                                    <span>e.g. 3/4</span>
                                </div>
                                <div class="odds-format-card elite-tap" 
                                     :class="{ active: oddsFormat === 'american' }"
                                     @click="setOddsFormat('american')">
                                    <strong>American</strong>
                                    <span>e.g. -133</span>
                                </div>
                            </div>
                        </div>

                        <!-- Device & Preferences Switches -->
                        <div class="settings-group">
                            <label class="settings-label">APPLICATION PREFERENCES</label>
                            
                            <div class="settings-switch-row">
                                <div class="switch-meta">
                                    <strong>Haptic Touch Feedback</strong>
                                    <span>Tactile micro-vibrations on taps & clicks</span>
                                </div>
                                <label class="elite-toggle">
                                    <input type="checkbox" v-model="hapticEnabled" @change="toggleHaptic">
                                    <span class="toggle-slider"></span>
                                </label>
                            </div>

                            <div class="settings-switch-row">
                                <div class="switch-meta">
                                    <strong>Keep Screen Awake</strong>
                                    <span>Prevents screen from sleeping in match view</span>
                                </div>
                                <label class="elite-toggle">
                                    <input type="checkbox" v-model="screenAwake" @change="toggleAwake">
                                    <span class="toggle-slider"></span>
                                </label>
                            </div>

                            <div class="settings-switch-row">
                                <div class="switch-meta">
                                    <strong>Instant VIP Push Alerts</strong>
                                    <span>Receive instant notifications on banker release</span>
                                </div>
                                <label class="elite-toggle">
                                    <input type="checkbox" v-model="pushEnabled" @change="togglePush">
                                    <span class="toggle-slider"></span>
                                </label>
                            </div>
                        </div>

                        <!-- Language Switcher -->
                        <div class="settings-group">
                            <label class="settings-label">LANGUAGE (DİL)</label>
                            <div class="settings-lang-select-box">
                                <select class="settings-select" v-model="currentLang" @change="onLangChange">
                                    <option value="en">🇬🇧 English</option>
                                    <option value="tr">🇹🇷 Türkçe</option>
                                    <option value="de">🇩🇪 Deutsch</option>
                                    <option value="es">🇪🇸 Español</option>
                                    <option value="pt">🇵🇹 Português</option>
                                    <option value="fr">🇫🇷 Français</option>
                                    <option value="it">🇮🇹 Italiano</option>
                                    <option value="ru">🇷🇺 Русский</option>
                                </select>
                            </div>
                        </div>

                        <div style="text-align:center; padding:16px; color:var(--elite-text-muted); font-size:11px;">
                            ACMS 2026 Dark Elite Flagship VIP • Version 3.4.0 (Build 2026.08)
                        </div>
                    </div>
                </div>
            </div>
        </transition>
    `,
    setup() {
        const oddsFormat = Vue.ref(localStorage.getItem('app_odds_format') || 'decimal');
        const hapticEnabled = Vue.ref(localStorage.getItem('app_haptic_enabled') !== '0');
        const screenAwake = Vue.ref(localStorage.getItem('app_keep_awake') !== '0');
        const pushEnabled = Vue.ref(localStorage.getItem('app_push_enabled') !== '0');
        const currentLang = Vue.ref(localStorage.getItem('app_locale') || 'en');

        function setOddsFormat(fmt) {
            oddsFormat.value = fmt;
            localStorage.setItem('app_odds_format', fmt);
            if (window.Native && typeof window.Native.haptic === 'function') {
                window.Native.haptic('light');
            }
        }

        function toggleHaptic() {
            localStorage.setItem('app_haptic_enabled', hapticEnabled.value ? '1' : '0');
            if (hapticEnabled.value && window.Native && typeof window.Native.haptic === 'function') {
                window.Native.haptic('medium');
            }
        }

        function toggleAwake() {
            localStorage.setItem('app_keep_awake', screenAwake.value ? '1' : '0');
            if (window.Native && typeof window.Native.setKeepAwake === 'function') {
                window.Native.setKeepAwake(screenAwake.value);
            }
        }

        function togglePush() {
            localStorage.setItem('app_push_enabled', pushEnabled.value ? '1' : '0');
        }

        function onLangChange() {
            if (window.i18n && typeof window.i18n.setLanguage === 'function') {
                window.i18n.setLanguage(currentLang.value);
            }
        }

        return {
            oddsFormat,
            hapticEnabled,
            screenAwake,
            pushEnabled,
            currentLang,
            setOddsFormat,
            toggleHaptic,
            toggleAwake,
            togglePush,
            onLangChange
        };
    }
};
