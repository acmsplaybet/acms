// ==============================================================================
// 2026 DARK ELITE - BOTTOM NAVIGATION COMPONENT (5 TABS)
// Home (🏠), VIP Picks (👑), Experts (👥), Strategy (💡), AI Tips (🤖)
// ==============================================================================

window.EliteBottomNav = {
    props: {
        activeTab: { type: String, default: 'home' }
    },
    emits: ['change-tab'],
    template: `
        <nav class="elite-bottom-nav">
            <!-- 1. Home / Bulletin -->
            <div class="elite-nav-item elite-tap" 
                 :class="{ active: activeTab === 'home' }"
                 @click="setTab('home')">
                <span class="elite-nav-icon">🏠</span>
                <span>{{ $t ? $t('nav.home') : 'Home' }}</span>
            </div>

            <!-- 2. PRO / VIP Picks -->
            <div class="elite-nav-item elite-tap" 
                 :class="{ active: activeTab === 'vip' }"
                 @click="setTab('vip')">
                <span class="elite-nav-icon">👑</span>
                <span>VIP Picks</span>
            </div>

            <!-- 3. Tipster Experts -->
            <div class="elite-nav-item elite-tap" 
                 :class="{ active: activeTab === 'experts' }"
                 @click="setTab('experts')">
                <span class="elite-nav-icon">👥</span>
                <span>Experts</span>
            </div>

            <!-- 4. Bankroll & Strategy -->
            <div class="elite-nav-item elite-tap" 
                 :class="{ active: activeTab === 'strategy' }"
                 @click="setTab('strategy')">
                <span class="elite-nav-icon">💡</span>
                <span>Strategy</span>
            </div>

            <!-- 5. Genius AI Assistant -->
            <div class="elite-nav-item elite-tap" 
                 :class="{ active: activeTab === 'aichat' }"
                 @click="setTab('aichat')">
                <span class="elite-nav-icon">🤖</span>
                <span>AI Tips</span>
            </div>
        </nav>
    `,
    setup(props, { emit }) {
        function setTab(tab) {
            if (window.Native && typeof window.Native.haptic === 'function') {
                window.Native.haptic('light');
            }
            emit('change-tab', tab);
        }

        return {
            setTab
        };
    }
};
