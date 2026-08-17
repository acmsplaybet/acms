// ==============================================================================
// 2026 DARK ELITE - MATCH FILTERS & SEARCH COMPONENT
// Live Social Proof Ticker, Search Input, 0ms Client-Side Filter Pills
// ==============================================================================

window.EliteMatchFilters = {
    props: {
        activeFilter: { type: String, default: 'ALL' },
        searchQuery: { type: String, default: '' },
        totalMatches: { type: Number, default: 67 },
        onlineUsers: { type: String, default: '2.8k' },
        winRate: { type: String, default: '82.1%' }
    },
    emits: ['update:filter', 'update:search'],
    template: `
        <div class="elite-filters-container">
            <!-- 1. Live Stats Ticker Bar -->
            <div class="elite-ticker-bar">
                <div>
                    <span class="elite-online-dot"></span>
                    <span>{{ onlineUsers }} Online</span>
                </div>
                <div class="elite-win-rate">
                    <span>📈 {{ winRate }} Win Rate</span>
                </div>
                <div style="color:var(--elite-text-muted);">
                    <span>{{ totalMatches }} Predictions</span>
                </div>
            </div>

            <!-- 2. Search Box -->
            <div class="elite-search-box">
                <span class="elite-search-icon">🔍</span>
                <input type="text" 
                       class="elite-search-input" 
                       :placeholder="$t ? $t('common.search_placeholder') : 'Search teams or leagues...'"
                       :value="searchQuery"
                       @input="$emit('update:search', $event.target.value)">
            </div>

            <!-- 3. Horizontal Filter Pills -->
            <div class="elite-filter-pills-scroll">
                <button v-for="pill in filterPills" :key="pill.id"
                        class="elite-pill elite-tap"
                        :class="[pill.class, { active: activeFilter === pill.id }]"
                        @click="setFilter(pill.id)">
                    <span>{{ pill.label }}</span>
                </button>
            </div>
        </div>
    `,
    setup(props, { emit }) {
        const filterPills = [
            { id: 'ALL', label: 'All Predictions', class: '' },
            { id: 'SAFE', label: '🛡️ SAFE PICKS', class: 'safe-pill' },
            { id: 'GOALS', label: '⚽ GOALS & BTTS', class: '' },
            { id: 'UPCOMING', label: '⏳ Upcoming', class: '' },
            { id: 'TODAY', label: '🔥 Today', class: '' },
            { id: 'TOMORROW', label: '📅 Tomorrow', class: '' },
            { id: 'PLUS_1H', label: '+1 Hour', class: '' },
            { id: 'PLUS_3H', label: '+3 Hours', class: '' }
        ];

        function setFilter(id) {
            if (window.Native && typeof window.Native.haptic === 'function') {
                window.Native.haptic('light');
            }
            emit('update:filter', id);
        }

        return {
            filterPills,
            setFilter
        };
    }
};
