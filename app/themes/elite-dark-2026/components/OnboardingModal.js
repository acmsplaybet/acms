// ==============================================================================
// 2026 DARK ELITE - ONBOARDING MODAL COMPONENT (5 STEPS)
// Multi-Language, Live Match Simulation, Tipsters, Push Pitch, Social Proof
// ==============================================================================

window.EliteOnboardingModal = {
    props: {
        show: { type: Boolean, default: false }
    },
    emits: ['close', 'completed'],
    template: `
        <transition name="fade">
            <div v-if="show" class="elite-onboard-overlay">
                <div class="elite-onboard-container">
                    <!-- Top Bar: Step Indicator & Skip Button -->
                    <div class="elite-onboard-header">
                        <div class="elite-onboard-dots">
                            <span v-for="step in 5" :key="step" 
                                  class="elite-dot" 
                                  :class="{ active: currentStep === step, completed: currentStep > step }">
                            </span>
                        </div>
                        <button class="elite-skip-btn" @click="finishOnboarding">
                            {{ $t ? $t('common.skip') : 'Skip' }}
                        </button>
                    </div>

                    <!-- Step 1: Language Selector -->
                    <div v-if="currentStep === 1" class="elite-onboard-slide">
                        <div class="elite-slide-icon">🌍</div>
                        <h2 class="elite-slide-title">Select Your Language</h2>
                        <p class="elite-slide-desc">Choose your preferred language for instant localized betting tips & odds.</p>
                        
                        <div class="elite-lang-grid">
                            <div v-for="lang in availableLanguages" :key="lang.code"
                                 class="elite-lang-card elite-tap"
                                 :class="{ selected: selectedLang === lang.code }"
                                 @click="changeLang(lang.code)">
                                <span class="lang-flag">{{ lang.flag }}</span>
                                <span class="lang-name">{{ lang.name }}</span>
                                <span v-if="selectedLang === lang.code" class="lang-check">✓</span>
                            </div>
                        </div>
                    </div>

                    <!-- Step 2: Live Match & Win Proof Simulation -->
                    <div v-if="currentStep === 2" class="elite-onboard-slide">
                        <div class="elite-slide-badge">⚡ LIVE SIMULATION</div>
                        <h2 class="elite-slide-title">Real-Time Match Intel</h2>
                        <p class="elite-slide-desc">Get ultra-accurate live match tips calculated by AI & Pro Tipsters.</p>

                        <div class="elite-demo-match-card">
                            <div class="demo-league">🇮🇹 Serie A • Live 65'</div>
                            <div class="demo-teams-row">
                                <div class="demo-team">
                                    <div class="demo-team-badge">🔵</div>
                                    <span>Inter Milan</span>
                                </div>
                                <div class="demo-score">2 - 1</div>
                                <div class="demo-team">
                                    <div class="demo-team-badge">⚫</div>
                                    <span>Atalanta</span>
                                </div>
                            </div>
                            <div class="demo-prediction-pill">
                                <span class="pill-label">PREDICTION:</span>
                                <span class="pill-pick">Home Win (1) @ 1.85</span>
                                <span class="pill-status win">WON 🟢</span>
                            </div>
                        </div>
                    </div>

                    <!-- Step 3: Push Notification Pitch -->
                    <div v-if="currentStep === 3" class="elite-onboard-slide">
                        <div class="elite-slide-icon">🔔</div>
                        <h2 class="elite-slide-title">Never Miss a High-Value Tip</h2>
                        <p class="elite-slide-desc">Get immediate notifications when high-confidence banker tips drop before odds drop.</p>

                        <div class="elite-demo-notification">
                            <div class="notif-header">
                                <div class="notif-app">
                                    <span class="app-icon">👑</span>
                                    <span>VIP Banker Alert</span>
                                </div>
                                <span class="notif-time">Just now</span>
                            </div>
                            <div class="notif-body">
                                <strong>🔥 Lanus vs Independiente</strong>
                                <p>Home Win @ 1.72 (Confidence: 86%) has been confirmed by Safe Steve.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Step 4: Pro Tipsters -->
                    <div v-if="currentStep === 4" class="elite-onboard-slide">
                        <div class="elite-slide-icon">👥</div>
                        <h2 class="elite-slide-title">Follow Verified Tipsters</h2>
                        <p class="elite-slide-desc">Each tipster specializes in distinct strategies tailored to your style.</p>

                        <div class="elite-tipsters-list">
                            <div class="demo-tipster-row">
                                <div class="tipster-avatar" style="background: linear-gradient(135deg, #00E676, #00B359);">🛡️</div>
                                <div class="tipster-meta">
                                    <strong>Safe Steve</strong>
                                    <span>Banker & Low Risk • 88.4% Win Rate</span>
                                </div>
                                <div class="tipster-badge green">TOP 1</div>
                            </div>
                            <div class="demo-tipster-row">
                                <div class="tipster-avatar" style="background: linear-gradient(135deg, #00E5FF, #2979FF);">⚖️</div>
                                <div class="tipster-meta">
                                    <strong>Balanced Ben</strong>
                                    <span>Goals & Double Chance • 79.2% Win Rate</span>
                                </div>
                                <div class="tipster-badge blue">TOP 2</div>
                            </div>
                            <div class="demo-tipster-row">
                                <div class="tipster-avatar" style="background: linear-gradient(135deg, #7C4DFF, #FF3D71);">🚀</div>
                                <div class="tipster-meta">
                                    <strong>Risky Rick</strong>
                                    <span>High Multipliers • 4.20x Avg Odd</span>
                                </div>
                                <div class="tipster-badge purple">HIGH ROI</div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 5: Social Proof & Start Action -->
                    <div v-if="currentStep === 5" class="elite-onboard-slide">
                        <div class="elite-slide-stars">⭐⭐⭐⭐⭐</div>
                        <h2 class="elite-slide-title">Join Over 1,000,000+ Winners</h2>
                        <p class="elite-slide-desc">You are now ready to access the flagship 2026 Dark Elite VIP betting suite.</p>

                        <div class="elite-social-proof-box">
                            <div class="proof-stats-grid">
                                <div class="stat-col">
                                    <strong>4.8 / 5.0</strong>
                                    <span>App Rating</span>
                                </div>
                                <div class="stat-col">
                                    <strong>84.2%</strong>
                                    <span>VIP Win Rate</span>
                                </div>
                                <div class="stat-col">
                                    <strong>100% Free</strong>
                                    <span>VIP Unlocked</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Bottom Action Button -->
                    <div class="elite-onboard-footer">
                        <button class="elite-action-btn elite-tap" @click="nextStep">
                            <span>{{ currentStep === 5 ? ($t ? $t('onboarding.get_started') : 'Get Started Now') : ($t ? $t('common.next') : 'Next') }}</span>
                            <span class="btn-arrow">→</span>
                        </button>
                    </div>
                </div>
            </div>
        </transition>
    `,
    setup(props, { emit }) {
        const currentStep = Vue.ref(1);
        const selectedLang = Vue.ref(localStorage.getItem('app_locale') || 'en');

        const availableLanguages = [
            { code: 'en', name: 'English', flag: '🇬🇧' },
            { code: 'tr', name: 'Türkçe', flag: '🇹🇷' },
            { code: 'de', name: 'Deutsch', flag: '🇩🇪' },
            { code: 'es', name: 'Español', flag: '🇪🇸' },
            { code: 'pt', name: 'Português', flag: '🇵🇹' },
            { code: 'fr', name: 'Français', flag: '🇫🇷' },
            { code: 'it', name: 'Italiano', flag: '🇮🇹' },
            { code: 'ru', name: 'Русский', flag: '🇷🇺' }
        ];

        function changeLang(code) {
            selectedLang.value = code;
            if (window.i18n && typeof window.i18n.setLanguage === 'function') {
                window.i18n.setLanguage(code);
            }
            if (window.Native && typeof window.Native.haptic === 'function') {
                window.Native.haptic('light');
            }
        }

        function nextStep() {
            if (window.Native && typeof window.Native.haptic === 'function') {
                window.Native.haptic('light');
            }
            if (currentStep.value < 5) {
                currentStep.value++;
            } else {
                finishOnboarding();
            }
        }

        function finishOnboarding() {
            localStorage.setItem('seen_onboarding', 'true');
            if (window.Native && typeof window.Native.haptic === 'function') {
                window.Native.haptic('medium');
            }
            emit('completed');
            emit('close');
        }

        return {
            currentStep,
            selectedLang,
            availableLanguages,
            changeLang,
            nextStep,
            finishOnboarding
        };
    }
};
