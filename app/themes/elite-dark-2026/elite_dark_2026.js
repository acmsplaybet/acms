// ==============================================================================
// 2026 DARK ELITE (FLAGSHIP VIP) MASTER DASHBOARD & COMPONENT INTEGRATOR
// Ultra-Smooth 60-120 FPS, 0ms Client-Side Filtering, Instant VIP Access
// ==============================================================================

window.EliteDark2026Dashboard = {
    components: {
        'elite-top-header': window.EliteTopHeader,
        'elite-left-drawer': window.EliteLeftDrawer,
        'elite-historical-archive': window.EliteHistoricalArchive,
        'elite-inbox-modal': window.EliteInboxModal,
        'elite-settings-modal': window.EliteSettingsModal,
        'elite-daily-tickets': window.EliteDailyTickets,
        'elite-match-filters': window.EliteMatchFilters,
        'elite-match-list': window.EliteMatchList,
        'elite-match-detail-modal': window.EliteMatchDetailModal,
        'elite-bottom-nav': window.EliteBottomNav,
        'elite-experts-view': window.EliteExpertsView,
        'elite-strategy-view': window.EliteStrategyView,
        'elite-ai-chat-view': window.EliteAiChatView,
        'elite-onboarding-modal': window.EliteOnboardingModal
    },
    template: `
        <div class="elite-theme-root">
            <!-- 5-Step Onboarding Modal -->
            <elite-onboarding-modal 
                :show="showOnboarding" 
                @close="showOnboarding = false"
                @completed="onOnboardingCompleted">
            </elite-onboarding-modal>

            <!-- Top Sticky Navigation Bar -->
            <elite-top-header 
                :app-config="appConfig"
                :unread-count="2"
                @open-drawer="showDrawer = true"
                @open-history="showHistory = true"
                @open-inbox="showInbox = true"
                @open-settings="showSettings = true">
            </elite-top-header>

            <!-- 50% Special Offer Ribbon (Promo Banner) -->
            <div class="elite-promo-ribbon elite-tap" @click="openPromoInfo">
                <span>⚡ <strong>EXCLUSIVE VIP PASS:</strong> All Pro Banker Slips Unlocked</span>
                <span class="action">ACTIVE ✓</span>
            </div>

            <!-- MAIN CONTENT AREA SWITCHER (5 TABS) -->
            <main class="elite-main-content">
                
                <!-- TAB 1: HOME (BULLETIN & TICKETS) -->
                <div v-if="activeTab === 'home' || activeTab === 'vip'">
                    <!-- Daily Banker Tickets Horizontal Carousel -->
                    <elite-daily-tickets 
                        :tickets="dailyTickets" 
                        @select-ticket="openTicketDetails">
                    </elite-daily-tickets>

                    <!-- Filter Horizon & Live Stats Ticker -->
                    <elite-match-filters
                        :active-filter="activeFilter"
                        :search-query="searchQuery"
                        :total-matches="filteredMatches.length"
                        online-users="3.4k"
                        win-rate="84.6%"
                        @update:filter="setFilter"
                        @update:search="searchQuery = $event">
                    </elite-match-filters>

                    <!-- Grouped League & Match List Feed -->
                    <elite-match-list 
                        :matches="filteredMatches" 
                        @select-match="openMatchModal">
                    </elite-match-list>
                </div>

                <!-- TAB 3: EXPERTS (TIPSTERS LEADERBOARD) -->
                <elite-experts-view v-if="activeTab === 'experts'"></elite-experts-view>

                <!-- TAB 4: STRATEGY (BANKROLL & STAKING) -->
                <elite-strategy-view v-if="activeTab === 'strategy'"></elite-strategy-view>

                <!-- TAB 5: GENIUS AI CHAT ASSISTANT -->
                <elite-ai-chat-view v-if="activeTab === 'aichat'"></elite-ai-chat-view>
            </main>

            <!-- MODALS & BOTTOM SHEETS -->
            <!-- 1. Left Drawer / Menu Sheet -->
            <elite-left-drawer 
                :show="showDrawer" 
                @close="showDrawer = false"
                @open-strategy="activeTab = 'strategy'"
                @open-guide="activeTab = 'strategy'"
                @rate-app="handleRateUs"
                @share-app="handleShareApp">
            </elite-left-drawer>

            <!-- 2. Historical Archive Results Sheet -->
            <elite-historical-archive 
                :show="showHistory" 
                @close="showHistory = false">
            </elite-historical-archive>

            <!-- 3. Push Inbox Modal -->
            <elite-inbox-modal 
                :show="showInbox" 
                @close="showInbox = false">
            </elite-inbox-modal>

            <!-- 4. Preferences & Settings Modal -->
            <elite-settings-modal 
                :show="showSettings" 
                @close="showSettings = false">
            </elite-settings-modal>

            <!-- 5. Deep Match Details & 3-Tab Stats Modal -->
            <elite-match-detail-modal 
                :match="selectedMatch" 
                :show="showMatchModal" 
                @close="showMatchModal = false">
            </elite-match-detail-modal>

            <!-- Floating 5-Tab Bottom Navigation Bar -->
            <elite-bottom-nav 
                :active-tab="activeTab" 
                @change-tab="activeTab = $event">
            </elite-bottom-nav>
        </div>
    `,
    setup() {
        const appConfig = Vue.inject('appConfig') || Vue.ref({ app_name: 'ELITE VIP 2026' });

        // Modal visibility states
        const showOnboarding = Vue.ref(localStorage.getItem('seen_onboarding') !== 'true');
        const showDrawer = Vue.ref(false);
        const showHistory = Vue.ref(false);
        const showInbox = Vue.ref(false);
        const showSettings = Vue.ref(false);
        const showMatchModal = Vue.ref(false);
        const selectedMatch = Vue.ref(null);

        // Tab state
        const activeTab = Vue.ref('home');
        const activeFilter = Vue.ref('ALL');
        const searchQuery = Vue.ref('');

        // Master Demo Matches Database
        const allMatches = Vue.ref([
            {
                id: 1,
                league: 'Argentina Primera División',
                flag: 'https://flagcdn.com/w40/ar.png',
                home_team: 'Lanús',
                away_team: 'Independiente',
                home_logo: 'https://media.api-sports.io/football/teams/448.png',
                away_logo: 'https://media.api-sports.io/football/teams/450.png',
                kickoff_time: '23:00',
                date: 'Today',
                market: '1X2',
                prediction: '1X',
                odd: '1.29',
                odds_1: '2.10',
                odds_x: '3.10',
                odds_2: '3.60',
                confidence: '78%',
                category: 'SAFE',
                home_strength: 4.3,
                away_strength: 3.1
            },
            {
                id: 2,
                league: 'Azerbaijan Premier League',
                flag: 'https://flagcdn.com/w40/az.png',
                home_team: 'Qarabağ',
                away_team: 'Shamakhi FK',
                home_logo: 'https://media.api-sports.io/football/teams/643.png',
                away_logo: 'https://media.api-sports.io/football/teams/645.png',
                kickoff_time: '18:30',
                date: 'Today',
                market: 'HANDICAP',
                prediction: 'Home -1.5',
                odd: '1.52',
                odds_1: '1.18',
                odds_x: '6.50',
                odds_2: '13.00',
                confidence: '88%',
                category: 'SAFE',
                home_strength: 4.8,
                away_strength: 1.9
            },
            {
                id: 3,
                league: 'Italy Serie A',
                flag: 'https://flagcdn.com/w40/it.png',
                home_team: 'Inter Milan',
                away_team: 'Atalanta',
                home_logo: 'https://media.api-sports.io/football/teams/505.png',
                away_logo: 'https://media.api-sports.io/football/teams/499.png',
                kickoff_time: '21:45',
                date: 'Today',
                market: 'GOALS',
                prediction: 'Over 2.5',
                odd: '1.85',
                odds_1: '1.90',
                odds_x: '3.60',
                odds_2: '3.80',
                confidence: '82%',
                category: 'GOALS',
                home_strength: 4.7,
                away_strength: 4.2
            },
            {
                id: 4,
                league: 'England Premier League',
                flag: 'https://flagcdn.com/w40/gb-eng.png',
                home_team: 'Arsenal',
                away_team: 'Wolves',
                home_logo: 'https://media.api-sports.io/football/teams/42.png',
                away_logo: 'https://media.api-sports.io/football/teams/39.png',
                kickoff_time: '17:00',
                date: 'Today',
                market: '1X2',
                prediction: '1',
                odd: '1.38',
                odds_1: '1.38',
                odds_x: '4.75',
                odds_2: '8.50',
                confidence: '90%',
                category: 'SAFE',
                home_strength: 4.9,
                away_strength: 2.7
            },
            {
                id: 5,
                league: 'Spain La Liga',
                flag: 'https://flagcdn.com/w40/es.png',
                home_team: 'Valencia',
                away_team: 'Barcelona',
                home_logo: 'https://media.api-sports.io/football/teams/532.png',
                away_logo: 'https://media.api-sports.io/football/teams/529.png',
                kickoff_time: '22:30',
                date: 'Today',
                market: 'BTTS',
                prediction: 'BTTS - YES',
                odd: '1.75',
                odds_1: '4.20',
                odds_x: '3.80',
                odds_2: '1.78',
                confidence: '79%',
                category: 'GOALS',
                home_strength: 3.4,
                away_strength: 4.6
            }
        ]);

        // Daily Banker Tickets
        const dailyTickets = Vue.ref([
            {
                id: 'safe_1',
                type: 'safe',
                total_odd: '1.50',
                countdown: '7h 44m',
                matches_count: 1,
                matches: [allMatches.value[0], allMatches.value[1]]
            },
            {
                id: 'medium_1',
                type: 'medium',
                total_odd: '1.99',
                countdown: '8h 27m',
                matches_count: 2,
                matches: [allMatches.value[2], allMatches.value[3]]
            },
            {
                id: 'risky_1',
                type: 'risky',
                total_odd: '3.74',
                countdown: '7h 27m',
                matches_count: 3,
                matches: [allMatches.value[2], allMatches.value[4]]
            }
        ]);

        // Client-side 0ms instant filter
        const filteredMatches = Vue.computed(() => {
            let list = allMatches.value;

            // Search query filter
            if (searchQuery.value.trim()) {
                const q = searchQuery.value.toLowerCase();
                list = list.filter(m => 
                    m.home_team.toLowerCase().includes(q) ||
                    m.away_team.toLowerCase().includes(q) ||
                    m.league.toLowerCase().includes(q)
                );
            }

            // Quick pill filters
            if (activeFilter.value === 'SAFE') {
                list = list.filter(m => m.category === 'SAFE');
            } else if (activeFilter.value === 'GOALS') {
                list = list.filter(m => m.category === 'GOALS' || m.market.includes('GOAL') || m.market.includes('BTTS'));
            }

            return list;
        });

        function setFilter(f) {
            activeFilter.value = f;
        }

        function openMatchModal(match) {
            selectedMatch.value = match;
            showMatchModal.value = true;
        }

        function openTicketDetails(ticket) {
            if (ticket.matches && ticket.matches.length > 0) {
                openMatchModal(ticket.matches[0]);
            }
        }

        function openPromoInfo() {
            if (window.Native && typeof window.Native.haptic === 'function') {
                window.Native.haptic('light');
            }
        }

        function handleRateUs() {
            if (window.Native && typeof window.Native.requestInAppReview === 'function') {
                window.Native.requestInAppReview();
            }
            showDrawer.value = false;
        }

        function handleShareApp() {
            if (navigator.share) {
                navigator.share({
                    title: '2026 Dark Elite VIP Betting Tips',
                    text: 'Check out the 2026 Dark Elite VIP betting suite!',
                    url: window.location.href
                }).catch(() => {});
            }
            showDrawer.value = false;
        }

        function onOnboardingCompleted() {
            showOnboarding.value = false;
        }

        return {
            appConfig,
            showOnboarding,
            showDrawer,
            showHistory,
            showInbox,
            showSettings,
            showMatchModal,
            selectedMatch,
            activeTab,
            activeFilter,
            searchQuery,
            dailyTickets,
            filteredMatches,
            setFilter,
            openMatchModal,
            openTicketDetails,
            openPromoInfo,
            handleRateUs,
            handleShareApp,
            onOnboardingCompleted
        };
    }
};
