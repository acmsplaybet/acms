// ==============================================================================
// 2026 DARK ELITE - LEFT DRAWER / MENU SHEET COMPONENT
// Betting Guide, How to Pick, 7-Day Strategy Course, Rate App, Share App
// ==============================================================================

window.EliteLeftDrawer = {
    props: {
        show: { type: Boolean, default: false }
    },
    emits: ['close', 'open-strategy', 'open-guide', 'rate-app', 'share-app'],
    template: `
        <transition name="fade">
            <div v-if="show" class="elite-modal-overlay" @click.self="$emit('close')">
                <div class="elite-sheet-container elite-drawer-sheet">
                    <!-- Drawer Header -->
                    <div class="elite-sheet-header">
                        <div style="display:flex; align-items:center; gap:10px;">
                            <div class="drawer-header-avatar">👑</div>
                            <div>
                                <h3 style="margin:0; font-size:16px; font-weight:800;">VIP Members Club</h3>
                                <span style="font-size:11px; color:var(--elite-emerald); font-weight:700;">● Active VIP Lifetime Access</span>
                            </div>
                        </div>
                        <button class="elite-header-btn elite-tap" @click="$emit('close')" style="width:34px; height:34px;">✕</button>
                    </div>

                    <!-- Drawer Links List -->
                    <div class="elite-sheet-body" style="padding-top:10px;">
                        <div class="drawer-menu-group">
                            <div class="drawer-group-title">ACADEMY & STRATEGIES</div>
                            
                            <div class="drawer-menu-item elite-tap" @click="$emit('open-strategy'); $emit('close');">
                                <div class="drawer-item-icon" style="background:rgba(0, 230, 118, 0.15); color:var(--elite-emerald);">💡</div>
                                <div class="drawer-item-text">
                                    <strong>7-Day Bankroll Strategy Course</strong>
                                    <span>Learn staking plans & risk management</span>
                                </div>
                                <div class="drawer-item-arrow">›</div>
                            </div>

                            <div class="drawer-menu-item elite-tap" @click="$emit('open-guide'); $emit('close');">
                                <div class="drawer-item-icon" style="background:rgba(0, 229, 255, 0.15); color:var(--elite-cyan);">📖</div>
                                <div class="drawer-item-text">
                                    <strong>How to Pick Winning Bets</strong>
                                    <span>Understanding odds, H2H & value betting</span>
                                </div>
                                <div class="drawer-item-arrow">›</div>
                            </div>
                        </div>

                        <div class="drawer-menu-group">
                            <div class="drawer-group-title">COMMUNITY & REVIEWS</div>
                            
                            <div class="drawer-menu-item elite-tap" @click="$emit('rate-app')">
                                <div class="drawer-item-icon" style="background:rgba(255, 145, 0, 0.15); color:var(--elite-amber);">⭐</div>
                                <div class="drawer-item-text">
                                    <strong>Rate Us 5 Stars</strong>
                                    <span>Support our tipsters & AI engineers</span>
                                </div>
                                <div class="drawer-item-arrow">›</div>
                            </div>

                            <div class="drawer-menu-item elite-tap" @click="$emit('share-app')">
                                <div class="drawer-item-icon" style="background:rgba(41, 121, 255, 0.15); color:var(--elite-blue);">📢</div>
                                <div class="drawer-item-text">
                                    <strong>Share with Friends</strong>
                                    <span>Invite betting partners to the VIP circle</span>
                                </div>
                                <div class="drawer-item-arrow">›</div>
                            </div>
                        </div>

                        <div class="drawer-footer-card">
                            <div style="font-size:24px; margin-bottom:6px;">🛡️</div>
                            <strong style="font-size:13px; color:#FFFFFF;">100% Verified Predictions</strong>
                            <p style="font-size:11px; color:var(--elite-text-muted); margin:4px 0 0 0; line-height:1.4;">
                                All historical odds, statistics and win rates are independently calculated and archived in real time.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </transition>
    `,
    setup() {
        return {};
    }
};
