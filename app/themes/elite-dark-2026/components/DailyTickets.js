// ==============================================================================
// 2026 DARK ELITE - DAILY TICKETS COMPONENT (3 HORIZONTAL BANKER CARDS)
// Safe (1.50x 🟢), Medium (1.99x 🔵), Risky (3.74x 🟣) + Live Countdown Timers
// ==============================================================================

window.EliteDailyTickets = {
    props: {
        tickets: { type: Array, default: () => [] }
    },
    emits: ['select-ticket'],
    template: `
        <section class="elite-daily-tickets-section">
            <div class="elite-section-header">
                <div class="elite-section-title">
                    <span>👑</span>
                    <span>Daily Banker Tickets</span>
                </div>
                <span class="elite-section-date-tag">Today • Fully Unlocked</span>
            </div>

            <div class="elite-tickets-scroll">
                <!-- 1. SAFE CARD (Green) -->
                <div class="elite-ticket-card safe elite-tap" @click="handleSelect(safeTicket)">
                    <div class="ticket-top">
                        <span class="ticket-badge">🟢 SAFE</span>
                    </div>
                    <div class="ticket-odd">{{ safeTicket.total_odd }}x</div>
                    <div class="ticket-countdown">
                        <span>⏳</span>
                        <span>{{ safeTicket.countdown || '7h 44m' }}</span>
                    </div>
                    <div class="ticket-bottom">
                        <span>{{ safeTicket.matches_count }} Match</span>
                        <span class="ticket-status-pill">VIP ACTIVE</span>
                    </div>
                </div>

                <!-- 2. MEDIUM CARD (Cyan) -->
                <div class="elite-ticket-card medium elite-tap" @click="handleSelect(mediumTicket)">
                    <div class="ticket-top">
                        <span class="ticket-badge">🔵 MEDIUM</span>
                    </div>
                    <div class="ticket-odd">{{ mediumTicket.total_odd }}x</div>
                    <div class="ticket-countdown">
                        <span>⏳</span>
                        <span>{{ mediumTicket.countdown || '8h 27m' }}</span>
                    </div>
                    <div class="ticket-bottom">
                        <span>{{ mediumTicket.matches_count }} Matches</span>
                        <span class="ticket-status-pill">VIP ACTIVE</span>
                    </div>
                </div>

                <!-- 3. RISKY CARD (Purple/Blue) -->
                <div class="elite-ticket-card risky elite-tap" @click="handleSelect(riskyTicket)">
                    <div class="ticket-top">
                        <span class="ticket-badge">🟣 RISKY</span>
                    </div>
                    <div class="ticket-odd">{{ riskyTicket.total_odd }}x</div>
                    <div class="ticket-countdown">
                        <span>⏳</span>
                        <span>{{ riskyTicket.countdown || '7h 27m' }}</span>
                    </div>
                    <div class="ticket-bottom">
                        <span>{{ riskyTicket.matches_count }} Matches</span>
                        <span class="ticket-status-pill">HIGH ROI</span>
                    </div>
                </div>
            </div>
        </section>
    `,
    setup(props, { emit }) {
        const defaultSafe = {
            id: 'daily_safe',
            title: 'Safe Daily Banker',
            type: 'safe',
            total_odd: '1.50',
            countdown: '7h 44m',
            matches_count: 1,
            matches: [
                { league: 'Super Lig', home: 'Galatasaray', away: 'Hatayspor', pick: 'Home Win (1)', odd: '1.25', confidence: '89%' },
                { league: 'Premier League', home: 'Man City', away: 'Ipswich', pick: 'Over 1.5 Goals', odd: '1.20', confidence: '92%' }
            ]
        };

        const defaultMedium = {
            id: 'daily_medium',
            title: 'Medium Value Ticket',
            type: 'medium',
            total_odd: '1.99',
            countdown: '8h 27m',
            matches_count: 2,
            matches: [
                { league: 'Serie A', home: 'Inter Milan', away: 'Atalanta', pick: 'Home Win (1)', odd: '1.85', confidence: '78%' },
                { league: 'La Liga', home: 'Real Madrid', away: 'Valladolid', pick: 'Home -1.5', odd: '1.45', confidence: '84%' }
            ]
        };

        const defaultRisky = {
            id: 'daily_risky',
            title: 'Risky High Multiplier',
            type: 'risky',
            total_odd: '3.74',
            countdown: '7h 27m',
            matches_count: 3,
            matches: [
                { league: 'Ligue 1', home: 'Brest', away: 'Marseille', pick: 'BTTS & Over 2.5', odd: '2.15', confidence: '68%' },
                { league: 'Primeira Liga', home: 'Porto', away: 'Rio Ave', pick: 'Porto Win to Nil', odd: '1.74', confidence: '72%' }
            ]
        };

        const safeTicket = Vue.computed(() => props.tickets?.find(t => t.type === 'safe') || defaultSafe);
        const mediumTicket = Vue.computed(() => props.tickets?.find(t => t.type === 'medium') || defaultMedium);
        const riskyTicket = Vue.computed(() => props.tickets?.find(t => t.type === 'risky') || defaultRisky);

        function handleSelect(ticket) {
            if (window.Native && typeof window.Native.haptic === 'function') {
                window.Native.haptic('light');
            }
            emit('select-ticket', ticket);
        }

        return {
            safeTicket,
            mediumTicket,
            riskyTicket,
            handleSelect
        };
    }
};
