// ==============================================================================
// 2026 DARK ELITE - MATCH DETAIL & DEEP STATS MODAL (3 TABS)
// 1. SUMMARY (1X2, Double Chance, Alternate Markets, Team Strength 0-5)
// 2. STATS (Last 10 Matches Dual Comparison Bars: Goals, Wins, Clean Sheets)
// 3. H2H (Head-to-Head Circles, Form Badges [W][D][L], Recent Clashes)
// ==============================================================================

window.EliteMatchDetailModal = {
    props: {
        match: { type: Object, default: null },
        show: { type: Boolean, default: false }
    },
    emits: ['close'],
    template: `
        <transition name="fade">
            <div v-if="show && match" class="elite-modal-overlay" @click.self="$emit('close')">
                <div class="elite-sheet-container" style="max-height: 94vh;">
                    
                    <!-- Sheet Header -->
                    <div class="elite-sheet-header">
                        <div style="display:flex; align-items:center; gap:8px;">
                            <span style="font-size:16px;">📊</span>
                            <h3 style="margin:0; font-size:15px; font-weight:800;">{{ match.league }}</h3>
                        </div>
                        <button class="elite-header-btn elite-tap" @click="$emit('close')" style="width:34px; height:34px;">✕</button>
                    </div>

                    <!-- Match Overview Card in Modal -->
                    <div class="elite-modal-match-banner">
                        <div class="modal-team-col">
                            <img :src="match.home_logo || 'assets/team_default.png'" class="modal-team-logo" alt="home">
                            <span class="modal-team-name">{{ match.home_team }}</span>
                        </div>
                        <div class="modal-vs-col">
                            <span class="modal-match-time">{{ match.kickoff_time || '20:45' }}</span>
                            <span class="modal-vs-badge">VS</span>
                            <span class="modal-match-date">{{ match.date || 'Today' }}</span>
                        </div>
                        <div class="modal-team-col">
                            <img :src="match.away_logo || 'assets/team_default.png'" class="modal-team-logo" alt="away">
                            <span class="modal-team-name">{{ match.away_team }}</span>
                        </div>
                    </div>

                    <!-- 3 Tabs Navigation -->
                    <div class="elite-sheet-tabs">
                        <div class="elite-sheet-tab elite-tap" 
                             :class="{ active: activeTab === 'summary' }"
                             @click="setTab('summary')">
                            {{ $t ? $t('match.summary') : 'SUMMARY' }}
                        </div>
                        <div class="elite-sheet-tab elite-tap" 
                             :class="{ active: activeTab === 'stats' }"
                             @click="setTab('stats')">
                            {{ $t ? $t('match.stats') : 'STATS' }}
                        </div>
                        <div class="elite-sheet-tab elite-tap" 
                             :class="{ active: activeTab === 'h2h' }"
                             @click="setTab('h2h')">
                            {{ $t ? $t('match.h2h') : 'H2H' }}
                        </div>
                    </div>

                    <!-- Sheet Body -->
                    <div class="elite-sheet-body">
                        
                        <!-- ================= TAB 1: SUMMARY ================= -->
                        <div v-if="activeTab === 'summary'" class="tab-content-fade">
                            
                            <!-- 1X2 Full Time Odds -->
                            <div class="modal-sub-section">
                                <div class="sub-title">1X2 FULL TIME ODDS</div>
                                <div class="elite-odds-grid">
                                    <div class="elite-odd-box" :class="{ recommended: match.prediction.includes('1') }">
                                        <div class="elite-odd-label">1 (Home)</div>
                                        <div class="elite-odd-val">{{ match.odds_1 || match.odd || '1.75' }}</div>
                                    </div>
                                    <div class="elite-odd-box" :class="{ recommended: match.prediction.includes('X') }">
                                        <div class="elite-odd-label">X (Draw)</div>
                                        <div class="elite-odd-val">{{ match.odds_x || '3.40' }}</div>
                                    </div>
                                    <div class="elite-odd-box" :class="{ recommended: match.prediction.includes('2') }">
                                        <div class="elite-odd-label">2 (Away)</div>
                                        <div class="elite-odd-val">{{ match.odds_2 || '4.20' }}</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Double Chance (1X, 12, X2) -->
                            <div class="modal-sub-section">
                                <div class="sub-title">DOUBLE CHANCE (ÇİFTE ŞANS)</div>
                                <div class="elite-odds-grid">
                                    <div class="elite-odd-box recommended">
                                        <div class="elite-odd-label">1X (Recommended)</div>
                                        <div class="elite-odd-val">1.22</div>
                                    </div>
                                    <div class="elite-odd-box">
                                        <div class="elite-odd-label">12</div>
                                        <div class="elite-odd-val">1.28</div>
                                    </div>
                                    <div class="elite-odd-box">
                                        <div class="elite-odd-label">X2</div>
                                        <div class="elite-odd-val">1.85</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Team Strength (0 - 5) Bars -->
                            <div class="modal-sub-section">
                                <div class="sub-title">TEAM STRENGTH RATING (0 - 5.0)</div>
                                <div class="elite-strength-container">
                                    <div class="strength-line">
                                        <div class="strength-team">
                                            <span>{{ match.home_team }}</span>
                                            <strong>{{ homeStrength }} / 5.0</strong>
                                        </div>
                                        <div class="strength-bar-bg">
                                            <div class="strength-bar-fill home" :style="{ width: (homeStrength / 5 * 100) + '%' }"></div>
                                        </div>
                                    </div>
                                    <div class="strength-line">
                                        <div class="strength-team">
                                            <span>{{ match.away_team }}</span>
                                            <strong>{{ awayStrength }} / 5.0</strong>
                                        </div>
                                        <div class="strength-bar-bg">
                                            <div class="strength-bar-fill away" :style="{ width: (awayStrength / 5 * 100) + '%' }"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="elite-ai-summary-card">
                                    <div style="font-size:16px;">🤖</div>
                                    <p>
                                        <strong>AI Assessment:</strong> {{ match.home_team }} holds an offensive advantage at home with 78% possession dominance. Recommended pick: <strong>{{ match.prediction }}</strong>.
                                    </p>
                                </div>
                            </div>

                            <!-- Alternate Markets Table -->
                            <div class="modal-sub-section">
                                <div class="sub-title">ALTERNATE MARKETS & PROBABILITIES</div>
                                <div class="elite-alt-markets-table">
                                    <div class="alt-row">
                                        <span>Over 1.5 Goals</span>
                                        <strong>1.28</strong>
                                        <span class="prob-tag">82%</span>
                                    </div>
                                    <div class="alt-row">
                                        <span>Over 2.5 Goals</span>
                                        <strong>1.95</strong>
                                        <span class="prob-tag">56%</span>
                                    </div>
                                    <div class="alt-row">
                                        <span>Both Teams to Score (BTTS)</span>
                                        <strong>1.82</strong>
                                        <span class="prob-tag">64%</span>
                                    </div>
                                    <div class="alt-row">
                                        <span>Home Team Over 1.5</span>
                                        <strong>1.68</strong>
                                        <span class="prob-tag">71%</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- ================= TAB 2: STATS ================= -->
                        <div v-if="activeTab === 'stats'" class="tab-content-fade">
                            <div class="sub-title" style="margin-bottom:14px;">LAST 10 MATCHES COMPARISON</div>
                            
                            <!-- Stat 1: Goals Scored -->
                            <div class="elite-stat-bar-row">
                                <div class="elite-stat-label-line">
                                    <span class="left">18</span>
                                    <span class="title">Goals Scored</span>
                                    <span class="right">11</span>
                                </div>
                                <div class="elite-dual-progress">
                                    <div class="elite-prog-left" style="width: 62%;"></div>
                                    <div class="elite-prog-right" style="width: 38%;"></div>
                                </div>
                            </div>

                            <!-- Stat 2: Victories -->
                            <div class="elite-stat-bar-row">
                                <div class="elite-stat-label-line">
                                    <span class="left">7</span>
                                    <span class="title">Victories</span>
                                    <span class="right">4</span>
                                </div>
                                <div class="elite-dual-progress">
                                    <div class="elite-prog-left" style="width: 64%;"></div>
                                    <div class="elite-prog-right" style="width: 36%;"></div>
                                </div>
                            </div>

                            <!-- Stat 3: Clean Sheets -->
                            <div class="elite-stat-bar-row">
                                <div class="elite-stat-label-line">
                                    <span class="left">5</span>
                                    <span class="title">Clean Sheets</span>
                                    <span class="right">2</span>
                                </div>
                                <div class="elite-dual-progress">
                                    <div class="elite-prog-left" style="width: 71%;"></div>
                                    <div class="elite-prog-right" style="width: 29%;"></div>
                                </div>
                            </div>

                            <!-- Stat 4: Avg Goals per Match -->
                            <div class="elite-stat-bar-row">
                                <div class="elite-stat-label-line">
                                    <span class="left">1.8</span>
                                    <span class="title">Avg Goals Scored</span>
                                    <span class="right">1.1</span>
                                </div>
                                <div class="elite-dual-progress">
                                    <div class="elite-prog-left" style="width: 62%;"></div>
                                    <div class="elite-prog-right" style="width: 38%;"></div>
                                </div>
                            </div>
                        </div>

                        <!-- ================= TAB 3: H2H ================= -->
                        <div v-if="activeTab === 'h2h'" class="tab-content-fade">
                            <div class="sub-title" style="margin-bottom:12px;">HEAD TO HEAD (H2H CLASHES)</div>
                            
                            <!-- H2H Outcome Circles -->
                            <div class="elite-h2h-circles">
                                <div class="elite-h2h-circle-item">
                                    <div class="elite-h2h-circle home">4</div>
                                    <span style="font-size:11px; color:var(--elite-cyan); font-weight:700;">Home Wins</span>
                                </div>
                                <div class="elite-h2h-circle-item">
                                    <div class="elite-h2h-circle draw">3</div>
                                    <span style="font-size:11px; color:var(--elite-text-muted); font-weight:700;">Draws</span>
                                </div>
                                <div class="elite-h2h-circle-item">
                                    <div class="elite-h2h-circle away">1</div>
                                    <span style="font-size:11px; color:var(--elite-amber); font-weight:700;">Away Wins</span>
                                </div>
                            </div>

                            <!-- Form Guide (Last 5 matches) -->
                            <div class="modal-sub-section">
                                <div class="sub-title">RECENT FORM (SON MAÇLAR)</div>
                                
                                <div class="form-guide-row">
                                    <span class="form-team">{{ match.home_team }}</span>
                                    <div class="elite-form-badges">
                                        <span class="elite-form-pill w">W</span>
                                        <span class="elite-form-pill w">W</span>
                                        <span class="elite-form-pill d">D</span>
                                        <span class="elite-form-pill w">W</span>
                                        <span class="elite-form-pill l">L</span>
                                    </div>
                                </div>

                                <div class="form-guide-row">
                                    <span class="form-team">{{ match.away_team }}</span>
                                    <div class="elite-form-badges">
                                        <span class="elite-form-pill l">L</span>
                                        <span class="elite-form-pill d">D</span>
                                        <span class="elite-form-pill w">W</span>
                                        <span class="elite-form-pill l">L</span>
                                        <span class="elite-form-pill d">D</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </transition>
    `,
    setup(props) {
        const activeTab = Vue.ref('summary');

        const homeStrength = Vue.computed(() => props.match?.home_strength || 4.2);
        const awayStrength = Vue.computed(() => props.match?.away_strength || 2.8);

        function setTab(tab) {
            activeTab.value = tab;
            if (window.Native && typeof window.Native.haptic === 'function') {
                window.Native.haptic('light');
            }
        }

        return {
            activeTab,
            homeStrength,
            awayStrength,
            setTab
        };
    }
};
