// ==============================================================================
// 2026 DARK ELITE - STRATEGY & BANKROLL CALCULATOR COMPONENT
// Staking Calculator, Kelly Criterion Simulator, 7-Day Bankroll Strategy
// ==============================================================================

window.EliteStrategyView = {
    template: `
        <div class="elite-view-container">
            <div class="elite-view-header">
                <h2 style="font-size:18px; font-weight:800; margin:0 0 4px 0;">💡 Bankroll & Staking Calculator</h2>
                <p style="font-size:12px; color:var(--elite-text-muted); margin:0;">Optimize your bet sizes to minimize risk & compound returns</p>
            </div>

            <!-- Calculator Card -->
            <div class="elite-calculator-card">
                <div class="calc-row">
                    <label>TOTAL BANKROLL ($ / ₺ / €)</label>
                    <input type="number" class="calc-input" v-model.number="bankroll" placeholder="1000">
                </div>
                <div class="calc-row">
                    <label>ODD (ORAN)</label>
                    <input type="number" step="0.05" class="calc-input" v-model.number="odd" placeholder="1.85">
                </div>
                <div class="calc-row">
                    <label>ESTIMATED WIN PROBABILITY (%)</label>
                    <input type="number" class="calc-input" v-model.number="probability" placeholder="65">
                </div>

                <div class="calc-result-box">
                    <div class="res-col">
                        <span>RECOMMENDED STAKE</span>
                        <strong>{{ recommendedStake }}</strong>
                    </div>
                    <div class="res-col">
                        <span>POTENTIAL PROFIT</span>
                        <strong style="color:var(--elite-emerald);">+{{ potentialProfit }}</strong>
                    </div>
                </div>
            </div>

            <!-- 7-Day Strategy Roadmap -->
            <div class="strategy-course-section">
                <h3 style="font-size:15px; font-weight:800; margin:0 0 12px 0;">📚 7-Day Smart Staking Plan</h3>
                
                <div class="strategy-step-card">
                    <div class="step-num">1</div>
                    <div class="step-body">
                        <strong>Rule of 2-5% Unit Staking</strong>
                        <p>Never risk more than 3% of your total bankroll on a single banker tip.</p>
                    </div>
                </div>

                <div class="strategy-step-card">
                    <div class="step-num">2</div>
                    <div class="step-body">
                        <strong>The Double Chance Shield</strong>
                        <p>On tight derbies, hedge with 1X or X2 to convert 60% probability into 88% safe ROI.</p>
                    </div>
                </div>

                <div class="strategy-step-card">
                    <div class="step-num">3</div>
                    <div class="step-body">
                        <strong>Compound Weekly Profits</strong>
                        <p>Recalculate your unit stake every Monday based on updated total balance.</p>
                    </div>
                </div>
            </div>
        </div>
    `,
    setup() {
        const bankroll = Vue.ref(1000);
        const odd = Vue.ref(1.85);
        const probability = Vue.ref(68);

        const recommendedStake = Vue.computed(() => {
            if (!bankroll.value || !odd.value || !probability.value) return '$0.00';
            // Fractional Kelly formula (0.25x Kelly for safety)
            const p = probability.value / 100;
            const b = odd.value - 1;
            const q = 1 - p;
            let f = (b * p - q) / b;
            if (f <= 0) f = 0.02; // minimum safe unit 2%
            const safeFraction = Math.min(Math.max(f * 0.25, 0.02), 0.05);
            return '$' + (bankroll.value * safeFraction).toFixed(2) + ' (' + (safeFraction * 100).toFixed(1) + '%)';
        });

        const potentialProfit = Vue.computed(() => {
            const stakeVal = parseFloat(recommendedStake.value.replace(/[^0-9.]/g, '')) || 0;
            return '$' + ((odd.value - 1) * stakeVal).toFixed(2);
        });

        return {
            bankroll,
            odd,
            probability,
            recommendedStake,
            potentialProfit
        };
    }
};
