// ==============================================================================
// 2026 DARK ELITE - HISTORICAL ARCHIVE MODAL (🕒 CALENDAR & RESULTS)
// Date Strip Selector, Won / Lost Filtering, Historical Slips & Archive
// ==============================================================================

window.EliteHistoricalArchive = {
    props: {
        show: { type: Boolean, default: false }
    },
    emits: ['close'],
    template: `
        <transition name="fade">
            <div v-if="show" class="elite-modal-overlay" @click.self="$emit('close')">
                <div class="elite-sheet-container">
                    <!-- Sheet Header -->
                    <div class="elite-sheet-header">
                        <div style="display:flex; align-items:center; gap:8px;">
                            <span style="font-size:18px;">🕒</span>
                            <h3 style="margin:0; font-size:16px; font-weight:800;">Historical Results & Archive</h3>
                        </div>
                        <button class="elite-header-btn elite-tap" @click="$emit('close')" style="width:34px; height:34px;">✕</button>
                    </div>

                    <!-- Horizontal Date Strip -->
                    <div class="elite-date-strip-scroll">
                        <div v-for="d in dateDays" :key="d.date"
                             class="elite-date-pill elite-tap"
                             :class="{ active: selectedDate === d.date }"
                             @click="selectDate(d.date)">
                            <span class="day-label">{{ d.dayLabel }}</span>
                            <span class="day-num">{{ d.dayNum }}</span>
                            <span v-if="d.isToday" class="today-dot">●</span>
                        </div>
                    </div>

                    <!-- Results Summary Bar -->
                    <div class="elite-history-stats-bar">
                        <div class="stat-pill win">
                            <span class="icon">🟢</span>
                            <span>{{ winCount }} WON</span>
                        </div>
                        <div class="stat-pill loss">
                            <span class="icon">🔴</span>
                            <span>{{ lossCount }} LOST</span>
                        </div>
                        <div class="stat-pill rate">
                            <span>Win Rate: <strong>{{ winRate }}%</strong></span>
                        </div>
                    </div>

                    <!-- Archive Match Feed -->
                    <div class="elite-sheet-body" style="padding-top:10px;">
                        <div v-if="filteredHistory.length === 0" style="text-align:center; padding:30px 10px; color:var(--elite-text-muted);">
                            <div style="font-size:32px; margin-bottom:8px;">📂</div>
                            <p style="font-size:13px; margin:0;">No archived matches found for this date.</p>
                        </div>

                        <div v-for="item in filteredHistory" :key="item.id" class="elite-history-card">
                            <div class="history-card-top">
                                <span class="history-league">{{ item.league }}</span>
                                <span class="history-score-badge">{{ item.score }}</span>
                            </div>
                            <div class="history-card-match">
                                <div class="teams">
                                    <div class="team">{{ item.home_team }}</div>
                                    <div class="team">{{ item.away_team }}</div>
                                </div>
                                <div class="pick-box" :class="item.status">
                                    <span class="market">{{ item.market }}</span>
                                    <span class="pick">{{ item.prediction }}</span>
                                    <span class="odd">@ {{ item.odd }}</span>
                                </div>
                            </div>
                            <div class="history-card-footer">
                                <span class="tipster">Tipster: <strong>{{ item.tipster }}</strong></span>
                                <span class="badge" :class="item.status">{{ item.status === 'won' ? 'WON ✓' : 'LOST ✕' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </transition>
    `,
    setup() {
        const selectedDate = Vue.ref('2026-08-16');

        // Dynamic 7-day strip generator
        const dateDays = [
            { date: '2026-08-11', dayLabel: 'MON', dayNum: '11', isToday: false },
            { date: '2026-08-12', dayLabel: 'TUE', dayNum: '12', isToday: false },
            { date: '2026-08-13', dayLabel: 'WED', dayNum: '13', isToday: false },
            { date: '2026-08-14', dayLabel: 'THU', dayNum: '14', isToday: false },
            { date: '2026-08-15', dayLabel: 'FRI', dayNum: '15', isToday: false },
            { date: '2026-08-16', dayLabel: 'SAT', dayNum: '16', isToday: false },
            { date: '2026-08-17', dayLabel: 'SUN', dayNum: '17', isToday: true }
        ];

        // Demo historical results
        const historyDatabase = [
            { id: 101, date: '2026-08-16', league: 'Premier League', home_team: 'Arsenal', away_team: 'Wolves', score: '2 - 0 (FT)', market: '1X2', prediction: 'Home Win (1)', odd: '1.38', status: 'won', tipster: 'Safe Steve' },
            { id: 102, date: '2026-08-16', league: 'La Liga', home_team: 'Valencia', away_team: 'Barcelona', score: '1 - 2 (FT)', market: '1X2', prediction: 'Away Win (2)', odd: '1.75', status: 'won', tipster: 'Balanced Ben' },
            { id: 103, date: '2026-08-16', league: 'Serie A', home_team: 'Genoa', away_team: 'Inter Milan', score: '2 - 2 (FT)', market: 'GOALS', prediction: 'Over 2.5', odd: '1.92', status: 'won', tipster: 'Risky Rick' },
            { id: 104, date: '2026-08-16', league: 'Ligue 1', home_team: 'Brest', away_team: 'Marseille', score: '1 - 5 (FT)', market: 'BTTS', prediction: 'BTTS - YES', odd: '1.80', status: 'won', tipster: 'Balanced Ben' },
            { id: 105, date: '2026-08-16', league: 'Liga Portugal', home_team: 'Nacional', away_team: 'Sporting CP', score: '1 - 6 (FT)', market: 'HANDICAP', prediction: 'Away -1.5', odd: '1.65', status: 'won', tipster: 'Safe Steve' },
            { id: 106, date: '2026-08-15', league: 'Super Lig', home_team: 'Galatasaray', away_team: 'Hatayspor', score: '2 - 1 (FT)', market: '1X2', prediction: 'Home Win (1)', odd: '1.25', status: 'won', tipster: 'Safe Steve' },
            { id: 107, date: '2026-08-15', league: 'La Liga', home_team: 'Athletic Bilbao', away_team: 'Getafe', score: '1 - 1 (FT)', market: '1X2', prediction: 'Home Win (1)', odd: '1.55', status: 'lost', tipster: 'Balanced Ben' }
        ];

        const filteredHistory = Vue.computed(() => {
            return historyDatabase.filter(m => m.date === selectedDate.value);
        });

        const winCount = Vue.computed(() => filteredHistory.value.filter(m => m.status === 'won').length);
        const lossCount = Vue.computed(() => filteredHistory.value.filter(m => m.status === 'lost').length);
        const winRate = Vue.computed(() => {
            const total = filteredHistory.value.length;
            if (total === 0) return '100';
            return ((winCount.value / total) * 100).toFixed(1);
        });

        function selectDate(d) {
            selectedDate.value = d;
            if (window.Native && typeof window.Native.haptic === 'function') {
                window.Native.haptic('light');
            }
        }

        return {
            selectedDate,
            dateDays,
            filteredHistory,
            winCount,
            lossCount,
            winRate,
            selectDate
        };
    }
};
