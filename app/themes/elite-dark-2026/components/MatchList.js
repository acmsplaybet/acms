// ==============================================================================
// 2026 DARK ELITE - MATCH LIST COMPONENT (LEAGUE GROUPS & MATCH CARDS)
// Grouped Accordions, Odds Badges, Confidence Meters, Star Favorites
// ==============================================================================

window.EliteMatchList = {
    props: {
        matches: { type: Array, default: () => [] }
    },
    emits: ['select-match', 'toggle-favorite'],
    template: `
        <div class="elite-match-list-feed">
            <div v-if="groupedLeagues.length === 0" style="text-align:center; padding:40px 16px; color:var(--elite-text-muted);">
                <div style="font-size:36px; margin-bottom:10px;">🔍</div>
                <p style="font-size:14px; font-weight:600; margin:0;">No matches found matching your active filter or search.</p>
            </div>

            <!-- League Accordion Group -->
            <div v-for="league in groupedLeagues" :key="league.name" class="elite-league-block">
                <!-- League Header -->
                <div class="elite-league-header">
                    <div class="elite-league-info">
                        <img :src="league.flag" class="elite-league-flag" alt="flag" onerror="this.src='data:image/svg+xml;utf8,<svg xmlns=\\'http://www.w3.org/2000/svg\\' width=\\'20\\' height=\\'14\\'><rect width=\\'20\\' height=\\'14\\' fill=\\'%23444\\'/></svg>'">
                        <span>{{ league.name }}</span>
                    </div>
                    <button class="elite-fav-btn elite-tap" 
                            :class="{ active: isLeagueFav(league.name) }"
                            @click.stop="toggleLeagueFav(league.name)">
                        ★
                    </button>
                </div>

                <!-- Match Rows in League -->
                <div v-for="match in league.matches" :key="match.id" 
                     class="elite-match-row elite-tap"
                     @click="onSelect(match)">
                    
                    <!-- Kickoff Time -->
                    <div class="elite-match-time">
                        {{ match.kickoff_time || '20:45' }}
                    </div>

                    <!-- Teams Column -->
                    <div class="elite-match-teams">
                        <div class="elite-team-line">
                            <img :src="match.home_logo || 'assets/team_default.png'" class="elite-team-logo" alt="home" onerror="this.style.opacity=0.4">
                            <span>{{ match.home_team }}</span>
                        </div>
                        <div class="elite-team-line">
                            <img :src="match.away_logo || 'assets/team_default.png'" class="elite-team-logo" alt="away" onerror="this.style.opacity=0.4">
                            <span>{{ match.away_team }}</span>
                        </div>
                    </div>

                    <!-- Prediction Pick & Odd Badge -->
                    <div class="elite-pick-badge">
                        <span class="elite-pick-market">{{ match.market || '1X2' }}</span>
                        <span class="elite-pick-val">{{ match.prediction }}</span>
                        <span class="elite-pick-odd">@ {{ match.odd }}</span>
                    </div>
                </div>
            </div>
        </div>
    `,
    setup(props, { emit }) {
        const favoriteLeagues = Vue.ref(JSON.parse(localStorage.getItem('elite_fav_leagues') || '[]'));

        const groupedLeagues = Vue.computed(() => {
            const groups = {};
            props.matches.forEach(m => {
                const lName = m.league || 'Other Leagues';
                if (!groups[lName]) {
                    groups[lName] = {
                        name: lName,
                        flag: m.flag || 'https://flagcdn.com/w40/un.png',
                        matches: []
                    };
                }
                groups[lName].matches.push(m);
            });
            return Object.values(groups);
        });

        function isLeagueFav(name) {
            return favoriteLeagues.value.includes(name);
        }

        function toggleLeagueFav(name) {
            if (isLeagueFav(name)) {
                favoriteLeagues.value = favoriteLeagues.value.filter(n => n !== name);
            } else {
                favoriteLeagues.value.push(name);
            }
            localStorage.setItem('elite_fav_leagues', JSON.stringify(favoriteLeagues.value));
            if (window.Native && typeof window.Native.haptic === 'function') {
                window.Native.haptic('light');
            }
        }

        function onSelect(match) {
            if (window.Native && typeof window.Native.haptic === 'function') {
                window.Native.haptic('light');
            }
            emit('select-match', match);
        }

        return {
            groupedLeagues,
            isLeagueFav,
            toggleLeagueFav,
            onSelect
        };
    }
};
