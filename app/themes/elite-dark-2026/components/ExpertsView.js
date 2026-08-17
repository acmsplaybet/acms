// ==============================================================================
// 2026 DARK ELITE - EXPERTS VIEW COMPONENT (PRO TIPSTERS LEADERBOARD)
// Win Rates, ROI %, Streak Badges, Specialization Cards
// ==============================================================================

window.EliteExpertsView = {
    template: `
        <div class="elite-view-container">
            <div class="elite-view-header">
                <h2 style="font-size:18px; font-weight:800; margin:0 0 4px 0;">👑 Pro Tipsters Leaderboard</h2>
                <p style="font-size:12px; color:var(--elite-text-muted); margin:0;">Verified track records calculated across 1,000+ matches</p>
            </div>

            <div class="elite-experts-grid">
                <div v-for="expert in experts" :key="expert.id" class="elite-expert-card">
                    <div class="expert-card-top">
                        <div class="expert-avatar-box" :style="{ background: expert.gradient }">
                            <span>{{ expert.avatar }}</span>
                        </div>
                        <div class="expert-info">
                            <div style="display:flex; align-items:center; gap:6px;">
                                <strong>{{ expert.name }}</strong>
                                <span class="verified-badge">✓</span>
                            </div>
                            <span class="expert-focus">{{ expert.specialty }}</span>
                        </div>
                        <div class="expert-rank-pill">#{{ expert.rank }}</div>
                    </div>

                    <div class="expert-stats-row">
                        <div class="stat-cell">
                            <strong>{{ expert.winRate }}%</strong>
                            <span>Win Rate</span>
                        </div>
                        <div class="stat-cell">
                            <strong>{{ expert.roi }}%</strong>
                            <span>Monthly ROI</span>
                        </div>
                        <div class="stat-cell">
                            <strong style="color:var(--elite-emerald);">🔥 {{ expert.streak }}</strong>
                            <span>Green Streak</span>
                        </div>
                    </div>

                    <div class="expert-last-pick">
                        <span style="font-size:10px; color:var(--elite-text-muted); font-weight:700;">LATEST TIP:</span>
                        <div style="display:flex; justify-content:space-between; align-items:center; margin-top:4px;">
                            <span style="font-size:12px; font-weight:700;">{{ expert.latestTip.match }}</span>
                            <span style="font-size:11px; color:var(--elite-emerald); font-weight:800; background:rgba(0,230,118,0.1); padding:2px 6px; border-radius:4px;">
                                {{ expert.latestTip.pick }} @ {{ expert.latestTip.odd }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `,
    setup() {
        const experts = Vue.ref([
            {
                id: 1,
                rank: 1,
                name: 'Safe Steve',
                avatar: '🛡️',
                specialty: 'Banker 1X2 & Double Chance',
                gradient: 'linear-gradient(135deg, #00E676, #00B359)',
                winRate: 88.4,
                roi: 34.2,
                streak: '9 Wins',
                latestTip: { match: 'Lanus vs Independiente', pick: 'Home Win (1)', odd: '1.72' }
            },
            {
                id: 2,
                rank: 2,
                name: 'Balanced Ben',
                avatar: '⚖️',
                specialty: 'BTTS & Over 1.5 Goals',
                gradient: 'linear-gradient(135deg, #00E5FF, #2979FF)',
                winRate: 79.2,
                roi: 48.6,
                streak: '6 Wins',
                latestTip: { match: 'Inter Milan vs Atalanta', pick: 'Over 2.5 Goals', odd: '1.85' }
            },
            {
                id: 3,
                rank: 3,
                name: 'Risky Rick',
                avatar: '🚀',
                specialty: 'High Multipliers & HT/FT',
                gradient: 'linear-gradient(135deg, #7C4DFF, #FF3D71)',
                winRate: 64.8,
                roi: 92.4,
                streak: '4 Wins',
                latestTip: { match: 'Brest vs Marseille', pick: 'Away & BTTS', odd: '3.75' }
            }
        ]);

        return {
            experts
        };
    }
};
