// ==============================================================================
// 2026 DARK ELITE - INBOX MODAL COMPONENT (🔔 PUSH NOTIFICATION LOGS)
// VIP Tip Drops, Live Match Updates, System Bulletins
// ==============================================================================

window.EliteInboxModal = {
    props: {
        show: { type: Boolean, default: false }
    },
    emits: ['close'],
    template: `
        <transition name="fade">
            <div v-if="show" class="elite-modal-overlay" @click.self="$emit('close')">
                <div class="elite-sheet-container">
                    <div class="elite-sheet-header">
                        <div style="display:flex; align-items:center; gap:8px;">
                            <span style="font-size:18px;">🔔</span>
                            <h3 style="margin:0; font-size:16px; font-weight:800;">Notifications & Alerts</h3>
                        </div>
                        <button class="elite-header-btn elite-tap" @click="$emit('close')" style="width:34px; height:34px;">✕</button>
                    </div>

                    <div class="elite-sheet-body" style="padding-top:10px;">
                        <div v-for="notif in notifications" :key="notif.id" 
                             class="elite-inbox-item elite-tap"
                             :class="{ unread: !notif.read }">
                            <div class="inbox-icon-box" :class="notif.type">
                                <span v-if="notif.type === 'tip'">👑</span>
                                <span v-else-if="notif.type === 'win'">🟢</span>
                                <span v-else>📢</span>
                            </div>
                            <div class="inbox-content">
                                <div class="inbox-top-line">
                                    <strong class="inbox-title">{{ notif.title }}</strong>
                                    <span class="inbox-time">{{ notif.time }}</span>
                                </div>
                                <p class="inbox-desc">{{ notif.message }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </transition>
    `,
    setup() {
        const notifications = Vue.ref([
            { id: 1, type: 'tip', title: '🔥 Banker of the Day Confirmed', message: 'Lanus vs Independiente - Home Win @ 1.72 has been released by Safe Steve.', time: '10m ago', read: false },
            { id: 2, type: 'win', title: '✅ Slip Won: Inter Milan Win', message: 'Inter Milan 2 - 0 Atalanta ended in green profit. Congratulations to all VIP members!', time: '2h ago', read: false },
            { id: 3, type: 'info', title: '🌟 2026 Dark Elite Theme Live', message: 'Welcome to the upgraded 2026 Dark Elite VIP experience with instant match analytics.', time: '1d ago', read: true }
        ]);

        return {
            notifications
        };
    }
};
