// ==============================================================================
// 2026 DARK ELITE - TOP HEADER COMPONENT
// Hamburger Menu, Logo, Historical Archive (🕒), Inbox (🔔), Settings (⚙️)
// ==============================================================================

window.EliteTopHeader = {
    props: {
        appConfig: { type: Object, default: () => ({}) },
        unreadCount: { type: Number, default: 2 }
    },
    emits: ['open-drawer', 'open-history', 'open-inbox', 'open-settings'],
    template: `
        <header class="elite-header">
            <!-- Left: Drawer / Menu Trigger -->
            <div class="elite-header-left">
                <button class="elite-header-btn elite-tap" @click="$emit('open-drawer')" title="Menu">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round">
                        <line x1="3" y1="6" x2="21" y2="6"></line>
                        <line x1="3" y1="12" x2="15" y2="12"></line>
                        <line x1="3" y1="18" x2="21" y2="18"></line>
                    </svg>
                </button>
            </div>

            <!-- Center: App Logo & Brand Title -->
            <div class="elite-brand-title">
                <img :src="appConfig?.logo_url || 'assets/logo.png'" alt="Logo" onerror="this.style.display='none'">
                <span>{{ appConfig?.app_name || 'ELITE VIP 2026' }}</span>
            </div>

            <!-- Right: 3 Quick Action Modals -->
            <div class="elite-header-right">
                <!-- 1. Historical Archive (Clock / Calendar) -->
                <button class="elite-header-btn elite-tap" @click="$emit('open-history')" title="History">
                    <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <polyline points="12 6 12 12 16 14"></polyline>
                    </svg>
                </button>

                <!-- 2. Notifications / Inbox -->
                <button class="elite-header-btn elite-tap" @click="$emit('open-inbox')" title="Notifications">
                    <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round">
                        <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                        <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                    </svg>
                    <span v-if="unreadCount > 0" class="elite-badge-dot"></span>
                </button>

                <!-- 3. Settings / Preferences -->
                <button class="elite-header-btn elite-tap" @click="$emit('open-settings')" title="Settings">
                    <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round">
                        <circle cx="12" cy="12" r="3"></circle>
                        <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
                    </svg>
                </button>
            </div>
        </header>
    `,
    setup(props, { emit }) {
        return {};
    }
};
