const { createApp, ref, reactive, onMounted, provide, inject, computed, onUnmounted, watch, nextTick } = Vue;
const { createRouter, createWebHashHistory } = VueRouter;

// --- NATIVE DEVICE BRIDGE HELPERS (F4.0) ---
const Native = {
    setSecureFlag(enable) {
        try {
            if (window.AndroidBridge && typeof window.AndroidBridge.setSecureFlag === 'function') {
                window.AndroidBridge.setSecureFlag(enable);
            }
            const plugin = window.Capacitor?.Plugins?.AppNative;
            if (plugin && plugin.setSecureFlag) {
                plugin.setSecureFlag({ enable: !!enable }).catch(() => {});
            }
        } catch (e) {
            console.warn('[Native.setSecureFlag]', e);
        }
    },

    setKeepAwake(enable) {
        try {
            const userPref = (window.i18n && window.i18n.keepScreenAwake) ? window.i18n.keepScreenAwake.value : (localStorage.getItem('app_keep_awake') !== '0');
            if (enable && !userPref) {
                enable = false;
            }
            if (enable && appConfigRef.value && appConfigRef.value.keep_screen_awake === false) {
                enable = false;
            }
            if (window.AndroidBridge && typeof window.AndroidBridge.setKeepAwake === 'function') {
                window.AndroidBridge.setKeepAwake(enable);
                return;
            }
            const plugin = window.Capacitor?.Plugins?.AppNative;
            if (plugin && plugin.setKeepAwake) {
                plugin.setKeepAwake({ enable: !!enable }).catch(() => {});
            }
        } catch (e) {
            console.warn('[Native.setKeepAwake]', e);
        }
    },

    syncScreenAwake() {
        try {
            const hash = window.location.hash || '';
            if (hash.includes('/tips') || hash.includes('/vip')) {
                this.setKeepAwake(true);
            } else {
                this.setKeepAwake(false);
            }
        } catch (e) {}
    },

    haptic(style = 'light') {
        try {
            if (appConfigRef.value && appConfigRef.value.enable_haptic === false) {
                return;
            }
            const effectiveStyle = (appConfigRef.value && appConfigRef.value.haptic_intensity) ? appConfigRef.value.haptic_intensity : style;
            
            if (window.AndroidBridge && typeof window.AndroidBridge.triggerHaptic === 'function') {
                window.AndroidBridge.triggerHaptic(effectiveStyle);
                return;
            }
            const plugin = window.Capacitor?.Plugins?.AppNative;
            if (plugin && plugin.triggerHaptic) {
                plugin.triggerHaptic({ style: effectiveStyle }).catch(() => {});
                return;
            }
            if (typeof navigator !== 'undefined' && navigator.vibrate) {
                if (effectiveStyle === 'heavy') navigator.vibrate(50);
                else if (effectiveStyle === 'medium') navigator.vibrate(25);
                else navigator.vibrate(15);
            }
        } catch (e) {}
    },

    requestInAppReview() {
        try {
            if (window.AndroidBridge && typeof window.AndroidBridge.requestInAppReview === 'function') {
                window.AndroidBridge.requestInAppReview();
                return;
            }
            const plugin = window.Capacitor?.Plugins?.AppNative;
            if (plugin && plugin.requestInAppReview) {
                plugin.requestInAppReview().catch(() => {});
            }
        } catch (e) {
            console.warn('[Native.requestInAppReview]', e);
        }
    }
};

function applyScreenSecurity(path, appConfig) {
    const appId = appConfig?.app_id || '1';
    const isAppPreventScreenshot = appConfig?.prevent_screenshot !== false; // Default: true (1)
    const isExempt = localStorage.getItem('acms_exempt_screenshot_' + appId) === '1';

    // If app enables protection AND user is not exempt from screenshot, block screenshot/screen recording
    if (isAppPreventScreenshot && !isExempt) {
        Native.setSecureFlag(true);
    } else {
        Native.setSecureFlag(false);
    }
}

// --- CENTRALIZED ONESIGNAL PUSH NOTIFICATION CLIENT ---
const PushClient = {
    isInitialized: false,

    init(appConfig, router) {
        if (this.isInitialized) return;
        const appId = appConfig?.onesignal_app_id;
        if (!appId) {
            console.log('[OneSignal] No OneSignal App ID configured for this app.');
            return;
        }

        const OneSignal = window.plugins?.OneSignal || window.OneSignal;
        if (!OneSignal) {
            console.log('[OneSignal] OneSignal plugin not detected on this platform.');
            return;
        }

        try {
            console.log('[OneSignal] Initializing with App ID:', appId);
            // 1. Initialize
            if (typeof OneSignal.initialize === 'function') {
                OneSignal.initialize(appId);
                
                // Request Permission
                if (OneSignal.Notifications && typeof OneSignal.Notifications.requestPermission === 'function') {
                    OneSignal.Notifications.requestPermission(true).then((accepted) => {
                        console.log('[OneSignal] Notification permission granted:', accepted);
                    }).catch(err => console.log('[OneSignal] Permission error:', err));
                }

                // Notification Click Listener (Deep Link)
                if (OneSignal.Notifications && typeof OneSignal.Notifications.addEventListener === 'function') {
                    OneSignal.Notifications.addEventListener('click', (event) => {
                        console.log('[OneSignal] Notification clicked:', event);
                        try {
                            const data = event?.notification?.additionalData || {};
                            const targetRoute = data.route || data.target_url || '/app/tips';
                            if (router && targetRoute) {
                                router.push(targetRoute.startsWith('/') ? targetRoute : '/' + targetRoute);
                            }
                        } catch (routeErr) {
                            console.warn('[OneSignal] Routing on click error:', routeErr);
                        }
                    });
                }
            } else if (typeof OneSignal.setAppId === 'function') {
                OneSignal.setAppId(appId);
            }

            this.isInitialized = true;
            console.log('[OneSignal] Push client successfully initialized.');
        } catch (e) {
            console.warn('[OneSignal] Init error:', e);
        }
    },

    setUser(user, appConfig) {
        const OneSignal = window.plugins?.OneSignal || window.OneSignal;
        if (!OneSignal) return;

        try {
            if (user && user.id) {
                const userIdStr = String(user.id);
                const appIdStr = String(appConfig?.app_id || 1);
                const statusStr = user.status || 'pending';

                if (OneSignal.login) {
                    OneSignal.login(userIdStr);
                }
                if (OneSignal.User && typeof OneSignal.User.addTags === 'function') {
                    const currentLang = (window.i18n && window.i18n.currentLang) ? window.i18n.currentLang.value : 'en';
                    OneSignal.User.addTags({
                        user_id: userIdStr,
                        app_id: appIdStr,
                        status: statusStr,
                        language: currentLang
                    });
                    console.log('[OneSignal] User tags set:', { user_id: userIdStr, app_id: appIdStr, status: statusStr, language: currentLang });
                }
            }
        } catch (e) {
            console.warn('[OneSignal] SetUser error:', e);
        }
    },

    logout() {
        const OneSignal = window.plugins?.OneSignal || window.OneSignal;
        if (!OneSignal) return;
        try {
            if (OneSignal.logout) {
                OneSignal.logout();
            }
            if (OneSignal.User && typeof OneSignal.User.removeTags === 'function') {
                OneSignal.User.removeTags(['user_id', 'status']);
            }
        } catch (e) {
            console.warn('[OneSignal] Logout error:', e);
        }
    }
};

// --- GLOBAL NETWORK STATE (F4.4) ---
const networkState = reactive({
    isOnline: true,
    showReconnected: false
});

// --- GLOBAL TOAST STATE (F2.3) ---
const toastState = reactive({
    toasts: []
});
let toastIdCounter = 0;
const showToast = (message, type = 'success') => {
    const id = toastIdCounter++;
    toastState.toasts.push({ id, message, type });
    setTimeout(() => {
        toastState.toasts = toastState.toasts.filter(t => t.id !== id);
    }, 3000); // 3 seconds
};

// --- COMPONENT: Onboarding (F2.7) ---
const Onboarding = {
    template: `
        <div class="onboarding-container">
            <div class="ob-image">
                <svg v-if="step === 1" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline><polyline points="17 6 23 6 23 12"></polyline></svg>
                <svg v-else-if="step === 2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                <svg v-else-if="step === 3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
            </div>
            <h1 class="ob-title">{{ steps[step-1].title }}</h1>
            <p class="ob-desc">{{ steps[step-1].desc }}</p>
            
            <div class="ob-dots">
                <div class="ob-dot" :class="{active: step === 1}"></div>
                <div class="ob-dot" :class="{active: step === 2}"></div>
                <div class="ob-dot" :class="{active: step === 3}"></div>
            </div>
            
            <button class="btn-gradient btn-block" @click="nextStep">{{ step === 3 ? 'Get Started' : 'Next' }}</button>
        </div>
    `,
    setup() {
        const appConfig = inject('appConfig');
        const router = VueRouter.useRouter();
        const step = ref(1);
        const steps = computed(() => {
            return appConfig.value?.onboarding_steps || [
                { title: 'High Win Rate', desc: 'Get access to premium betting tips with a highly proven success record.' },
                { title: 'Daily Safe Picks', desc: 'Our experts analyze hundreds of matches to bring you the safest picks daily.' },
                { title: 'Join the VIP Family', desc: 'Become a VIP member today and start winning consistently.' }
            ];
        });

        const nextStep = () => {
            if (step.value < 3) {
                step.value++;
            } else {
                localStorage.setItem('seen_onboarding', 'true');
                router.push('/');
            }
        };

        return { step, steps, nextStep };
    }
};

// --- CENTRALIZED NATIVE & WEB GOOGLE SIGN IN HANDLER ---
async function triggerGoogleSignIn(router, appConfig, fallbackOpenModalCallback) {
    const isNative = window.Capacitor && window.Capacitor.isNativePlatform && window.Capacitor.isNativePlatform();
    const GoogleAuth = window.Capacitor?.Plugins?.GoogleAuth || window.GoogleAuth;

    if (isNative && GoogleAuth) {
        try {
            showToast(window.i18n ? window.i18n.t('auth.google_selecting') : 'Selecting Google account...', 'info');
            try {
                await GoogleAuth.initialize({
                    clientId: '366733771640-81uenafnkg3lgkp5rpge6lubcmq2mha9.apps.googleusercontent.com',
                    grantOfflineAccess: false
                });
            } catch (initErr) {
                console.log('[GoogleAuth] Init info:', initErr);
            }

            const googleUser = await GoogleAuth.signIn();
            if (googleUser && googleUser.email) {
                const res = await apiCall('auth/google_auth.php', 'POST', {
                    email: googleUser.email.trim(),
                    name: googleUser.name || googleUser.displayName || googleUser.givenName || googleUser.email.split('@')[0],
                    google_id: googleUser.id || null,
                    avatar_url: googleUser.imageUrl || null,
                    id_token: googleUser.authentication?.idToken || null,
                    app_id: appConfig?.value?.app_id || 1,
                    gpa_code: 'GOOGLE-PLAY-VERIFIED'
                });

                if (res && res.status === 'success') {
                    localStorage.setItem('acms_token_1', res.token);
                    if (res.user) {
                        PushClient.setUser(res.user, appConfig?.value);
                    }
                    showToast(window.i18n ? window.i18n.t('auth.google_signin_success') : 'Signed in with Google successfully!', 'success');
                    router.push('/app/home');
                    return;
                } else if (res && res.status === 'banned') {
                    localStorage.setItem('acms_token_1', res.token);
                    showToast((window.i18n ? window.i18n.t('auth.account_suspended') : 'Account suspended: ') + (res.ban_reason || (window.i18n ? window.i18n.t('support.title') : 'Support')), 'error');
                    router.push('/app/home');
                    return;
                } else {
                    showToast(res?.message || (window.i18n ? window.i18n.t('auth.google_signin_failed') : 'Google Sign-In failed.'), 'error');
                    return;
                }
            }
        } catch (nativeErr) {
            console.warn('[GoogleAuth] Native Sign-In canceled or failed:', nativeErr);
            const errStr = (nativeErr?.message || (typeof nativeErr === 'object' ? JSON.stringify(nativeErr) : String(nativeErr)) || '').toLowerCase();
            if (errStr.includes('cancel') || errStr.includes('12501') || errStr.includes('closed') || errStr.includes('abort')) {
                return;
            }
            showToast('Google Error: ' + (nativeErr?.message || JSON.stringify(nativeErr) || 'Unknown error'), 'error');
            if (fallbackOpenModalCallback) fallbackOpenModalCallback();
            return;
        }
    }

    if (fallbackOpenModalCallback) {
        fallbackOpenModalCallback();
    }
}

// --- CENTRALIZED SIGN OUT (Clears local session & resets Google native account cache) ---
async function performSignOut(router) {
    PushClient.logout();
    const isNative = window.Capacitor && window.Capacitor.isNativePlatform && window.Capacitor.isNativePlatform();
    const GoogleAuth = window.Capacitor?.Plugins?.GoogleAuth || window.GoogleAuth;
    if (isNative && GoogleAuth && typeof GoogleAuth.signOut === 'function') {
        try {
            await GoogleAuth.initialize({
                clientId: '366733771640-81uenafnkg3lgkp5rpge6lubcmq2mha9.apps.googleusercontent.com',
                grantOfflineAccess: false
            }).catch(() => {});
            await GoogleAuth.signOut().catch(err => console.log('[GoogleAuth] signOut info:', err));
        } catch (e) {
            console.log('[GoogleAuth] Native signOut error ignored:', e);
        }
    }
    try {
        await apiCall('auth/logout.php', 'POST');
    } catch (e) {
        console.warn('Logout API info:', e);
    }
    localStorage.removeItem('acms_token_1');
    localStorage.removeItem('acms_exempt_security_1');
    localStorage.removeItem('acms_exempt_screenshot_1');
    showToast(window.i18n ? window.i18n.t('profile.sign_out_success') : 'Signed out successfully.', 'info');
    if (router) {
        router.push('/');
    } else {
        window.location.hash = '#/';
    }
}

// --- COMPONENT: Guest Landing Page (F1.3) ---
const GuestLanding = {
    template: `
        <div class="page-container">
            <div class="auth-header">
                <img :src="appConfig?.logo_url" alt="App Logo" v-if="appConfig?.logo_url">
                <h1 style="color: var(--color-primary);">{{ appConfig?.app_name || 'ACMS App' }}</h1>
            </div>
            <div class="guide-cards">
                <div class="guide-card">
                    <div class="guide-card-icon">1</div>
                    <div class="guide-card-text">{{ appConfig?.guide_steps?.[0] || $t('onboarding.step1_title') }}</div>
                </div>
                <div class="guide-card">
                    <div class="guide-card-icon">2</div>
                    <div class="guide-card-text">{{ appConfig?.guide_steps?.[1] || $t('onboarding.step2_title') }}</div>
                </div>
                <div class="guide-card">
                    <div class="guide-card-icon">3</div>
                    <div class="guide-card-text">{{ appConfig?.guide_steps?.[2] || $t('onboarding.step3_title') }}</div>
                </div>
            </div>
            
            <div class="auth-actions">
                <button class="btn-google btn-block" @click="openGoogleAuth">
                    <svg width="18" height="18" viewBox="0 0 24 24">
                        <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                        <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                        <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/>
                        <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/>
                    </svg>
                    <span>{{ $t('auth.google_signin') }}</span>
                </button>
                <div class="auth-divider"><span>{{ $t('auth.or') }}</span></div>
                <button class="btn-gradient btn-block" @click="$router.push('/register')">{{ $t('auth.signup') }}</button>
                <button class="btn-outline btn-block" @click="$router.push('/login')">{{ $t('auth.signin') }}</button>
            </div>

            <!-- Google Auth Sheet/Modal -->
            <transition name="slide-up">
                <div class="bottom-sheet-overlay" v-if="showGoogleModal" @click.self="showGoogleModal = false">
                    <div class="bottom-sheet-content" style="max-height: 90vh;">
                        <div class="bottom-sheet-header">
                            <div style="display:flex; align-items:center; gap:10px;">
                                <svg width="22" height="22" viewBox="0 0 24 24">
                                    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                                    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                                    <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/>
                                    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/>
                                </svg>
                                <h3 style="margin:0; font-size:16px;">{{ $t('auth.google_signin') }}</h3>
                            </div>
                            <button class="close-btn" @click="showGoogleModal = false">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                            </button>
                        </div>
                        <div class="bottom-sheet-body" style="padding-top:10px;">
                            <p style="font-size:13px; color:#9ca3af; margin-bottom:16px; line-height:1.5;">
                                Your Google Play account will be verified automatically for instant review.
                            </p>

                            <div class="form-group" style="margin-bottom:12px;">
                                <label style="font-size:12px; color:#cbd5e1;">{{ $t('auth.full_name') }}</label>
                                <input type="text" class="form-control" v-model="googleName" placeholder="e.g. John Doe">
                            </div>
                            <div class="form-group" style="margin-bottom:16px;">
                                <label style="font-size:12px; color:#cbd5e1;">Google Play / Gmail {{ $t('auth.email') }}</label>
                                <input type="email" class="form-control" v-model="googleEmail" placeholder="user@gmail.com" required>
                            </div>

                            <div style="background:rgba(66, 133, 244, 0.08); border:1px solid rgba(66, 133, 244, 0.2); border-radius:10px; padding:12px; margin-bottom:18px; display:flex; gap:10px; align-items:center;">
                                <div style="color:#60a5fa; font-size:18px;">🛡️</div>
                                <div style="font-size:11px; color:#93c5fd; line-height:1.4;">
                                    Google Play purchase verification will be attached to your account automatically.
                                </div>
                            </div>

                            <button class="btn-google btn-block" style="width:100%; justify-content:center; margin-bottom:8px;" @click="executeGoogleAuth" :disabled="isGoogleLoading || !googleEmail">
                                <svg width="18" height="18" viewBox="0 0 24 24">
                                    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                                    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                                    <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/>
                                    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/>
                                </svg>
                                <span>{{ isGoogleLoading ? $t('common.loading') : $t('auth.google_signin') }}</span>
                            </button>
                        </div>
                    </div>
                </div>
            </transition>

            <div class="modal-overlay" v-if="showModal">
                <div class="modal-content">
                    <h2 style="margin-bottom: 16px; color: var(--color-primary);">{{ $t('home.important_notice') }}</h2>
                    <p>{{ appConfig?.announcement_modal?.text }}</p>
                    <button class="btn-gradient btn-block" @click="closeModal">{{ $t('common.got_it') }}</button>
                </div>
            </div>
        </div>
    `,
    setup() {
        const appConfig = inject('appConfig');
        const router = VueRouter.useRouter();
        const showModal = ref(false);
        const showGoogleModal = ref(false);
        const googleName = ref('');
        const googleEmail = ref('');
        const isGoogleLoading = ref(false);

        onMounted(() => {
            if (appConfig.value?.announcement_modal?.show_on_login) {
                const modalId = appConfig.value.announcement_modal.id;
                const seen = localStorage.getItem('seen_modal_' + modalId);
                if (!seen) showModal.value = true;
            }
        });

        const closeModal = () => {
            showModal.value = false;
            const modalId = appConfig.value?.announcement_modal?.id;
            if (modalId) localStorage.setItem('seen_modal_' + modalId, 'true');
        };

        const openGoogleAuth = () => {
            triggerGoogleSignIn(router, appConfig, () => {
                googleEmail.value = '';
                googleName.value = '';
                showGoogleModal.value = true;
            });
        };

        const executeGoogleAuth = async () => {
            if (!googleEmail.value || !googleEmail.value.includes('@')) {
                showToast(window.i18n ? window.i18n.t('auth.invalid_google_email') : 'Please enter a valid Google email address.', 'error');
                return;
            }
            isGoogleLoading.value = true;
            try {
                const res = await apiCall('auth/google_auth.php', 'POST', {
                    email: googleEmail.value.trim(),
                    name: googleName.value.trim() || googleEmail.value.split('@')[0],
                    app_id: 1,
                    gpa_code: 'GOOGLE-PLAY-VERIFIED'
                });

                if (res && res.status === 'success') {
                    localStorage.setItem('acms_token_1', res.token);
                    showGoogleModal.value = false;
                    showToast(window.i18n ? window.i18n.t('common.success') : 'Success', 'success');
                    router.push('/app/home');
                } else if (res && res.status === 'banned') {
                    localStorage.setItem('acms_token_1', res.token);
                    showToast((window.i18n ? window.i18n.t('auth.account_suspended') : 'Account suspended: ') + (res.ban_reason || (window.i18n ? window.i18n.t('support.title') : 'Support')), 'error');
                    router.push('/app/home');
                } else {
                    showToast(res?.message || (window.i18n ? window.i18n.t('auth.google_signin_failed') : 'Authentication failed.'), 'error');
                }
            } catch (err) {
                showToast(window.i18n ? window.i18n.t('common.connection_error') : 'Connection error occurred.', 'error');
            } finally {
                isGoogleLoading.value = false;
            }
        };

        return { 
            appConfig, showModal, closeModal, 
            showGoogleModal, googleName, googleEmail, isGoogleLoading,
            openGoogleAuth, executeGoogleAuth
        };
    }
};

// --- COMPONENT: Sign Up Form (F1.4) ---
const Register = {
    template: `
        <div class="page-container">
            <div class="auth-header" style="margin-top: 3vh; margin-bottom: 20px;">
                <h1>{{ $t('auth.signup_title') }}</h1>
                <p style="color: #a0a0a0; font-size: 14px; margin-top: 6px;">{{ $t('auth.signup_desc') }}</p>
            </div>
            
            <button class="btn-google btn-block" style="margin-bottom: 16px;" @click="openGoogleAuth">
                <svg width="18" height="18" viewBox="0 0 24 24">
                    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                    <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/>
                    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/>
                </svg>
                <span>{{ $t('auth.google_signin') }}</span>
            </button>

            <div class="auth-divider"><span>{{ $t('auth.or') }}</span></div>

            <form @submit.prevent="submitForm">
                <div class="form-group">
                    <label>{{ $t('auth.full_name') }}</label>
                    <input type="text" class="form-control" v-model="name" placeholder="John Doe" required>
                </div>
                <div class="form-group">
                    <label>{{ $t('auth.email') }}</label>
                    <input type="email" class="form-control" v-model="email" placeholder="john@example.com" required>
                </div>
                <div class="form-group">
                    <label>{{ $t('auth.password') }}</label>
                    <div class="password-wrapper">
                        <input :type="showPassword ? 'text' : 'password'" class="form-control" v-model="password" placeholder="Min. 8 characters" required>
                        <button type="button" class="password-toggle" @click="showPassword = !showPassword">
                            <svg v-if="!showPassword" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                            <svg v-else width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line></svg>
                        </button>
                    </div>
                </div>
                <div class="form-group">
                    <label>{{ $t('auth.how_heard') }}</label>
                    <select class="form-control" v-model="hearFrom">
                        <option value="">{{ $t('auth.how_heard_placeholder') }}</option>
                        <option value="Telegram">Telegram</option>
                        <option value="Google Play">Google Play</option>
                        <option value="Friend">Friend</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                <div class="form-group" style="margin-bottom: 8px;">
                    <label>{{ $t('auth.gpa_code') }}</label>
                    <input type="text" class="form-control" v-model="gpaCode" placeholder="GPA.XXXX-XXXX-XXXX-XXXXX" required>
                </div>
                <div class="accordion">
                    <button type="button" class="accordion-header" @click="showGpaHelp = !showGpaHelp">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
                        {{ $t('vip.where_to_find_order_code') }}
                    </button>
                    <div class="accordion-body" v-if="showGpaHelp">
                        {{ $t('vip.order_code_desc') }}
                    </div>
                </div>
                <button type="submit" class="btn-gradient btn-block" style="margin-top: 20px;" :disabled="isLoading">
                    {{ isLoading ? $t('auth.creating_account') : $t('auth.signup') }}
                </button>
            </form>
            <div class="auth-link">
                {{ $t('auth.already_have_account') }} <span @click="$router.push('/login')">{{ $t('auth.signin') }}</span>
            </div>

            <!-- Google Auth Sheet/Modal -->
            <transition name="slide-up">
                <div class="bottom-sheet-overlay" v-if="showGoogleModal" @click.self="showGoogleModal = false">
                    <div class="bottom-sheet-content" style="max-height: 90vh;">
                        <div class="bottom-sheet-header">
                            <div style="display:flex; align-items:center; gap:10px;">
                                <svg width="22" height="22" viewBox="0 0 24 24">
                                    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                                    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                                    <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/>
                                    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/>
                                </svg>
                                <h3 style="margin:0; font-size:16px;">{{ $t('auth.google_signin') }}</h3>
                            </div>
                            <button class="close-btn" @click="showGoogleModal = false">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                            </button>
                        </div>
                        <div class="bottom-sheet-body" style="padding-top:10px;">
                            <p style="font-size:13px; color:#9ca3af; margin-bottom:16px; line-height:1.5;">
                                Your Google Play account will be verified automatically for instant review.
                            </p>

                            <div class="form-group" style="margin-bottom:12px;">
                                <label style="font-size:12px; color:#cbd5e1;">{{ $t('auth.full_name') }}</label>
                                <input type="text" class="form-control" v-model="googleName" placeholder="e.g. John Doe">
                            </div>
                            <div class="form-group" style="margin-bottom:16px;">
                                <label style="font-size:12px; color:#cbd5e1;">Google Play / Gmail {{ $t('auth.email') }}</label>
                                <input type="email" class="form-control" v-model="googleEmail" placeholder="user@gmail.com" required>
                            </div>

                            <button class="btn-google btn-block" style="width:100%; justify-content:center;" @click="executeGoogleAuth" :disabled="isGoogleLoading || !googleEmail">
                                <svg width="18" height="18" viewBox="0 0 24 24">
                                    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                                    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                                    <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/>
                                    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/>
                                </svg>
                                <span>{{ isGoogleLoading ? $t('common.loading') : $t('auth.google_signin') }}</span>
                            </button>
                        </div>
                    </div>
                </div>
            </transition>
        </div>
    `,
    setup() {
        const appConfig = inject('appConfig');
        const showPassword = ref(false);
        const showGpaHelp = ref(false);
        const name = ref('');
        const email = ref('');
        const password = ref('');
        const gpaCode = ref('');
        const hearFrom = ref('');
        const isLoading = ref(false);
        const router = VueRouter.useRouter();

        const showGoogleModal = ref(false);
        const googleName = ref('');
        const googleEmail = ref('');
        const isGoogleLoading = ref(false);

        const openGoogleAuth = () => {
            triggerGoogleSignIn(router, appConfig, () => {
                googleEmail.value = '';
                googleName.value = '';
                showGoogleModal.value = true;
            });
        };

        const executeGoogleAuth = async () => {
            if (!googleEmail.value || !googleEmail.value.includes('@')) {
                showToast(window.i18n ? window.i18n.t('auth.invalid_google_email') : 'Please enter a valid Google email address.', 'error');
                return;
            }
            isGoogleLoading.value = true;
            try {
                const res = await apiCall('auth/google_auth.php', 'POST', {
                    email: googleEmail.value.trim(),
                    name: googleName.value.trim() || googleEmail.value.split('@')[0],
                    app_id: 1,
                    gpa_code: 'GOOGLE-PLAY-VERIFIED'
                });

                if (res && res.status === 'success') {
                    localStorage.setItem('acms_token_1', res.token);
                    showGoogleModal.value = false;
                    showToast(window.i18n ? window.i18n.t('common.success') : 'Success', 'success');
                    router.push('/app/home');
                } else if (res && res.status === 'banned') {
                    localStorage.setItem('acms_token_1', res.token);
                    showToast((window.i18n ? window.i18n.t('auth.account_suspended') : 'Account suspended: ') + (res.ban_reason || (window.i18n ? window.i18n.t('support.title') : 'Support')), 'error');
                    router.push('/app/home');
                } else {
                    showToast(res?.message || (window.i18n ? window.i18n.t('auth.google_signin_failed') : 'Authentication failed.'), 'error');
                }
            } catch (err) {
                showToast(window.i18n ? window.i18n.t('common.connection_error') : 'Connection error occurred.', 'error');
            } finally {
                isGoogleLoading.value = false;
            }
        };
        
        const submitForm = async () => {
            isLoading.value = true;
            try {
                const data = await apiCall('auth/register.php', 'POST', {
                    name: name.value,
                    email: email.value,
                    password: password.value,
                    gpa_code: gpaCode.value,
                    how_found: hearFrom.value,
                    app_id: 1
                });
                
                if (data && data.status === 'success') {
                    localStorage.setItem('acms_token_1', data.token);
                    router.push('/welcome');
                }
            } finally {
                isLoading.value = false;
            }
        };
        
        return { 
            showPassword, showGpaHelp, name, email, password, 
            gpaCode, hearFrom, isLoading, submitForm,
            showGoogleModal, googleName, googleEmail, isGoogleLoading,
            openGoogleAuth, executeGoogleAuth
        };
    }
};

// --- COMPONENT: Sign In Form (F1.6) ---
const Login = {
    template: `
        <div class="page-container">
            <div class="auth-header" style="margin-top: 3vh; margin-bottom: 24px;">
                <h1>{{ $t('auth.signin_title') }}</h1>
                <p style="color: #a0a0a0; font-size: 14px; margin-top: 6px;">{{ $t('auth.signin_desc') }}</p>
            </div>

            <button class="btn-google btn-block" style="margin-bottom: 16px;" @click="openGoogleAuth">
                <svg width="18" height="18" viewBox="0 0 24 24">
                    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                    <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/>
                    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/>
                </svg>
                <span>{{ $t('auth.google_signin') }}</span>
            </button>

            <div class="auth-divider"><span>{{ $t('auth.or') }}</span></div>

            <form @submit.prevent="submitForm">
                <div class="form-group">
                    <label>{{ $t('auth.email') }}</label>
                    <input type="email" class="form-control" v-model="email" placeholder="john@example.com" required>
                </div>
                <div class="form-group" style="margin-bottom: 12px;">
                    <label>{{ $t('auth.password') }}</label>
                    <div class="password-wrapper">
                        <input :type="showPassword ? 'text' : 'password'" class="form-control" v-model="password" placeholder="••••••••" required>
                        <button type="button" class="password-toggle" @click="showPassword = !showPassword">
                            <svg v-if="!showPassword" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                            <svg v-else width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line></svg>
                        </button>
                    </div>
                </div>
                <div style="text-align: right; margin-bottom: 24px;">
                    <span style="color: var(--color-primary); font-size: 13px; font-weight: 600; cursor: pointer;" @click="$router.push('/forgot-password')">{{ $t('auth.forgot_password') }}</span>
                </div>
                <button type="submit" class="btn-gradient btn-block" :disabled="isLoading">
                    {{ isLoading ? $t('auth.signing_in') : $t('auth.signin') }}
                </button>
            </form>
            <div class="auth-link">
                {{ $t('auth.dont_have_account') }} <span @click="$router.push('/register')">{{ $t('auth.signup') }}</span>
            </div>

            <!-- Google Auth Sheet/Modal -->
            <transition name="slide-up">
                <div class="bottom-sheet-overlay" v-if="showGoogleModal" @click.self="showGoogleModal = false">
                    <div class="bottom-sheet-content" style="max-height: 90vh;">
                        <div class="bottom-sheet-header">
                            <div style="display:flex; align-items:center; gap:10px;">
                                <svg width="22" height="22" viewBox="0 0 24 24">
                                    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                                    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                                    <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/>
                                    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/>
                                </svg>
                                <h3 style="margin:0; font-size:16px;">{{ $t('auth.google_signin') }}</h3>
                            </div>
                            <button class="close-btn" @click="showGoogleModal = false">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                            </button>
                        </div>
                        <div class="bottom-sheet-body" style="padding-top:10px;">
                            <p style="font-size:13px; color:#9ca3af; margin-bottom:16px; line-height:1.5;">
                                Your Google Play account will be verified automatically for instant review.
                            </p>

                            <div class="form-group" style="margin-bottom:12px;">
                                <label style="font-size:12px; color:#cbd5e1;">{{ $t('auth.full_name') }}</label>
                                <input type="text" class="form-control" v-model="googleName" placeholder="e.g. John Doe">
                            </div>
                            <div class="form-group" style="margin-bottom:16px;">
                                <label style="font-size:12px; color:#cbd5e1;">Google Play / Gmail {{ $t('auth.email') }}</label>
                                <input type="email" class="form-control" v-model="googleEmail" placeholder="user@gmail.com" required>
                            </div>

                            <button class="btn-google btn-block" style="width:100%; justify-content:center;" @click="executeGoogleAuth" :disabled="isGoogleLoading || !googleEmail">
                                <svg width="18" height="18" viewBox="0 0 24 24">
                                    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                                    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                                    <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/>
                                    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/>
                                </svg>
                                <span>{{ isGoogleLoading ? $t('common.loading') : $t('auth.google_signin') }}</span>
                            </button>
                        </div>
                    </div>
                </div>
            </transition>
        </div>
    `,
    setup() {
        const appConfig = inject('appConfig');
        const showPassword = ref(false);
        const email = ref('');
        const password = ref('');
        const isLoading = ref(false);
        const router = VueRouter.useRouter();

        const showGoogleModal = ref(false);
        const googleName = ref('');
        const googleEmail = ref('');
        const isGoogleLoading = ref(false);

        const openGoogleAuth = () => {
            triggerGoogleSignIn(router, appConfig, () => {
                googleEmail.value = '';
                googleName.value = '';
                showGoogleModal.value = true;
            });
        };

        const executeGoogleAuth = async () => {
            if (!googleEmail.value || !googleEmail.value.includes('@')) {
                showToast(window.i18n ? window.i18n.t('auth.invalid_google_email') : 'Please enter a valid Google email address.', 'error');
                return;
            }
            isGoogleLoading.value = true;
            try {
                const res = await apiCall('auth/google_auth.php', 'POST', {
                    email: googleEmail.value.trim(),
                    name: googleName.value.trim() || googleEmail.value.split('@')[0],
                    app_id: 1,
                    gpa_code: 'GOOGLE-PLAY-VERIFIED'
                });

                if (res && res.status === 'success') {
                    localStorage.setItem('acms_token_1', res.token);
                    showGoogleModal.value = false;
                    showToast(window.i18n ? window.i18n.t('common.success') : 'Success', 'success');
                    router.push('/app/home');
                } else if (res && res.status === 'banned') {
                    localStorage.setItem('acms_token_1', res.token);
                    showToast((window.i18n ? window.i18n.t('auth.account_suspended') : 'Account suspended: ') + (res.ban_reason || (window.i18n ? window.i18n.t('support.title') : 'Support')), 'error');
                    router.push('/app/home');
                } else {
                    showToast(res?.message || (window.i18n ? window.i18n.t('auth.google_signin_failed') : 'Authentication failed.'), 'error');
                }
            } catch (err) {
                showToast(window.i18n ? window.i18n.t('common.connection_error') : 'Connection error occurred.', 'error');
            } finally {
                isGoogleLoading.value = false;
            }
        };
        
        const submitForm = async () => {
            isLoading.value = true;
            try {
                const data = await apiCall('auth/login.php', 'POST', { 
                    email: email.value, 
                    password: password.value, 
                    app_id: 1 
                });
                
                if (data && data.status === 'success') {
                    localStorage.setItem('acms_token_1', data.token);
                    router.push('/app/home');
                } else if (data && data.status === 'banned') {
                    localStorage.setItem('acms_token_1', data.token);
                    showToast('Your account is banned: ' + (data.ban_reason || 'Contact support.'), 'error');
                    router.push('/app/home');
                    return;
                }
            } finally {
                isLoading.value = false;
            }
        };
        
        return { 
            showPassword, email, password, isLoading, submitForm,
            showGoogleModal, googleName, googleEmail, isGoogleLoading,
            openGoogleAuth, executeGoogleAuth
        };
    }
};

// --- COMPONENT: Forgot Password (F1.7) ---
const ForgotPassword = {
    template: `
        <div class="page-container">
            <div class="auth-header" style="margin-top: 4vh; margin-bottom: 40px;">
                <h1>{{ $t('auth.forgot_password') }}</h1>
                <p style="color: #a0a0a0; font-size: 14px; margin-top: 8px;">{{ step === 1 ? 'Enter your email to receive a reset code' : 'Enter the code and your new password' }}</p>
            </div>
            <form @submit.prevent="submitForm">
                <template v-if="step === 1">
                    <div class="form-group">
                        <label>{{ $t('auth.email') }}</label>
                        <input type="email" class="form-control" v-model="email" placeholder="john@example.com" required>
                    </div>
                    <button type="submit" class="btn-gradient btn-block" style="margin-top: 16px;" :disabled="isLoading">
                        {{ isLoading ? $t('common.saving') : $t('auth.reset_password') }}
                    </button>
                </template>
                <template v-else>
                    <div style="text-align: center; margin-bottom: 20px;">
                        <span style="font-size: 14px; color: var(--color-primary); font-weight: 600;">Time remaining: {{ formatTime(timeLeft) }}</span>
                    </div>
                    <div class="form-group">
                        <label>OTP Code</label>
                        <input type="text" class="form-control" v-model="otpCode" placeholder="6-digit code" required>
                    </div>
                    <div class="form-group">
                        <label>{{ $t('auth.new_password') }}</label>
                        <div class="password-wrapper">
                            <input :type="showPassword ? 'text' : 'password'" class="form-control" v-model="newPassword" placeholder="Min. 8 characters" required>
                            <button type="button" class="password-toggle" @click="showPassword = !showPassword">
                                <svg v-if="!showPassword" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                <svg v-else width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line></svg>
                            </button>
                        </div>
                    </div>
                    <button type="submit" class="btn-gradient btn-block" style="margin-top: 16px;" :disabled="isLoading">
                        {{ isLoading ? $t('common.saving') : $t('auth.reset_password') }}
                    </button>
                </template>
            </form>
            <div class="auth-link">
                <span @click="$router.push('/login')">{{ $t('auth.signin') }}</span>
            </div>
        </div>
    `,
    setup() {
        const step = ref(1);
        const email = ref('');
        const otpCode = ref('');
        const newPassword = ref('');
        const showPassword = ref(false);
        const isLoading = ref(false);
        const router = VueRouter.useRouter();
        
        const timeLeft = ref(15 * 60);
        let timer = null;

        const startTimer = () => {
            timeLeft.value = 15 * 60;
            if (timer) clearInterval(timer);
            timer = setInterval(() => {
                if (timeLeft.value > 0) timeLeft.value--;
                else clearInterval(timer);
            }, 1000);
        };

        const formatTime = (seconds) => {
            const m = Math.floor(seconds / 60).toString().padStart(2, '0');
            const s = (seconds % 60).toString().padStart(2, '0');
            return `${m}:${s}`;
        };

        onUnmounted(() => {
            if (timer) clearInterval(timer);
        });

        const submitForm = async () => {
            isLoading.value = true;
            try {
                if (step.value === 1) {
                    const data = await apiCall('auth/forgot_password.php', 'POST', { email: email.value, app_id: 1 });
                    if (data && data.status === 'success') {
                        showToast(window.i18n ? window.i18n.t('auth.code_sent_email') : "Check your email for the code.", "success");
                        step.value = 2;
                        startTimer();
                    }
                } else {
                    const data = await apiCall('auth/reset_password.php', 'POST', { 
                        email: email.value, 
                        otp_code: otpCode.value, 
                        new_password: newPassword.value, 
                        app_id: 1 
                    });
                    if (data && data.status === 'success') {
                        showToast(window.i18n ? window.i18n.t('auth.password_reset_success') : "Password reset! Please login.", "success");
                        router.push('/login');
                    }
                }
            } finally {
                isLoading.value = false;
            }
        };
        return { step, email, otpCode, newPassword, showPassword, isLoading, submitForm, timeLeft, formatTime };
    }
};

// --- COMPONENT: Welcome (F1.5) ---
const Welcome = {
    template: `
        <div class="welcome-container">
            <div class="welcome-icon">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"></polyline></svg>
            </div>
            <h1 class="welcome-title">{{ $t('onboarding.step3_title') }}</h1>
            <p class="welcome-desc">{{ $t('vip.vip_locked_desc') }}</p>
            <div class="contact-links">
                <a :href="appConfig?.social?.telegram || '#'" class="contact-link" v-if="appConfig?.social?.telegram">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="22" y1="2" x2="11" y2="13"></line><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg>
                </a>
                <a href="#" class="contact-link">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                </a>
            </div>
            <button class="btn-gradient btn-block" style="margin-top: auto;" @click="$router.push('/app/home')">{{ $t('common.confirm') }}</button>
        </div>
    `,
    setup() {
        const appConfig = inject('appConfig');
        return { appConfig };
    }
};

// --- COMPONENT: App Layout (Main Shell + Notifications + Promo + RateUs + Network Alert) ---
const AppLayout = {
    template: `
        <div class="app-layout" @click="closeNotifs">
            <!-- NETWORK STATUS ALERT BANNER (F4.4) -->
            <transition name="slide-down">
                <div v-if="!networkState.isOnline" class="network-offline-banner">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="1" y1="1" x2="23" y2="23"></line><path d="M16.72 11.06A10.94 10.94 0 0 1 19 12.55"></path><path d="M5 12.55a10.94 10.94 0 0 1 5.17-2.39"></path><path d="M10.71 5.05A16 16 0 0 1 22.58 9"></path><path d="M1.42 9a15.91 15.91 0 0 1 4.7-2.88"></path><path d="M8.53 16.11a6 6 0 0 1 6.95 0"></path><line x1="12" y1="20" x2="12.01" y2="20"></line></svg>
                    <span>No Internet Connection • Showing cached data</span>
                </div>
            </transition>
            <transition name="slide-down">
                <div v-if="networkState.showReconnected" class="network-online-banner">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12.55a11 11 0 0 1 14.08 0"></path><path d="M1.42 9a16 16 0 0 1 21.16 0"></path><path d="M8.53 16.11a6 6 0 0 1 6.95 0"></path><line x1="12" y1="20" x2="12.01" y2="20"></line></svg>
                    <span>Internet Connection Restored</span>
                </div>
            </transition>

            <header class="app-header">
                <div class="app-header-logo">
                    <img :src="appConfig?.logo_url" alt="Logo" v-if="appConfig?.logo_url">
                    <h2 :style="{ color: 'var(--color-accent, #ffcc00)' }">{{ appConfig?.app_name || 'App' }}</h2>
                </div>
                
                <button class="header-action" @click.stop="toggleNotifs">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg>
                    <div class="badge" v-if="hasUnreadNotifs"></div>
                </button>
                
                <!-- NOTIFICATION INBOX (F2.3) -->
                <div class="notif-dropdown" v-if="showNotifs" @click.stop>
                    <div class="notif-header">Notifications</div>
                    <div class="notif-list" v-if="notifications.length > 0">
                        <div class="notif-item" v-for="(n, i) in notifications" :key="i">
                            <div class="notif-icon">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                            </div>
                            <div class="notif-content">
                                <p>{{ n.text }}</p>
                                <span class="notif-time">{{ n.time }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="notif-empty" v-else>
                        No notifications yet
                    </div>
                </div>
            </header>
            
            <router-view v-slot="{ Component }">
                <transition name="fade" mode="out-in">
                    <component :is="Component" />
                </transition>
            </router-view>
            
            <!-- PROMO BANNER (F2.5) -->
            <div class="promo-banner-container" v-if="showPromo">
                <div class="promo-banner" @click="goToVipHub">
                    <div class="promo-content">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink:0;"><path d="M4.5 16.5c-1.5 1.26-2 5-2 5s3.74-.5 5-2c.71-.84.7-2.13-.09-2.91a2.18 2.18 0 0 0-2.91-.09z"></path><path d="m12 15-3-3a22 22 0 0 1 2-3.95A12.88 12.88 0 0 1 22 2c0 2.72-.78 7.5-6 11a22.35 22.35 0 0 1-4 2z"></path><path d="M9 12H4s.55-3.03 2-4c1.62-1.08 5 0 5 0"></path><path d="M12 15v5s3.03-.55 4-2c1.08-1.62 0-5 0-5"></path></svg>
                        <div class="promo-text">{{ activePromo?.badge_text || activePromo?.title }}</div>
                    </div>
                    <button class="promo-close" @click.stop="closePromo">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                    </button>
                </div>
            </div>
            
            <!-- RATE US MODAL -->
            <div v-if="showRateModal" class="modal-overlay" style="position:fixed; top:0; left:0; right:0; bottom:0; background:rgba(0,0,0,0.85); backdrop-filter:blur(8px); z-index:9999; display:flex; align-items:center; justify-content:center; padding:20px;" @click.self="rateLater">
                <div class="modal-content" style="width:100%; max-width:340px; background:rgba(13,20,36,0.95); border:1px solid rgba(255,255,255,0.12); border-radius:24px; padding:24px; text-align:center; box-shadow:0 20px 50px rgba(0,0,0,0.6);">
                    
                    <!-- AŞAMA 1: Yıldızlar ve Yönlendirme -->
                    <template v-if="rateStep === 1">
                        <div style="display:flex; justify-content:center; gap:6px; margin-bottom:16px; cursor:pointer;" @click="rateNow">
                            <svg v-for="i in 5" :key="i" width="28" height="28" viewBox="0 0 24 24" fill="#ffcc00" stroke="#ffcc00" stroke-width="1.5">
                                <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                            </svg>
                        </div>
                        <h3 style="margin-bottom:8px; font-size:18px; color:#fff;">{{ appConfig?.rate_us?.title || 'Enjoying the app?' }}</h3>
                        <p style="font-size:13px; color:#94a3b8; margin-bottom:20px; line-height:1.5;">
                            {{ appConfig?.rate_us?.text || 'Tap 5 stars on Google Play to unlock special bonuses!' }}
                        </p>
                        <div v-if="appConfig?.rate_us?.reward"
                             style="font-size:12px; color:var(--color-accent, #ffcc00); font-weight:600; margin-bottom:18px; background:rgba(255,204,0,0.1); padding:8px 12px; border-radius:10px; border:1px solid rgba(255,204,0,0.2);">
                            🎁 {{ appConfig.rate_us.reward }}
                        </div>
                        <div style="display:flex; flex-direction:column; gap:10px;">
                            <button class="btn-gradient btn-block" style="display:flex; align-items:center; justify-content:center; gap:8px;" @click="rateNow">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="#fff"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                                <span>{{ appConfig?.rate_us?.rate_btn_text || 'Rate Us 5 Stars' }}</span>
                            </button>
                            <button class="btn-outline btn-block" style="border:1px solid rgba(255,255,255,0.12); font-size:13px; color:#cbd5e1;" @click="rateLater">
                                {{ appConfig?.rate_us?.later_btn_text || 'Remind me later' }}
                            </button>
                            <button style="background:none; border:none; color:#64748b; font-size:12px; cursor:pointer; padding:6px; display:flex; align-items:center; justify-content:center; gap:4px;" @click="rateAlreadyDone">
                                <span>I already rated</span>
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                            </button>
                        </div>
                    </template>
                    
                    <!-- AŞAMA 2: Teşekkür & Tüm Aktif İletişim Kanalları Grid Butonları -->
                    <template v-if="rateStep === 2">
                        <div style="display:flex; justify-content:center; margin-bottom:14px;">
                            <div style="width:60px; height:60px; border-radius:50%; background:rgba(34,197,94,0.15); display:flex; align-items:center; justify-content:center; border:1px solid rgba(34,197,94,0.3);">
                                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#22c55e" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                            </div>
                        </div>
                        <h3 style="margin-bottom:8px; font-size:18px; color:#fff;">{{ appConfig?.rate_us?.step2_title || 'Thanks for your support!' }}</h3>
                        <p style="font-size:13px; color:#94a3b8; margin-bottom:18px; line-height:1.5;">
                            {{ appConfig?.rate_us?.step2_text || 'Send us a screenshot of your 5-star review to claim your VIP access gift!' }}
                        </p>
                        
                        <!-- İletişim Kanalları Grid -->
                        <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(120px, 1fr)); gap:8px; margin-bottom:12px;">
                            <!-- E-posta -->
                            <a v-if="appConfig?.contact?.email" :href="'mailto:' + appConfig.contact.email" class="btn-gradient" style="text-decoration:none; display:flex; align-items:center; justify-content:center; gap:6px; padding:10px 8px; font-size:12px; border-radius:12px;">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                                <span>Email</span>
                            </a>
                            
                            <!-- Telegram -->
                            <a v-if="appConfig?.contact?.telegram" :href="formatTelegramUrl(appConfig.contact.telegram)" target="_blank" class="btn-outline" style="text-decoration:none; display:flex; align-items:center; justify-content:center; gap:6px; padding:10px 8px; font-size:12px; border-radius:12px; border:1px solid rgba(255,255,255,0.15); color:#fff; background:rgba(255,255,255,0.05);">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#0088cc" stroke-width="2"><line x1="22" y1="2" x2="11" y2="13"></line><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg>
                                <span>Telegram</span>
                            </a>
                            
                            <!-- WhatsApp -->
                            <a v-if="appConfig?.contact?.whatsapp" :href="formatWhatsappUrl(appConfig.contact.whatsapp)" target="_blank" class="btn-outline" style="text-decoration:none; display:flex; align-items:center; justify-content:center; gap:6px; padding:10px 8px; font-size:12px; border-radius:12px; border:1px solid rgba(255,255,255,0.15); color:#fff; background:rgba(255,255,255,0.05);">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#25D366" stroke-width="2"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path></svg>
                                <span>WhatsApp</span>
                            </a>
                            
                            <!-- Instagram -->
                            <a v-if="appConfig?.contact?.instagram" :href="formatInstagramUrl(appConfig.contact.instagram)" target="_blank" class="btn-outline" style="text-decoration:none; display:flex; align-items:center; justify-content:center; gap:6px; padding:10px 8px; font-size:12px; border-radius:12px; border:1px solid rgba(255,255,255,0.15); color:#fff; background:rgba(255,255,255,0.05);">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#E1306C" stroke-width="2"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line></svg>
                                <span>Instagram</span>
                            </a>
                        </div>
                        
                        <button class="btn-outline btn-block" style="border:none; color:#94a3b8; font-size:13px; margin-top:4px;" @click="closeRateModal">
                            {{ appConfig?.rate_us?.step2_done_btn || 'Done ✓' }}
                        </button>
                    </template>
                    
                </div>
            </div>

            <!-- BOTTOM NAVIGATION BAR (F1.9) -->
            <nav class="bottom-nav">
                <div class="nav-item" :class="{ active: currentPath === '/app/home' }" @click="navigateNav('/app/home')" @touchstart.passive="triggerNavHaptic">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
                    <span>{{ $t('nav.home') }}</span>
                </div>
                <div class="nav-item" :class="{ active: currentPath === '/app/tips' }" @click="navigateNav('/app/tips')" @touchstart.passive="triggerNavHaptic">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                    <span>{{ $t('nav.tips') }}</span>
                </div>
                <div class="nav-item" :class="{ active: currentPath === '/app/vip-hub' }" @click="navigateNav('/app/vip-hub')" @touchstart.passive="triggerNavHaptic">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                    <span>{{ $t('nav.vip_hub') }}</span>
                </div>
                <div class="nav-item" :class="{ active: currentPath === '/app/profile' }" @click="navigateNav('/app/profile')" @touchstart.passive="triggerNavHaptic">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                    <span>{{ $t('nav.profile') }}</span>
                </div>
            </nav>
        </div>
    `,
    setup() {
        const appConfig = inject('appConfig');
        const router = VueRouter.useRouter();
        
        // --- ROUTING & BOTTOM NAV ---
        const currentPath = computed(() => router.currentRoute.value?.path || '/app/home');
        
        const triggerNavHaptic = () => {
            Native.haptic('light');
        };

        const navigateNav = (path) => {
            triggerNavHaptic();
            if (router.currentRoute.value?.path !== path) {
                router.push(path);
            }
        };

        // --- NETWORK EVENT LISTENERS ---
        const handleOnline = () => {
            networkState.isOnline = true;
            networkState.showReconnected = true;
            Native.haptic('medium');
            setTimeout(() => {
                networkState.showReconnected = false;
            }, 3000);
        };

        const handleOffline = () => {
            networkState.isOnline = false;
            Native.haptic('heavy');
        };

        window.addEventListener('online', handleOnline);
        window.addEventListener('offline', handleOffline);

        const showRateModal = ref(false);
        const rateStep = ref(1);
        
        // Promo
        const activePromo = ref(null);
        const promoDismissed = ref(false);
        const showPromo = computed(() => activePromo.value !== null && !promoDismissed.value);
        
        const closePromo = () => {
            promoDismissed.value = true;
            if (activePromo.value && activePromo.value.id) {
                localStorage.setItem(`promo_dismissed_${activePromo.value.id}`, Date.now());
            }
        };
        const goToVipHub = () => {
            Native.haptic('light');
            router.push('/app/vip-hub');
        };
        
        onMounted(async () => {
            const promoData = await apiCall('promotions.php?app_id=1');
            if (promoData && promoData.status === 'success' && promoData.data) {
                const promo = promoData.data;
                activePromo.value = promo;
                
                const dismissedTime = localStorage.getItem(`promo_dismissed_${promo.id}`);
                if (dismissedTime) {
                    const diffHours = (Date.now() - parseInt(dismissedTime, 10)) / (1000 * 60 * 60);
                    if (diffHours < 48) { // 2 days
                        promoDismissed.value = true;
                    }
                }
            }

            const heartbeatInterval = setInterval(async () => {
                const token = localStorage.getItem('acms_token_1');
                if (!token) {
                    clearInterval(heartbeatInterval);
                    return;
                }
                const data = await apiCall('auth/verify.php', 'POST', { token, app_id: 1 });
                
                if (data === null) {
                    clearInterval(heartbeatInterval);
                    setTimeout(() => router.push('/'), 100);
                    return;
                }
                
                if (data.user && data.user.exempt_security === 1) {
                    localStorage.setItem('acms_exempt_security_' + (appConfig.value?.app_id || '1'), '1');
                } else {
                    localStorage.removeItem('acms_exempt_security_' + (appConfig.value?.app_id || '1'));
                }

                if (data.user && data.user.exempt_screenshot === 1) {
                    localStorage.setItem('acms_exempt_screenshot_' + (appConfig.value?.app_id || '1'), '1');
                } else {
                    localStorage.removeItem('acms_exempt_screenshot_' + (appConfig.value?.app_id || '1'));
                }
                
                // Apply dynamic screenshot protection
                applyScreenSecurity(router.currentRoute.value?.path, appConfig.value);

                if (data.status === 'banned') {
                    clearInterval(heartbeatInterval);
                }
                if (data.maintenance === true) {
                    showToast(window.i18n ? window.i18n.t('common.maintenance_msg') : 'System is temporarily under maintenance.', 'error');
                }
            }, 5 * 60 * 1000); // 5 dakika

            onUnmounted(() => {
                clearInterval(heartbeatInterval);
                window.removeEventListener('online', handleOnline);
                window.removeEventListener('offline', handleOffline);
            });
            
            // Rate Us tetikleme mantığı
            const checkRateUs = () => {
                const ru = appConfig.value?.rate_us;
                if (!ru?.active) return;
                
                const playLink = ru?.play_store_link || appConfig.value?.play_store_link;
                if (!playLink) return;
                
                const token = localStorage.getItem('acms_token_1');
                if (!token) return;
                
                const appId = appConfig.value?.app_id || '1';
                const exemptKey = 'acms_exempt_security_' + appId;
                if (localStorage.getItem(exemptKey) === '1') return;
                
                const statusKey = 'acms_rate_status_' + appId;
                if (localStorage.getItem(statusKey) === 'done') return;
                
                const snoozeKey = 'acms_rate_snooze_' + appId;
                const snoozeUntil = localStorage.getItem(snoozeKey);
                if (snoozeUntil && Date.now() < parseInt(snoozeUntil, 10)) return;
                
                const sessionKey = 'acms_rate_sessions_' + appId;
                let sessions = parseInt(localStorage.getItem(sessionKey) || '0', 10) + 1;
                localStorage.setItem(sessionKey, sessions.toString());
                
                if (sessions >= 3) {
                    setTimeout(() => {
                        showRateModal.value = true;
                        rateStep.value = 1;
                    }, 4000);
                }
            };
            
            router.afterEach((to) => {
                if (to.path === '/app/tips') {
                    checkRateUs();
                }
            });
        });
        
        // Notifications
        const showNotifs = ref(false);
        const hasUnreadNotifs = ref(true);
        const notifications = ref([
            { text: "Your GPA code was approved! Welcome to VIP.", time: "2 hours ago" },
            { text: "New expert picks are available for today.", time: "4 hours ago" }
        ]);
        const toggleNotifs = () => {
            Native.haptic('light');
            showNotifs.value = !showNotifs.value;
            if (showNotifs.value) hasUnreadNotifs.value = false;
        };
        const closeNotifs = () => showNotifs.value = false;
        
        // Rate Us Actions
        const rateNow = () => {
            Native.haptic('medium');
            Native.requestInAppReview(); // Launch native Google Play in-app review
            rateStep.value = 2;
            const link = appConfig.value?.play_store_link || '';
            if (link && (!window.AndroidBridge && !window.Capacitor?.Plugins?.AppNative)) {
                window.open(link, '_blank');
            }
            const appId = appConfig.value?.app_id || '1';
            localStorage.setItem('acms_rate_status_' + appId, 'done');
        };

        const rateLater = () => {
            Native.haptic('light');
            const appId = appConfig.value?.app_id || '1';
            const days = appConfig.value?.rate_us?.snooze_days || 3;
            localStorage.setItem('acms_rate_snooze_' + appId, (Date.now() + days * 24 * 60 * 60 * 1000).toString());
            localStorage.setItem('acms_rate_sessions_' + appId, '0');
            showRateModal.value = false;
            rateStep.value = 1;
        };

        const rateAlreadyDone = () => {
            Native.haptic('light');
            const appId = appConfig.value?.app_id || '1';
            localStorage.setItem('acms_rate_status_' + appId, 'done');
            showRateModal.value = false;
            rateStep.value = 1;
        };

        const closeRateModal = () => {
            Native.haptic('light');
            showRateModal.value = false;
            rateStep.value = 1;
        };

        // URL Biçimlendirme Fonksiyonları
        const formatTelegramUrl = (tg) => {
            if (!tg) return '#';
            const clean = tg.trim();
            if (clean.startsWith('http://') || clean.startsWith('https://')) return clean;
            return 'https://t.me/' + clean.replace('@', '');
        };
        const formatWhatsappUrl = (wa) => {
            if (!wa) return '#';
            const clean = wa.trim();
            if (clean.startsWith('http://') || clean.startsWith('https://')) return clean;
            return 'https://wa.me/' + clean.replace(/[^0-9]/g, '');
        };
        const formatInstagramUrl = (ig) => {
            if (!ig) return '#';
            const clean = ig.trim();
            if (clean.startsWith('http://') || clean.startsWith('https://')) return clean;
            return 'https://instagram.com/' + clean.replace('@', '');
        };

        // Rate Us Canlı Önizleme Event Dinleyicisi
        const handlePreviewRateUs = () => {
            showRateModal.value = true;
            rateStep.value = 1;
        };
        window.addEventListener('acms-trigger-rate-us', handlePreviewRateUs);
        onUnmounted(() => window.removeEventListener('acms-trigger-rate-us', handlePreviewRateUs));

        return { 
            appConfig, networkState, currentPath, triggerNavHaptic, navigateNav,
            activePromo, showPromo, closePromo, goToVipHub,
            showNotifs, toggleNotifs, closeNotifs, hasUnreadNotifs, notifications,
            showRateModal, rateStep, rateNow, rateLater, rateAlreadyDone, closeRateModal,
            formatTelegramUrl, formatWhatsappUrl, formatInstagramUrl
        };
    }
};

// --- COMPONENT: Home Page (F1.9) ---
const Home = {
    template: `
        <div class="main-content" style="position: relative; min-height: 500px;">
            <transition name="fade">
                <div v-if="isLoading" key="loading" style="display:flex; justify-content:center; align-items:center; padding:60px 20px;">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="var(--color-primary, #6366f1)" stroke-width="2" style="animation: spin 1s linear infinite;"><line x1="12" y1="2" x2="12" y2="6"></line><line x1="12" y1="18" x2="12" y2="22"></line><line x1="4.93" y1="4.93" x2="7.76" y2="7.76"></line><line x1="16.24" y1="16.24" x2="19.07" y2="19.07"></line><line x1="2" y1="12" x2="6" y2="12"></line><line x1="18" y1="12" x2="22" y2="12"></line><line x1="4.93" y1="19.07" x2="7.76" y2="16.24"></line><line x1="16.24" y1="7.76" x2="19.07" y2="4.93"></line></svg>
                </div>
                
                <div v-else key="content">
                    <!-- PENDING APPROVAL NOTICE BANNER -->
                    <div v-if="userStatus === 'pending'" class="pending-banner">
                        <div style="font-size: 22px; line-height: 1;">⏳</div>
                        <div style="flex: 1;">
                            <div style="font-weight: 700; font-size: 13px; color: #fbbf24; margin-bottom: 2px;">{{ $t('vip.vip_locked_title') }}</div>
                            <div style="font-size: 12px; color: #cbd5e1; line-height: 1.4;">{{ $t('vip.vip_locked_desc') }}</div>
                        </div>
                    </div>

                    <!-- MODUL 1: Announcements -->
                    <div class="glass-module" v-if="announcement">
                        <div class="module-header">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--color-primary)" stroke-width="2"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path></svg>
                            <h3>{{ $t('home.latest_news') }}</h3>
                        </div>
                        <div class="module-content">
                            <div class="announcement-title">{{ $t('home.announcement') }}</div>
                            <p style="font-size: 13px; color: #a0a0a0; line-height: 1.4;">{{ announcement }}</p>
                        </div>
                    </div>

                <!-- MODUL 2: Countdown -->
                <div class="glass-module" style="margin-top: 20px;">
                    <div class="module-header">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--color-primary)" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                        <h3>{{ $t('home.next_match_in') }}</h3>
                    </div>
                    <div class="module-content" style="text-align: center; padding: 24px 16px;">
                        <div class="countdown-time" v-if="countdownStr" style="margin: 0;">{{ countdownStr }}</div>
                        <div class="countdown-time" v-else style="font-size: 16px; margin: 0; font-weight: 500;">{{ $t('home.matches_in_progress') }}</div>
                    </div>
                </div>

                <!-- MODUL 3: Teaser -->
                <div class="glass-module" v-if="topPick" style="margin-top: 20px;">
                    <div class="module-header">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--color-primary)" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                        <h3>{{ $t('home.expert_pick') }}</h3>
                    </div>
                    <div class="module-content" style="padding: 12px;">
                        <div class="locked-card" style="background: transparent; padding: 0; border: none;">
                        <div class="locked-overlay" v-if="!isApproved" style="border-radius: 12px; background: rgba(0,0,0,0.6);">
                            <template v-if="userStatus === 'banned'">
                                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                                <span style="color: #ef4444; font-weight:600;">{{ $t('status.banned') }}</span>
                            </template>
                            <template v-else>
                                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                                <span>{{ $t('home.expert_pick') }}</span>
                            </template>
                        </div>
                        <div :class="isApproved ? '' : 'locked-content'" :style="isApproved ? '' : 'filter: blur(4px);'">
                            <div class="mc" :class="'match-' + topPick.status">
                                <div class="mc-head">
                                    <div class="mc-league">
                                        <img :src="topPick.league_logo || 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7'" class="mc-league-img" @error="$event.target.src='data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7'">
                                        <span>{{ topPick.league }}</span>
                                    </div>
                                    <span class="mc-time" v-if="topPick.status === 'postponed'" style="color:#ef4444;">{{ $t('status.postponed') }}</span>
                                    <span class="mc-time" v-else>{{ formatTime(topPick.match_time) }}<span v-if="topPick.status !== 'pending'" class="mc-ft"> FT</span></span>
                                </div>
                                <div class="mc-body">
                                    <div class="mc-home">
                                        <img :src="topPick.home_logo || 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7'" class="mc-logo" @error="$event.target.src='data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7'">
                                        <span class="mc-tname">{{ topPick.home_team }}</span>
                                    </div>
                                    <div class="mc-vs" :class="topPick.status === 'win' ? 'mc-vs-win' : (topPick.status === 'lose' ? 'mc-vs-lose' : '')">
                                        {{ topPick.status !== 'pending' && topPick.status !== 'postponed' && topPick.score ? topPick.score : (topPick.status === 'postponed' ? '--' : 'VS') }}
                                    </div>
                                    <div class="mc-away">
                                        <span class="mc-tname">{{ topPick.away_team }}</span>
                                        <img :src="topPick.away_logo || 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7'" class="mc-logo" @error="$event.target.src='data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7'">
                                    </div>
                                </div>
                                <div class="mc-stats">
                                    <span class="mc-pct">{{ topPick.confidence_rate ? topPick.confidence_rate + '%' : '-' }}</span>
                                    <span class="mc-pred prediction-val" v-if="topPick.prediction">{{ $translatePick(topPick.prediction) }}</span>
                                    <span class="mc-pred" v-else style="display:inline-flex; align-items:center; justify-content:center; gap:4px;"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>VIP</span>
                                    <span class="mc-odds" v-if="topPick.odds">{{ parseFloat(topPick.odds).toFixed(2) }}</span>
                                    <span class="mc-odds" v-else><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- MODUL 4: Top 5 Win -->
                <div class="glass-module" style="margin-top: 20px;">
                    <div class="module-header">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--color-primary)" stroke-width="2"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline></svg>
                        <h3>{{ $t('home.top_winners') }}</h3>
                    </div>
                    <div class="module-content" style="padding: 12px;">
                        <div class="locked-card" style="background: transparent; padding: 0; border: none;">
                        <div class="locked-overlay" v-if="!isApproved" style="border-radius: 12px; background: rgba(0,0,0,0.6);">
                            <template v-if="userStatus === 'banned'">
                                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                                <span style="color: #ef4444; font-weight:600;">{{ $t('status.banned') }}</span>
                            </template>
                            <template v-else>
                                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                                <span>{{ $t('home.unlock_history') }}</span>
                            </template>
                        </div>
                        <div :class="isApproved ? '' : 'locked-content'" :style="isApproved ? '' : 'filter: blur(4px);'">
                            <template v-if="topWinners.length > 0">
                                <div v-for="match in topWinners" :key="match.id" class="mc match-won">
                                    <div class="mc-head">
                                        <div class="mc-league">
                                            <img :src="match.league_logo || 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7'" class="mc-league-img" @error="$event.target.src='data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7'">
                                            <span>{{ match.league }}</span>
                                        </div>
                                        <span class="mc-time">{{ formatDate(match.match_time) }} {{ formatTime(match.match_time) }}<span class="mc-ft"> FT</span></span>
                                    </div>
                                    <div class="mc-body">
                                        <div class="mc-home">
                                            <img :src="match.home_logo || 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7'" class="mc-logo" @error="$event.target.src='data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7'">
                                            <span class="mc-tname">{{ match.home_team }}</span>
                                        </div>
                                        <div class="mc-vs mc-vs-win">
                                            {{ match.score || 'VS' }}
                                        </div>
                                        <div class="mc-away">
                                            <span class="mc-tname">{{ match.away_team }}</span>
                                            <img :src="match.away_logo || 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7'" class="mc-logo" @error="$event.target.src='data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7'">
                                        </div>
                                    </div>
                                    <div class="mc-stats">
                                        <span class="mc-pct">{{ match.confidence_rate ? match.confidence_rate + '%' : '-' }}</span>
                                        <span class="mc-pred prediction-val" v-if="match.prediction">{{ $translatePick(match.prediction) }}</span>
                                        <span class="mc-pred" v-else style="display:inline-flex; align-items:center; justify-content:center; gap:4px;"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>VIP</span>
                                        <span class="mc-odds" v-if="match.odds">{{ parseFloat(match.odds).toFixed(2) }}</span>
                                        <span class="mc-odds" v-else><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg></span>
                                    </div>
                                </div>
                            </template>
                            <div v-else style="text-align:center; padding: 20px; color: #a0a0a0; font-size: 13px;">
                                {{ $t('home.no_winners_yet') }}
                            </div>
                        </div>
                    </div>
                </div>
            </transition>

            <!-- WELCOME / ANNOUNCEMENT MODAL -->
            <div v-if="showWelcomeModal" class="modal-overlay" style="position:fixed; top:0; left:0; right:0; bottom:0; background:rgba(0,0,0,0.85); backdrop-filter:blur(8px); z-index:9998; display:flex; align-items:center; justify-content:center; padding:20px;">
                <div class="modal-content" style="width:100%; max-width:340px; background:rgba(13,20,36,0.96); border:1px solid rgba(255,255,255,0.12); border-radius:24px; padding:24px; text-align:center; box-shadow:0 20px 50px rgba(0,0,0,0.6);">
                    <div style="display:flex; justify-content:center; margin-bottom:12px;">
                        <div style="width:52px; height:52px; border-radius:50%; background:rgba(99,102,241,0.15); display:flex; align-items:center; justify-content:center; border:1px solid rgba(99,102,241,0.3);">
                            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="var(--color-primary, #6366f1)" stroke-width="2"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg>
                        </div>
                    </div>
                    <h3 style="color:#fff; font-size:18px; margin-bottom:8px;">{{ appConfig?.announcement_modal?.title || $t('home.important_notice') }}</h3>
                    <p style="color:#94a3b8; font-size:13px; line-height:1.6; margin-bottom:20px;">{{ appConfig?.announcement_modal?.text }}</p>
                    <button class="btn-gradient btn-block" @click="closeWelcomeModal">{{ $t('common.got_it') }}</button>
                </div>
            </div>
        </div>
    `,
    setup() {
        const appConfig = inject('appConfig');
        const todayMatches = ref([]);
        const isLoading = ref(true);
        const userStatus = ref('guest');

        // Welcome Modal Frekans Kontrolü
        const showWelcomeModal = ref(false);
        const checkWelcomeModal = () => {
            const modal = appConfig?.value?.announcement_modal;
            if (!modal || !modal.active || !modal.text) return;
            
            const appId = appConfig.value?.app_id || '1';
            const seenKey = 'acms_welcome_modal_seen_' + appId;
            const lastSeen = localStorage.getItem(seenKey);
            const freq = modal.frequency || 'daily';
            
            if (freq === 'always') {
                showWelcomeModal.value = true;
            } else if (freq === 'once') {
                if (!lastSeen) showWelcomeModal.value = true;
            } else { // daily
                if (!lastSeen || (Date.now() - parseInt(lastSeen)) > 24 * 60 * 60 * 1000) {
                    showWelcomeModal.value = true;
                }
            }
        };
        const closeWelcomeModal = () => {
            showWelcomeModal.value = false;
            const appId = appConfig.value?.app_id || '1';
            localStorage.setItem('acms_welcome_modal_seen_' + appId, Date.now().toString());
        };

        const handleTriggerWelcomeModal = () => {
            showWelcomeModal.value = true;
        };
        window.addEventListener('acms-trigger-welcome-modal', handleTriggerWelcomeModal);

        watch(appConfig, () => {
            checkWelcomeModal();
        }, { immediate: true, deep: true });

        const announcement = computed(() => {
            if (appConfig.value && appConfig.value.home_announcement_text) {
                return appConfig.value.home_announcement_text;
            }
            return null;
        });

        const countdownStr = ref(null);
        let timer = null;

        const updateCountdown = (targetTime) => {
            const now = new Date();
            const diff = targetTime - now;
            if (diff <= 0) {
                countdownStr.value = null;
                return;
            }
            const h = Math.floor(diff / 1000 / 60 / 60);
            const m = Math.floor(diff / 1000 / 60) % 60;
            const s = Math.floor(diff / 1000) % 60;
            countdownStr.value = `${h.toString().padStart(2, '0')}:${m.toString().padStart(2, '0')}:${s.toString().padStart(2, '0')}`;
        };

        const topPick = computed(() => {
            if (!todayMatches.value.length) return null;
            const pending = todayMatches.value.filter(m => m.status === 'pending');
            if (!pending.length) return null;
            return pending.reduce((prev, current) => {
                return (prev.confidence_rate > current.confidence_rate) ? prev : current;
            });
        });

        const topWinners = ref([]);

        const isApproved = computed(() => userStatus.value === 'approved');

        const formatTime = (timeStr) => {
            if (!timeStr) return '';
            if (window.i18n) return window.i18n.formatKickoffTime(timeStr);
            const dt = new Date(timeStr.replace(/-/g, '/'));
            return dt.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
        };

        const formatDate = (timeStr) => {
            if (!timeStr) return '';
            if (window.i18n) return window.i18n.formatMatchDate(timeStr);
            const dt = new Date(timeStr.replace(/-/g, '/'));
            return new Intl.DateTimeFormat('en-US', { month: 'short', day: 'numeric' }).format(dt);
        };

        onMounted(async () => {
            isLoading.value = true;
            try {
                // Fetch profile first to get user status
                const profileData = await apiCall('profile.php?app_id=1');
                if (profileData && profileData.status === 'success' && profileData.data) {
                    userStatus.value = profileData.data.status;
                }

                const d = new Date();
                const offset = d.getTimezoneOffset() * 60000;
                const localISOTime = (new Date(d.getTime() - offset)).toISOString().split('T')[0];
                
                const data = await apiCall('matches.php?app_id=1&date=' + localISOTime);
                if (data && data.status === 'success') {
                    todayMatches.value = data.data;
                    
                    const pending = todayMatches.value.filter(m => m.status === 'pending');
                    if (pending.length > 0) {
                        const now = new Date();
                        let closestTime = null;
                        for (const m of pending) {
                            if (!m.match_time) continue;
                            const mt = new Date(m.match_time.replace(/-/g, '/'));
                            if (mt > now) {
                                if (!closestTime || mt < closestTime) {
                                    closestTime = mt;
                                }
                            }
                        }
                        if (closestTime) {
                            updateCountdown(closestTime);
                            timer = setInterval(() => updateCountdown(closestTime), 1000);
                        }
                    }
                }
                
                // Fetch Recent Winners
                const winnersData = await apiCall('matches.php?app_id=1&action=recent_winners');
                if (winnersData && winnersData.status === 'success') {
                    topWinners.value = winnersData.data;
                }
            } finally {
                isLoading.value = false;
            }
        });

        onUnmounted(() => {
            if (timer) clearInterval(timer);
            window.removeEventListener('acms-trigger-welcome-modal', handleTriggerWelcomeModal);
        });

        return { isLoading, announcement, countdownStr, topPick, topWinners, userStatus, isApproved, formatTime, formatDate, showWelcomeModal, closeWelcomeModal };
    }
};

// --- GLOBAL MATCHES CACHE (Prevents flashing & jumping on tab switch) ---
const globalMatchesCache = {};

// --- COMPONENT: Tips Page (F1.10) ---
const Tips = {
    template: `
        <div class="main-content">
            <div class="date-slider" ref="dateSliderRef">
                <div 
                    v-for="day in pastDays" 
                    :key="day.date" 
                    class="date-pill" 
                    :class="{ active: selectedDate === day.date }" 
                    @click="selectDate(day.date)"
                >
                    {{ day.label }}
                </div>
            </div>
            
            <!-- PENDING APPROVAL NOTICE BANNER -->
            <div v-if="userStatus === 'pending'" class="pending-banner" style="margin-top: 14px; margin-bottom: 14px;">
                <div style="font-size: 22px; line-height: 1;">⏳</div>
                <div style="flex: 1;">
                    <div style="font-weight: 700; font-size: 13px; color: #fbbf24; margin-bottom: 2px;">{{ $t('vip.vip_locked_title') }}</div>
                    <div style="font-size: 12px; color: #cbd5e1; line-height: 1.4;">{{ $t('vip.vip_locked_desc') }}</div>
                </div>
            </div>
            
            <div v-if="isLoading" key="loading">
                <!-- Skeleton Loader Simulation -->
                <div class="mc" style="opacity: 0.6; animation: pulse 1.5s infinite; pointer-events: none; margin-bottom: 7px;" v-for="i in 4" :key="'skeleton'+i">
                    <div class="mc-head">
                        <div class="mc-league">
                            <div style="width: 16px; height: 16px; background: rgba(255,255,255,0.1); border-radius: 50%;"></div>
                            <div style="width: 120px; height: 12px; background: rgba(255,255,255,0.1); border-radius: 4px; margin-left: 6px;"></div>
                        </div>
                        <div style="width: 35px; height: 12px; background: rgba(255,255,255,0.1); border-radius: 4px;"></div>
                    </div>
                    <div class="mc-body">
                        <div class="mc-home">
                            <div class="mc-logo" style="background: rgba(255,255,255,0.1); border-radius: 50%;"></div>
                            <div style="width: 80px; height: 14px; background: rgba(255,255,255,0.1); border-radius: 4px; margin-left: 8px;"></div>
                        </div>
                        <div class="mc-vs" style="background: rgba(255,255,255,0.05); color: transparent; border: none;">VS</div>
                        <div class="mc-away">
                            <div style="width: 80px; height: 14px; background: rgba(255,255,255,0.1); border-radius: 4px; margin-right: 8px;"></div>
                            <div class="mc-logo" style="background: rgba(255,255,255,0.1); border-radius: 50%;"></div>
                        </div>
                    </div>
                    <div class="mc-stats">
                        <span class="mc-pct" style="color: transparent; background: rgba(255,255,255,0.05);">----</span>
                        <span class="mc-pred" style="color: transparent; background: rgba(255,255,255,0.1);">{{ $t('common.loading') }}</span>
                        <span class="mc-odds" style="color: transparent; background: rgba(255,255,255,0.05);">----</span>
                    </div>
                </div>
            </div>
            
            <div v-else-if="matches.length === 0" key="empty" style="padding: 60px 20px; text-align: center; color: #a0a0a0;">
                <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="opacity: 0.4; margin-bottom: 16px;"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                <div style="font-size: 16px;">{{ $t('history.no_matches') }}</div>
            </div>
            
            <div v-else-if="userStatus === 'banned'" key="banned" class="locked-card" style="padding: 0; margin-top: 10px;">
                <div class="locked-overlay" style="border-radius: 12px; z-index: 10;">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                    <span style="color: #ef4444; font-weight:600;">{{ $t('status.banned') }}</span>
                </div>
                <div class="locked-content" style="filter: blur(4px); padding: 16px;">
                    <div v-for="match in matches.slice(0,3)" :key="match.id" class="match-card match-pending" style="margin-bottom: 12px;">
                        <div class="match-teams">
                            <div class="team"><span>{{ match.home_team }}</span></div>
                            <div class="vs">VS</div>
                            <div class="team"><span>{{ match.away_team }}</span></div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div v-else :key="'content-' + selectedDate">
                <div v-for="match in matches" :key="match.id" class="mc" :class="getMatchClass(match.status)">
                    <div class="mc-head">
                        <div class="mc-league">
                            <img :src="match.league_logo || 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7'" class="mc-league-img" @error="$event.target.src='data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7'">
                            <span>{{ match.league }}</span>
                        </div>
                        <span class="mc-time" v-if="match.status === 'postponed'" style="color:#ef4444;">{{ $t('status.postponed') }}</span>
                        <span class="mc-time" v-else>{{ formatTime(match.match_time) }}<span v-if="match.status !== 'pending'" class="mc-ft"> FT</span></span>
                    </div>
                    <div class="mc-body">
                        <div class="mc-home">
                            <img :src="match.home_logo || 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7'" class="mc-logo" @error="$event.target.src='data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7'">
                            <span class="mc-tname">{{ match.home_team }}</span>
                        </div>
                        <div class="mc-vs" :class="match.status === 'win' ? 'mc-vs-win' : (match.status === 'lose' ? 'mc-vs-lose' : '')">
                            {{ match.status !== 'pending' && match.status !== 'postponed' && match.score ? match.score : (match.status === 'postponed' ? '--' : 'VS') }}
                        </div>
                        <div class="mc-away">
                            <span class="mc-tname">{{ match.away_team }}</span>
                            <img :src="match.away_logo || 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7'" class="mc-logo" @error="$event.target.src='data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7'">
                        </div>
                    </div>
                    <div class="mc-stats">
                        <span class="mc-pct">{{ match.confidence_rate ? match.confidence_rate + '%' : '-' }}</span>
                        <span class="mc-pred prediction-val" v-if="match.prediction">{{ $translatePick(match.prediction) }}</span>
                        <span class="mc-pred" v-else>🔒 VIP</span>
                        <span class="mc-odds" v-if="match.odds">{{ parseFloat(match.odds).toFixed(2) }}</span>
                        <span class="mc-odds" v-else>🔒</span>
                    </div>
                </div>
            </div>
        </div>
    `,
    setup() {
        const selectedDate = ref('');
        const matches = ref([]);
        const isLoading = ref(true);
        const userStatus = ref('guest');
        const dateSliderRef = ref(null);

        const getPastDays = (numDays) => {
            const days = [];
            const lang = (window.i18n && window.i18n.currentLang) ? window.i18n.currentLang.value : 'en';
            for (let i = numDays - 1; i >= 0; i--) {
                const d = new Date();
                d.setDate(d.getDate() - i);
                
                const offset = d.getTimezoneOffset() * 60000;
                const localISOTime = (new Date(d.getTime() - offset)).toISOString().split('T')[0];
                
                let label = '';
                if (i === 0) {
                    label = (window.i18n) ? window.i18n.t('history.today') : 'Today';
                } else if (i === 1) {
                    label = (window.i18n) ? window.i18n.t('history.yesterday') : 'Yesterday';
                } else {
                    const localeMap = { en: 'en-US', tr: 'tr-TR', de: 'de-DE', es: 'es-ES', pt: 'pt-PT', fr: 'fr-FR' };
                    const loc = localeMap[lang] || 'en-US';
                    const formatter = new Intl.DateTimeFormat(loc, { day: '2-digit', month: 'short' });
                    label = formatter.format(d);
                }
                days.push({ date: localISOTime, label: label });
            }
            return days;
        };

        const pastDays = computed(() => {
            if (window.i18n && window.i18n.currentLang) {
                const _ = window.i18n.currentLang.value;
            }
            return getPastDays(5);
        });

        const fetchMatches = async (date) => {
            if (globalMatchesCache[date]) {
                matches.value = globalMatchesCache[date];
                isLoading.value = false;
                return;
            }
            isLoading.value = true;
            try {
                const data = await apiCall('matches.php?app_id=1&date=' + date);
                if (data && data.status === 'success') {
                    globalMatchesCache[date] = data.data;
                    matches.value = data.data;
                } else {
                    matches.value = [];
                }
            } finally {
                isLoading.value = false;
            }
        };

        const selectDate = (date) => {
            Native.haptic('light');
            selectedDate.value = date;
            fetchMatches(date);
        };

        const getMatchClass = (status) => {
            if (status === 'win') return 'match-won';
            if (status === 'lose') return 'match-lost';
            if (status === 'postponed') return 'match-postponed';
            return 'match-pending';
        };

        const formatTime = (datetime) => {
            if (!datetime) return '';
            if (window.i18n) return window.i18n.formatKickoffTime(datetime);
            try {
                const parts = datetime.split(' ');
                if (parts.length === 2) {
                    const timeParts = parts[1].split(':');
                    return timeParts[0] + ':' + timeParts[1];
                }
            } catch (e) {}
            return datetime;
        };

        onMounted(async () => {
            // Keep screen on while viewing predictions
            Native.setKeepAwake(true);

            const profileData = await apiCall('profile.php?app_id=1', 'GET');
            if (profileData && profileData.status === 'success') {
                userStatus.value = profileData.data.status;
            }
            
            if (pastDays.value.length > 0) {
                selectedDate.value = pastDays.value[pastDays.value.length - 1].date;
                fetchMatches(selectedDate.value);
            }

            setTimeout(() => {
                if (dateSliderRef.value) {
                    dateSliderRef.value.scrollLeft = dateSliderRef.value.scrollWidth;
                }
            }, 100);
        });

        onUnmounted(() => {
            // Restore normal screen timeout when leaving tips
            Native.setKeepAwake(false);
        });

        return { 
            pastDays, selectedDate, matches, isLoading, userStatus,
            selectDate, getMatchClass, formatTime, dateSliderRef
        };
    }
};

// --- COMPONENT: Profile & Settings (F2.2 & i18n & Timezone) ---
const Profile = {
    template: `
        <div class="main-content">
            <div class="profile-card">
                <div class="profile-header">
                    <div class="profile-avatar">
                        <img v-if="user?.avatar_url" :src="user.avatar_url" alt="Avatar">
                        <span v-else>{{ getInitials(user?.name) }}</span>
                    </div>
                    <div class="profile-info">
                        <div class="profile-name" style="display:flex; align-items:center; gap:6px; flex-wrap:wrap;">
                            <span>{{ user?.name }}</span>
                            <span v-if="user?.auth_provider === 'google' || user?.google_verified == 1" style="display:inline-flex; align-items:center; background:#4285f4; color:#fff; font-size:10px; font-weight:700; padding:2px 6px; border-radius:10px;">
                                <svg width="10" height="10" viewBox="0 0 24 24" fill="currentColor" style="margin-right:3px;"><path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/></svg>Google
                            </span>
                        </div>
                        <div class="profile-email">{{ user?.email }}</div>
                        
                        <div class="status-badge pending" v-if="user?.status === 'pending'">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                            {{ $t('status.pending_review') }}
                        </div>
                        <div class="status-badge" v-else-if="user?.status === 'approved'">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                            {{ $t('status.approved') }}
                        </div>
                        <div class="status-badge" v-else-if="user?.status === 'banned'" style="background: rgba(239,68,68,0.15); color: #ef4444; border: 1px solid rgba(239,68,68,0.3);">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                            {{ $t('status.banned') }}
                        </div>
                        <div class="status-badge danger-text" v-else>
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                            {{ user?.status ? user.status.charAt(0).toUpperCase() + user.status.slice(1) : '' }}
                        </div>
                    </div>
                </div>
                
                <div v-if="user?.status === 'banned' && user?.ban_reason" style="margin-top:10px; padding:12px; border-radius:8px; background:rgba(239,68,68,0.08); border:1px solid rgba(239,68,68,0.3);">
                    <p style="font-size:12px; color:#ef4444; font-weight:600; margin:0 0 4px;">Ban Reason</p>
                    <p style="font-size:13px; color:#a0a0a0; margin:0;">{{ user.ban_reason }}</p>
                </div>
                
                <!-- Update GPA Button (F2.2) -->
                <button v-if="!isLoading && user?.status === 'pending'" class="btn-outline btn-block" style="margin-bottom: 24px;" @click="showGpaModal = true">
                    {{ $t('profile.update_order_code') }}
                </button>
                
                <!-- Preferences Section (Language & Timezone) -->
                <h3 class="section-title" style="font-size: 13px; color: #a0a0a0; text-transform: uppercase;">{{ $t('profile.preferences_section') }}</h3>
                <div class="menu-group">
                    <!-- Language Selection -->
                    <button class="menu-item" @click="showLangModal = true">
                        <div class="menu-item-left">
                            <div class="menu-item-icon">
                                <span style="font-size:18px; line-height:1;">{{ currentLangInfo.flag }}</span>
                            </div>
                            <div style="text-align:left;">
                                <span>{{ $t('profile.language') }}</span>
                                <div style="font-size:11px; color:#94a3b8;">{{ currentLangInfo.nativeName }}</div>
                            </div>
                        </div>
                        <div style="display:flex; align-items:center; gap:6px;">
                            <span style="font-size:12px; color:var(--color-primary, #00c4ff); font-weight:600;">{{ currentLangInfo.code.toUpperCase() }}</span>
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#6b7280" stroke-width="2"><polyline points="9 18 15 12 9 6"></polyline></svg>
                        </div>
                    </button>
                </div>

                <!-- Auto Timezone Switch -->
                <div class="switch-container" @click="toggleTimezone">
                    <div style="text-align:left;">
                        <div class="switch-label-title">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                            <span>{{ $t('profile.timezone_title') }}</span>
                        </div>
                        <div class="switch-label-desc">
                            {{ isAutoTimezone ? $t('profile.timezone_on') : $t('profile.timezone_off') }} • {{ $t('profile.timezone_desc') }}
                        </div>
                    </div>
                    <div class="toggle-switch" :class="{ on: isAutoTimezone }">
                        <div class="toggle-slider"></div>
                    </div>
                </div>

                <!-- Keep Screen Awake Switch -->
                <div class="switch-container" @click="toggleKeepAwake" style="margin-top: 10px;">
                    <div style="text-align:left;">
                        <div class="switch-label-title">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="5" y="2" width="14" height="20" rx="2" ry="2"></rect><line x1="12" y1="18" x2="12.01" y2="18"></line></svg>
                            <span>{{ $t('profile.keep_awake_title') }}</span>
                        </div>
                        <div class="switch-label-desc">
                            {{ isKeepAwake ? $t('profile.keep_awake_on') : $t('profile.keep_awake_off') }} • {{ $t('profile.keep_awake_desc') }}
                        </div>
                    </div>
                    <div class="toggle-switch" :class="{ on: isKeepAwake }">
                        <div class="toggle-slider"></div>
                    </div>
                </div>

                <!-- Account Section -->
                <h3 class="section-title" style="font-size: 13px; color: #a0a0a0; text-transform: uppercase; margin-top:20px;">{{ $t('profile.account_section') }}</h3>
                <div class="menu-group">
                    <button v-if="user?.auth_provider !== 'google' && user?.google_verified != 1" class="menu-item" @click="showPasswordModal = true">
                        <div class="menu-item-left">
                            <div class="menu-item-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg></div>
                            <span>{{ $t('profile.change_password') }}</span>
                        </div>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#6b7280" stroke-width="2"><polyline points="9 18 15 12 9 6"></polyline></svg>
                    </button>
                    <button class="menu-item danger-text" @click="showDeleteModal = true">
                        <div class="menu-item-left">
                            <div class="menu-item-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg></div>
                            <span>{{ $t('profile.delete_account') }}</span>
                        </div>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#6b7280" stroke-width="2"><polyline points="9 18 15 12 9 6"></polyline></svg>
                    </button>
                </div>
                
                <h3 class="section-title" style="font-size: 13px; color: #a0a0a0; text-transform: uppercase;">{{ $t('profile.support_section') }}</h3>
                <div class="menu-group">
                    <button class="menu-item" @click="openContactUs">
                        <div class="menu-item-left">
                            <div class="menu-item-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg></div>
                            <span>{{ $t('profile.contact_us') }}</span>
                        </div>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#6b7280" stroke-width="2"><polyline points="9 18 15 12 9 6"></polyline></svg>
                    </button>
                    <button class="menu-item" @click="openModal($t('profile.privacy_policy'), appConfig?.privacy_policy || 'Not available.')">
                        <div class="menu-item-left">
                            <div class="menu-item-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg></div>
                            <span>{{ $t('profile.privacy_policy') }}</span>
                        </div>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#6b7280" stroke-width="2"><polyline points="9 18 15 12 9 6"></polyline></svg>
                    </button>
                    <button class="menu-item" @click="openModal($t('profile.terms_of_use'), appConfig?.terms_of_use || 'Not available.')">
                        <div class="menu-item-left">
                            <div class="menu-item-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg></div>
                            <span>{{ $t('profile.terms_of_use') }}</span>
                        </div>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#6b7280" stroke-width="2"><polyline points="9 18 15 12 9 6"></polyline></svg>
                    </button>
                    <button class="menu-item" @click="openModal($t('profile.about_us'), appConfig?.about_us || 'Not available.')">
                        <div class="menu-item-left">
                            <div class="menu-item-icon">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
                            </div>
                            <span>{{ $t('profile.about_us') }}</span>
                        </div>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#6b7280" stroke-width="2"><polyline points="9 18 15 12 9 6"></polyline></svg>
                    </button>
                </div>
                
                <button class="btn-outline btn-block" @click="handleSignOut" style="margin-top: 8px;">{{ $t('profile.sign_out') }}</button>
                <div class="app-version">{{ $t('common.app_version') }} v1.0.0</div>
            </div>
            
            <!-- Language Picker Modal (Bottom Sheet) -->
            <transition name="slide-up">
                <div class="bottom-sheet-overlay" v-if="showLangModal" @click.self="showLangModal = false">
                    <div class="bottom-sheet-content" style="max-height: 80vh;">
                        <div class="bottom-sheet-header">
                            <div style="display:flex; align-items:center; gap:8px;">
                                <span style="font-size:20px;">🌐</span>
                                <h3 style="margin:0; font-size:16px;">{{ $t('profile.select_language') }}</h3>
                            </div>
                            <button class="close-btn" @click="showLangModal = false">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                            </button>
                        </div>
                        <div class="bottom-sheet-body" style="padding-top:10px;">
                            <div class="lang-list">
                                <button 
                                    v-for="lang in supportedLanguages" 
                                    :key="lang.code" 
                                    class="lang-item" 
                                    :class="{ active: currentLang === lang.code }"
                                    @click="changeLanguage(lang.code)"
                                >
                                    <div class="lang-item-left">
                                        <span class="lang-flag">{{ lang.flag }}</span>
                                        <div>
                                            <div class="lang-name">{{ lang.nativeName }}</div>
                                            <div class="lang-native">{{ lang.name }} ({{ lang.code.toUpperCase() }})</div>
                                        </div>
                                    </div>
                                    <div class="lang-check" v-if="currentLang === lang.code">✓</div>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </transition>

            <!-- Legal Modal (Bottom Sheet) -->
            <transition name="slide-up">
                <div class="bottom-sheet-overlay" v-if="modal.isOpen" @click.self="closeModal">
                    <div class="bottom-sheet-content">
                        <div class="bottom-sheet-header">
                            <h3>{{ modal.title }}</h3>
                            <button class="close-btn" @click="closeModal">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                            </button>
                        </div>
                        <div class="bottom-sheet-body">
                            {{ modal.content }}
                        </div>
                    </div>
                </div>
            </transition>

            <!-- Update GPA Modal -->
            <transition name="fade-scale">
                <div class="modal-overlay" v-if="showGpaModal" @click.self="showGpaModal = false">
                    <div class="modal-content">
                        <h2 style="margin-bottom: 16px;">{{ $t('profile.update_order_code') }}</h2>
                        <p style="margin-bottom: 24px; font-size: 13px;">{{ $t('vip.order_code_desc') }}</p>
                        <form @submit.prevent="submitGpa">
                            <input type="text" class="form-control" v-model="newGpaCode" placeholder="GPA.XXXX-XXXX-XXXX-XXXXX" required style="margin-bottom: 16px;">
                            <button type="submit" class="btn-gradient btn-block">{{ $t('vip.submit_order_code') }}</button>
                        </form>
                    </div>
                </div>
            </transition>
            
            <!-- Change Password Modal -->
            <transition name="fade-scale">
                <div class="modal-overlay" v-if="showPasswordModal" @click.self="showPasswordModal = false">
                    <div class="modal-content">
                        <h3 style="margin-bottom:15px">{{ $t('profile.change_password') }}</h3>
                        <input type="password" class="form-control" v-model="oldPassword" :placeholder="$t('auth.old_password')" style="margin-bottom:10px">
                        <input type="password" class="form-control" v-model="newPassword" :placeholder="$t('auth.new_password') + ' (min 8)'" style="margin-bottom:15px">
                        <button class="btn-gradient btn-block" @click="handleChangePassword" :disabled="isLoadingAction">
                            {{ isLoadingAction ? $t('common.saving') : $t('profile.change_password') }}
                        </button>
                        <button class="btn-outline btn-block" style="margin-top:10px; border:none" @click="showPasswordModal = false">{{ $t('common.cancel') }}</button>
                    </div>
                </div>
            </transition>

            <!-- Delete Account Modal -->
            <transition name="fade-scale">
                <div class="modal-overlay" v-if="showDeleteModal" @click.self="showDeleteModal = false">
                    <div class="modal-content">
                        <h3 style="margin-bottom:10px; color:#ef4444">{{ $t('profile.delete_account') }}</h3>
                        <p style="font-size:13px; color:#a0a0a0; margin-bottom:15px">
                            {{ (user?.auth_provider === 'google' || user?.google_verified == 1) ? 'Warning: This action will permanently delete your account, Google verification and all associated prediction data.' : 'Warning: This action will permanently delete your account. Please enter your password to confirm.' }}
                        </p>
                        <div style="display:flex; align-items:flex-start; gap:10px; margin-bottom:16px; background:rgba(239,68,68,0.06); border-radius:8px; padding:12px;">
                            <input type="checkbox" id="deleteAgreeCheck" v-model="deleteAgreed" style="margin-top:2px; accent-color:#ef4444; flex-shrink:0;">
                            <label for="deleteAgreeCheck" style="font-size:12px; color:#a0a0a0; cursor:pointer; line-height:1.6;">
                                I have read and understood the warnings. I acknowledge that this action is <strong style="color:#f87171">permanent and irreversible</strong>.
                            </label>
                        </div>
                        <input v-if="user?.auth_provider !== 'google' && user?.google_verified != 1" type="password" class="form-control" v-model="deletePassword" :placeholder="$t('auth.password')" style="margin-bottom:15px">
                        <button class="btn-gradient btn-block" style="background: linear-gradient(135deg, #ef4444 0%, #b91c1c 100%);" @click="handleDeleteAccount" :disabled="isLoadingAction || !deleteAgreed || (user?.auth_provider !== 'google' && user?.google_verified != 1 && !deletePassword)">
                            {{ isLoadingAction ? $t('common.saving') : $t('common.confirm') }}
                        </button>
                        <button class="btn-outline btn-block" style="margin-top:10px; border:none" @click="showDeleteModal = false">{{ $t('common.cancel') }}</button>
                    </div>
                </div>
            </transition>
        </div>
    `,
    setup() {
        const appConfig = inject('appConfig');
        const router = VueRouter.useRouter();
        const user = ref(null);
        const isLoading = ref(true);
        const showGpaModal = ref(false);
        const newGpaCode = ref('');
        
        const showPasswordModal = ref(false);
        const showDeleteModal = ref(false);
        const oldPassword = ref('');
        const newPassword = ref('');
        const deletePassword = ref('');
        const isLoadingAction = ref(false);
        const deleteAgreed = ref(false);

        // Language & Timezone state
        const showLangModal = ref(false);
        const supportedLanguages = window.i18n ? window.i18n.SUPPORTED_LANGUAGES : [];
        const currentLang = window.i18n ? window.i18n.currentLang : ref('en');
        const isAutoTimezone = window.i18n ? window.i18n.autoTimezone : ref(true);
        const isKeepAwake = window.i18n ? window.i18n.keepScreenAwake : ref(true);

        const currentLangInfo = computed(() => {
            if (!supportedLanguages || !supportedLanguages.length) {
                return { code: 'en', name: 'English', nativeName: 'English (US/UK)', flag: '🇬🇧' };
            }
            return supportedLanguages.find(l => l.code === currentLang.value) || supportedLanguages[0];
        });

        const changeLanguage = (code) => {
            Native.haptic('medium');
            if (window.i18n) {
                window.i18n.setLanguage(code);
            }
            showLangModal.value = false;
            showToast(window.i18n ? window.i18n.t('common.success') : 'Saved', 'success');
        };

        const toggleTimezone = () => {
            Native.haptic('light');
            if (window.i18n) {
                window.i18n.setAutoTimezone(!isAutoTimezone.value);
            }
        };

        const toggleKeepAwake = () => {
            Native.haptic('light');
            if (window.i18n) {
                window.i18n.setKeepScreenAwake(!isKeepAwake.value);
            }
        };
        
        const modal = ref({ isOpen: false, title: '', content: '' });
        const openModal = (title, content) => {
            modal.value.title = title;
            modal.value.content = content;
            modal.value.isOpen = true;
        };
        const closeModal = () => modal.value.isOpen = false;
        
        const getInitials = (name) => {
            if (!name) return '';
            const words = name.trim().split(' ');
            if (words.length > 1) return (words[0][0] + words[1][0]).toUpperCase();
            return (words[0][0]).toUpperCase();
        };

        const openContactUs = () => {
            router.push('/app/support');
        };

        const fetchProfile = async () => {
            isLoading.value = true;
            try {
                const data = await apiCall('profile.php', 'GET');
                if (data && data.status === 'success') {
                    user.value = data.data;
                }
            } finally {
                isLoading.value = false;
            }
        };

        const submitGpa = async () => {
            if (!newGpaCode.value) return;
            const data = await apiCall('profile.php', 'POST', { action: 'update_gpa', gpa_code: newGpaCode.value });
            if (data && data.status === 'success') {
                showToast(window.i18n ? window.i18n.t('auth.order_code_updated') : "Order code updated! Pending review.", "success");
                showGpaModal.value = false;
                if (user.value) {
                    user.value.status = 'pending';
                }
            }
        };
        
        const handleChangePassword = async () => {
            if (newPassword.value.length < 8) return showToast('New password must be at least 8 characters', 'error');
            isLoadingAction.value = true;
            const data = await apiCall('profile.php', 'POST', { action: 'change_password', old_password: oldPassword.value, new_password: newPassword.value });
            isLoadingAction.value = false;
            if (data && data.status === 'success') {
                showToast(data.message, 'success');
                showPasswordModal.value = false;
                oldPassword.value = ''; newPassword.value = '';
            }
        };

        const handleDeleteAccount = async () => {
            isLoadingAction.value = true;
            const data = await apiCall('profile.php', 'POST', { action: 'delete_account', password: deletePassword.value });
            isLoadingAction.value = false;
            if (data && data.status === 'success') {
                showToast(window.i18n ? window.i18n.t('auth.account_deleted') : 'Account deleted successfully.', 'success');
                deleteAgreed.value = false;
                localStorage.removeItem('acms_token_1');
                router.push('/');
            }
        };

        const handleSignOut = async () => {
            await performSignOut(router);
        };

        onMounted(() => {
            fetchProfile();
        });

        return { 
            appConfig, user, isLoading, showGpaModal, newGpaCode, submitGpa, 
            modal, openModal, closeModal, handleSignOut, getInitials, openContactUs,
            showPasswordModal, showDeleteModal, oldPassword, newPassword, deletePassword, isLoadingAction, handleChangePassword, handleDeleteAccount, deleteAgreed,
            showLangModal, supportedLanguages, currentLang, isAutoTimezone, isKeepAwake, currentLangInfo, changeLanguage, toggleTimezone, toggleKeepAwake
        };
    }
};

// --- COMPONENT: VIP Hub (F2.1) ---
const VipHub = {
    template: `
        <div class="main-content">
            <div class="vip-hub-header">
                <h2>{{ $t('vip.ecosystem_title') }}</h2>
                <p>{{ $t('vip.ecosystem_desc') }}</p>
            </div>
            
            <div v-if="!appConfig" key="loading" style="display:flex; justify-content:center; align-items:center; padding:60px 20px;">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="var(--color-primary, #6366f1)" stroke-width="2" style="animation: spin 1s linear infinite;"><line x1="12" y1="2" x2="12" y2="6"></line><line x1="12" y1="18" x2="12" y2="22"></line><line x1="4.93" y1="4.93" x2="7.76" y2="7.76"></line><line x1="16.24" y1="16.24" x2="19.07" y2="19.07"></line><line x1="2" y1="12" x2="6" y2="12"></line><line x1="18" y1="12" x2="22" y2="12"></line><line x1="4.93" y1="19.07" x2="7.76" y2="16.24"></line><line x1="16.24" y1="7.76" x2="19.07" y2="4.93"></line></svg>
                </div>
                
                <div v-else-if="!appConfig.vip_hub_apps || appConfig.vip_hub_apps.length === 0" key="empty" class="empty-state">
                    <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#4b5563" stroke-width="1.5"><path d="M4 14.899A7 7 0 1 1 15.71 8h1.79a4.5 4.5 0 0 1 2.5 8.242"></path><path d="M12 12v9"></path><path d="M8 17l4 4 4-4"></path></svg>
                    <div class="empty-state-text">{{ $t('vip.no_other_apps') }}</div>
                </div>
                
                <div v-else key="content" style="padding-bottom: 20px;">
                    <div class="cross-promo-card" v-for="app in appConfig.vip_hub_apps" :key="app.id">
                        <img v-if="app.logo_url" :src="app.logo_url" class="promo-app-logo" :alt="app.name" @error="$event.target.style.display='none'">
                        <div v-else class="promo-app-logo" style="display:flex; align-items:center; justify-content:center; background:#6366f1; color:#fff; font-weight:bold; font-size:18px;">
                            {{ getInitials(app.name) }}
                        </div>
                        <div class="promo-app-info">
                            <div class="promo-app-name">{{ app.name }}</div>
                            <div class="promo-app-desc">{{ app.vip_hub_description || 'Premium VIP Predictions.' }}</div>
                        </div>
                        <button class="btn-gradient btn-download" @click="downloadApp(app.play_store_link)">{{ $t('vip.get_on_google_play') }}</button>
                    </div>
                    
                    <div style="margin-top:24px; padding:16px; background:rgba(255,255,255,0.04); border-radius:12px; border:1px solid rgba(255,255,255,0.08);">
                        <p style="font-size:11px; color:#6b7280; line-height:1.6; text-align:center; margin:0;">
                            {{ $t('vip.ecosystem_disclaimer') }}
                        </p>
                    </div>
                </div>
        </div>
    `,
    setup() {
        const appConfig = inject('appConfig');
        const getInitials = (name) => {
            if (!name) return '';
            const words = name.trim().split(' ');
            if (words.length > 1) return (words[0][0] + words[1][0]).toUpperCase();
            return (words[0][0]).toUpperCase();
        };
        const downloadApp = (playStoreUrl) => {
            if (!playStoreUrl) return;
            if (window.Android && window.Android.openUrl) {
                window.Android.openUrl(playStoreUrl);
            } else {
                console.log("Mock Android.openUrl(): ", playStoreUrl);
                window.open(playStoreUrl, '_blank');
            }
        };
        return { appConfig, downloadApp };
    }
};

// --- COMPONENT: Support Center (F2.4) ---
const SupportCenter = {
    template: `
        <div class="main-content">
            <div v-if="isLoading" style="display:flex; justify-content:center; padding:40px;">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#6366f1" stroke-width="2" style="animation: spin 1s linear infinite;"><line x1="12" y1="2" x2="12" y2="6"></line><line x1="12" y1="18" x2="12" y2="22"></line><line x1="4.93" y1="4.93" x2="7.76" y2="7.76"></line><line x1="16.24" y1="16.24" x2="19.07" y2="19.07"></line><line x1="2" y1="12" x2="6" y2="12"></line><line x1="18" y1="12" x2="22" y2="12"></line><line x1="4.93" y1="19.07" x2="7.76" y2="16.24"></line><line x1="16.24" y1="7.76" x2="19.07" y2="4.93"></line></svg>
            </div>
            <div v-else>
                <div v-if="appConfig?.contact && hasContact" style="margin-bottom:24px;">
                    <h3 style="font-size:14px; color:#a0a0a0; text-transform:uppercase; margin-bottom:12px;">📞 {{ $t('support.reach_us') }}</h3>
                    <div style="display:flex; flex-direction:column; gap:10px;">
                        <div v-if="appConfig.contact.telegram"
                           @click="openExternalLink(buildTelegramUrl(appConfig.contact.telegram), 'Telegram')"
                           style="display:flex; align-items:center; gap:12px; padding:14px; background:var(--bg-card); border-radius:12px; text-decoration:none; color:inherit; cursor:pointer;">
                            <div style="width:40px; height:40px; border-radius:50%; background:#229ED9; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="white"><path d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm5.894 8.221l-1.97 9.28c-.145.658-.537.818-1.084.508l-3-2.21-1.447 1.394c-.16.16-.295.295-.605.295l.213-3.053 5.56-5.023c.242-.213-.054-.333-.373-.12L7.93 13.67l-2.969-.924c-.643-.204-.657-.643.136-.953l11.57-4.461c.537-.194 1.006.131.827.889z"/></svg>
                            </div>
                            <div>
                                <div style="font-size:14px; font-weight:600;">Telegram</div>
                                <div style="font-size:11px; color:#a0a0a0; margin-top:2px;">
                                    {{ $t('support.typical_reply') }}: <span style="color:#22c55e; font-weight:600;">{{ appConfig.contact.telegram_response || '~1–2 hours' }}</span>
                                </div>
                            </div>
                            <svg style="margin-left:auto;" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#6b7280" stroke-width="2"><polyline points="9 18 15 12 9 6"></polyline></svg>
                        </div>
                        <div v-if="appConfig.contact.whatsapp"
                           @click="openExternalLink(buildWhatsappUrl(appConfig.contact.whatsapp), 'WhatsApp')"
                           style="display:flex; align-items:center; gap:12px; padding:14px; background:var(--bg-card); border-radius:12px; text-decoration:none; color:inherit; cursor:pointer;">
                            <div style="width:40px; height:40px; border-radius:50%; background:#25D366; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="white"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
                            </div>
                            <div>
                                <div style="font-size:14px; font-weight:600;">WhatsApp</div>
                                <div style="font-size:11px; color:#a0a0a0; margin-top:2px;">
                                    {{ $t('support.typical_reply') }}: <span style="color:#22c55e; font-weight:600;">{{ appConfig.contact.whatsapp_response || '~1–2 hours' }}</span>
                                </div>
                            </div>
                            <svg style="margin-left:auto;" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#6b7280" stroke-width="2"><polyline points="9 18 15 12 9 6"></polyline></svg>
                        </div>
                        <div v-if="appConfig.contact.instagram"
                           @click="openExternalLink(buildInstagramUrl(appConfig.contact.instagram), 'Instagram')"
                           style="display:flex; align-items:center; gap:12px; padding:14px; background:var(--bg-card); border-radius:12px; text-decoration:none; color:inherit; cursor:pointer;">
                            <div style="width:40px; height:40px; border-radius:50%; background:linear-gradient(45deg,#f09433,#e6683c,#dc2743,#cc2366,#bc1888); display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line></svg>
                            </div>
                            <div>
                                <div style="font-size:14px; font-weight:600;">Instagram</div>
                                <div style="font-size:11px; color:#a0a0a0; margin-top:2px;">
                                    {{ $t('support.typical_reply') }}: <span style="color:#f59e0b; font-weight:600;">{{ appConfig.contact.instagram_response || '~24 hours' }}</span>
                                </div>
                            </div>
                            <svg style="margin-left:auto;" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#6b7280" stroke-width="2"><polyline points="9 18 15 12 9 6"></polyline></svg>
                        </div>
                        <div v-if="appConfig.contact.email"
                           @click="openExternalLink('mailto:' + appConfig.contact.email, 'Email')"
                           style="display:flex; align-items:center; gap:12px; padding:14px; background:var(--bg-card); border-radius:12px; text-decoration:none; color:inherit; cursor:pointer;">
                            <div style="width:40px; height:40px; border-radius:50%; background:var(--color-primary); display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                            </div>
                            <div>
                                <div style="font-size:14px; font-weight:600;">Email</div>
                                <div style="font-size:11px; color:#a0a0a0; margin-top:2px;">
                                    {{ $t('support.typical_reply') }}: <span style="color:#f59e0b; font-weight:600;">{{ appConfig.contact.email_response || '~24 hours' }}</span>
                                </div>
                            </div>
                            <svg style="margin-left:auto;" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#6b7280" stroke-width="2"><polyline points="9 18 15 12 9 6"></polyline></svg>
                        </div>
                    </div>
                </div>
                
                <h3 class="section-title">{{ $t('support.create_ticket') }}</h3>
                <div class="support-section">
                    <form @submit.prevent="submitTicket">
                        <div class="form-group">
                            <label>{{ $t('support.category') }}</label>
                            <select class="form-control" v-model="category" style="appearance: none;">
                                <option value="Payment & GPA Code">{{ $t('support.cat_payment') }}</option>
                                <option value="Technical Issue">{{ $t('support.cat_technical') }}</option>
                                <option value="Other">{{ $t('support.cat_other') }}</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>{{ $t('support.message') }}</label>
                            <textarea class="form-control" v-model="message" :placeholder="$t('support.message_placeholder')" required></textarea>
                        </div>
                        <button type="submit" class="btn-outline btn-block">{{ $t('support.submit_ticket') }}</button>
                    </form>
                </div>
                
                <h3 class="section-title">{{ $t('support.your_tickets') }}</h3>
                <div class="support-section">
                    <div v-if="tickets.length === 0" class="empty-state">
                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#4b5563" stroke-width="1.5"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                        <div class="empty-state-text">{{ $t('support.no_tickets') }}</div>
                    </div>
                    <div class="ticket-history-item" v-for="ticket in tickets" :key="ticket.id">
                        <div class="ticket-header">
                            <span class="ticket-cat">{{ ticket.category }}{{ ticket.subject ? ' - ' + ticket.subject : '' }}</span>
                            <span :class="['ticket-status', (ticket.status === 'open' || ticket.status === 'pending') ? 'pending' : (ticket.status === 'closed' || ticket.status === 'resolved' ? 'resolved' : 'canceled')]">{{ ticket.status.charAt(0).toUpperCase() + ticket.status.slice(1) }}</span>
                        </div>
                        <div class="ticket-msg">{{ ticket.message.substring(0, 60) }}{{ ticket.message.length > 60 ? '...' : '' }}</div>
                        <div style="font-size:10px; color:#6b7280; margin-top:4px;">{{ ticket.created_at }}</div>
                    </div>
                </div>
                
                <h3 class="section-title">Frequently Asked Questions</h3>
                <div class="support-section">
                    <div v-if="faqs.length === 0" class="empty-state">
                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#4b5563" stroke-width="1.5"><circle cx="12" cy="12" r="10"></circle><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
                        <div class="empty-state-text">No FAQs available yet.</div>
                    </div>
                    <div class="support-accordion" v-else>
                        <div class="support-accordion-item" v-for="faq in faqs" :key="faq.id">
                            <button class="support-accordion-header" @click="toggleFaq(faq.id)">
                                {{ faq.question }}
                                <svg v-if="activeFaq===faq.id" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="18 15 12 9 6 15"></polyline></svg>
                                <svg v-else width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"></polyline></svg>
                            </button>
                            <div class="support-accordion-body" v-if="activeFaq===faq.id">
                                {{ faq.answer }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- External Link Confirmation Modal -->
            <transition name="fade-scale">
                <div class="modal-overlay" v-if="externalLinkModal.show" @click.self="externalLinkModal.show = false">
                    <div class="modal-content" style="text-align:center;">
                        <div style="width:56px; height:56px; border-radius:50%; background:rgba(99,102,241,0.15); display:flex; align-items:center; justify-content:center; margin:0 auto 16px;">
                            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="var(--color-primary)" stroke-width="2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
                        </div>
                        <h3 style="margin-bottom:8px;">Leaving the App</h3>
                        <p style="font-size:13px; color:#a0a0a0; margin-bottom:24px; line-height:1.6;">
                            You're leaving the app, opening <strong style="color:white;">{{ externalLinkModal.platform }}</strong>. Continue?
                        </p>
                        <button class="btn-gradient btn-block" @click="confirmExternalLink" style="margin-bottom:10px;">
                            Open {{ externalLinkModal.platform }}
                        </button>
                        <button class="btn-outline btn-block" style="border:none; color:#6b7280;" @click="externalLinkModal.show = false">Cancel</button>
                    </div>
                </div>
            </transition>
        </div>
    `,
    setup() {
        const appConfig = inject('appConfig');
        const faqs = ref([]);
        const tickets = ref([]);
        const isLoading = ref(true);
        const category = ref('Payment & GPA Code');
        const message = ref('');
        const activeFaq = ref(0);
        
        const externalLinkModal = reactive({ show: false, url: '', platform: '' });

        const openExternalLink = (url, platform) => {
            externalLinkModal.url = url;
            externalLinkModal.platform = platform;
            externalLinkModal.show = true;
        };

        const confirmExternalLink = () => {
            externalLinkModal.show = false;
            window.open(externalLinkModal.url, '_blank');
        };
        
        const buildTelegramUrl = (val) => {
            if (!val) return '#';
            if (val.startsWith('http')) return val;
            return 'https://t.me/' + val.replace('@', '').trim();
        };
        const buildInstagramUrl = (val) => {
            if (!val) return '#';
            if (val.startsWith('http')) return val;
            return 'https://instagram.com/' + val.replace('@', '').trim();
        };
        const buildWhatsappUrl = (val) => {
            if (!val) return '#';
            if (val.startsWith('http')) return val;
            const digits = val.replace(/\D/g, '');
            return 'https://wa.me/' + digits;
        };

        const hasContact = computed(() => {
            const c = appConfig.value?.contact;
            return c && (c.telegram || c.whatsapp || c.email);
        });
        
        const toggleFaq = (id) => {
            activeFaq.value = activeFaq.value === id ? 0 : id;
        };

        const fetchTickets = async () => {
            const data = await apiCall('support.php?action=tickets');
            if (data && data.status === 'success') {
                tickets.value = data.data;
            }
        };

        const loadData = async () => {
            isLoading.value = true;
            try {
                const [faqData, ticketData] = await Promise.all([
                    apiCall('support.php?action=faqs&app_id=1'),
                    apiCall('support.php?action=tickets')
                ]);
                if (faqData && faqData.status === 'success') {
                    faqs.value = faqData.data;
                }
                if (ticketData && ticketData.status === 'success') {
                    tickets.value = ticketData.data;
                }
            } finally {
                isLoading.value = false;
            }
        };

        onMounted(() => {
            loadData();
        });

        const submitTicket = async () => {
            if (!category.value || !message.value) return;
            const data = await apiCall('support.php', 'POST', { 
                action: 'create_ticket', 
                category: category.value,
                subject: category.value,
                message: message.value,
                app_id: 1
            });
            
            if (data && data.status === 'success') {
                showToast("Ticket submitted successfully!", "success");
                category.value = 'Payment & GPA Code';
                message.value = '';
                fetchTickets();
            }
        };

        return { 
            appConfig, hasContact, faqs, tickets, isLoading, category, message, 
            activeFaq, toggleFaq, submitTicket,
            buildTelegramUrl, buildInstagramUrl, buildWhatsappUrl,
            externalLinkModal, openExternalLink, confirmExternalLink
        };
    }
};

// --- API MOTORU (F3.1) ---
function getApiEndpoint(endpoint) {
    if (window.location.protocol === 'capacitor:' || 
        window.location.protocol === 'file:' || 
        (window.location.hostname === 'localhost' && !window.location.pathname.includes('/acms/')) ||
        (!window.location.pathname.includes('/acms/'))
    ) {
        return 'http://192.168.1.35/acms/api/app/' + endpoint;
    }
    return '../api/app/' + endpoint;
}

async function apiCall(endpoint, method = 'GET', body = null) {
    const url = getApiEndpoint(endpoint);
    const token = localStorage.getItem('acms_token_1');
    const headers = { 'Content-Type': 'application/json' };
    
    if (token) {
        headers['Authorization'] = 'Bearer ' + token;
    }
    
    const config = { method, headers };
    if (body) {
        config.body = JSON.stringify(body);
    }
    
    const cacheKey = 'acms_cache_' + endpoint;

    try {
        const response = await fetch(url, config);
        
        // When any HTTP request succeeds, restore online status
        if (!networkState.isOnline) {
            networkState.isOnline = true;
            networkState.showReconnected = true;
            setTimeout(() => {
                networkState.showReconnected = false;
            }, 3000);
        }

        if (response.status === 401) {
            localStorage.removeItem('acms_token_1');
            showToast(window.i18n ? window.i18n.t('auth.session_expired') : 'Session expired.', 'error');
            return null;
        }
        
        if (response.status === 403) {
            localStorage.removeItem('acms_token_1');
            return { status: 'banned' };
        }
        
        const data = await response.json();
        
        if (data.status === 'error') {
            showToast(data.message || 'An error occurred.', 'error');
        } else if (method === 'GET' && data.status === 'success') {
            try {
                localStorage.setItem(cacheKey, JSON.stringify(data));
            } catch (e) {}
        }
        
        return data;
    } catch (error) {
        console.warn('[apiCall] Network error or offline mode:', error);
        networkState.isOnline = false;
        
        // Offline resilience fallback
        if (method === 'GET') {
            const cached = localStorage.getItem(cacheKey);
            if (cached) {
                try {
                    const parsed = JSON.parse(cached);
                    console.log('[apiCall] Serving from cache for:', endpoint);
                    return parsed;
                } catch (e) {}
            }
        }

        return null;
    }
}

// --- ROUTER CONFIGURATION ---
const routes = [
    { path: '/', component: GuestLanding },
    { path: '/onboarding', component: Onboarding },
    { path: '/register', component: Register },
    { path: '/login', component: Login },
    { path: '/forgot-password', component: ForgotPassword },
    { path: '/welcome', component: Welcome },
    { 
        path: '/app', 
        component: AppLayout,
        children: [
            { path: '', redirect: '/app/home' },
            { path: 'home', component: Home },
            { path: 'tips', component: Tips },
            { path: 'profile', component: Profile },
            { path: 'vip-hub', component: VipHub },
            { path: 'support', component: SupportCenter }
        ]
    },
    { path: '/:pathMatch(.*)*', redirect: '/app/home' }
];

const router = createRouter({
    history: createWebHashHistory(),
    routes,
    scrollBehavior(to, from, savedPosition) {
        return { top: 0, left: 0 };
    }
});

// Route Guard: Screen Security (FLAG_SECURE) & Keep Awake (F4.1 & F4.2)
router.afterEach((to) => {
    // 1. Keep Awake on Tips page
    if (to.path.includes('/app/tips')) {
        Native.setKeepAwake(true);
    } else {
        Native.setKeepAwake(false);
    }

    // 2. FLAG_SECURE Screen recording/capture protection
    applyScreenSecurity(to.path, appConfigRef.value);
});

// Guard for Onboarding and Session Resume
router.beforeEach(async (to, from, next) => {
    const urlParams = new URLSearchParams(window.location.search);
    const isPreview = urlParams.get('preview') === 'true' || urlParams.get('preview') === '1';

    const seenOnboarding = localStorage.getItem('seen_onboarding');
    const token = localStorage.getItem('acms_token_1');

    if (to.path.startsWith('/app')) {
        if (isPreview) {
            // Canlı önizleme modunda giriş zorunluluğunu atla, doğrudan render et
            return next();
        }
        if (!token) return next('/login');
        
        const data = await apiCall('auth/verify.php', 'POST', { token: token, app_id: 1 });
        if (data && (data.status === 'success' || data.status === 'banned')) {
            if (data.token) localStorage.setItem('acms_token_1', data.token);
            if (data.user) {
                PushClient.setUser(data.user, appConfigRef.value);
            }
            if (data.user && data.user.exempt_security === 1) {
                localStorage.setItem('acms_exempt_security_1', '1');
            } else if (data.user) {
                localStorage.removeItem('acms_exempt_security_1');
            }
            if (data.user && data.user.exempt_screenshot === 1) {
                localStorage.setItem('acms_exempt_screenshot_1', '1');
            } else if (data.user) {
                localStorage.removeItem('acms_exempt_screenshot_1');
            }
            return next();
        } else {
            localStorage.removeItem('acms_token_1');
            return next('/login');
        }
    }

    if (to.path === '/' && !seenOnboarding && !isPreview) {
        return next('/onboarding');
    }
    
    // Auto-redirect if token exists
    if ((to.path === '/login' || to.path === '/register' || to.path === '/') && token && !isPreview) {
        const data = await apiCall('auth/verify.php', 'POST', { token: token, app_id: 1 });
        if (data && data.status === 'success') {
            if (data.token) localStorage.setItem('acms_token_1', data.token);
            if (data.user) {
                PushClient.setUser(data.user, appConfigRef.value);
            }
            if (data.user && data.user.exempt_security === 1) {
                localStorage.setItem('acms_exempt_security_1', '1');
            } else if (data.user) {
                localStorage.removeItem('acms_exempt_security_1');
            }
            if (data.user && data.user.exempt_screenshot === 1) {
                localStorage.setItem('acms_exempt_screenshot_1', '1');
            } else if (data.user) {
                localStorage.removeItem('acms_exempt_screenshot_1');
            }
            return next('/app/home');
        }
    }

    next();
});

// --- VUE APP ROOT TEMPLATE (Wraps Toast + Router) ---
const RootComponent = {
    template: `
        <div>
            <!-- Global Toast Container -->
            <div class="toast-container">
                <div v-for="toast in toastState.toasts" :key="toast.id" class="toast" :class="toast.type">
                    <svg v-if="toast.type === 'success'" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"></polyline></svg>
                    <svg v-else-if="toast.type === 'error'" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                    <span>{{ toast.message }}</span>
                </div>
            </div>
            <router-view v-slot="{ Component }">
                <transition name="fade" mode="out-in">
                    <component :is="Component" />
                </transition>
            </router-view>
        </div>
    `,
    setup() {
        return { toastState };
    }
}

// --- VUE APP INITIALIZATION ---
const app = createApp(RootComponent);

// Global i18n Helpers
if (window.i18n) {
    app.config.globalProperties.$t = (key, params) => window.i18n.t(key, params);
    app.config.globalProperties.$i18n = window.i18n;
    app.config.globalProperties.$translatePick = (pick) => window.i18n.translatePick(pick);
    app.config.globalProperties.$translateStatus = (status) => window.i18n.translateStatus(status);
    app.config.globalProperties.$formatKickoff = (time) => window.i18n.formatKickoffTime(time);
    app.config.globalProperties.$formatDate = (time) => window.i18n.formatMatchDate(time);
    app.provide('i18n', window.i18n);
}

// Provide init setup & config
const appConfigRef = ref(null);
app.provide('appConfig', appConfigRef); 

app.use(router);

// --- CANLI ÖNİZLEME (LIVE CUSTOMIZER) POSTMESSAGE KÖPRÜSÜ ---
window.addEventListener('message', function(event) {
    if (!event.data || typeof event.data !== 'object') return;
    // 1. Canlı Konfigürasyon Güncelleme
    if (event.data.type === 'ACMS_PREVIEW_UPDATE' && event.data.data) {
        const d = event.data.data;
        if (!appConfigRef.value) appConfigRef.value = {};
        if (d.name !== undefined) appConfigRef.value.app_name = d.name;
        if (d.home_announcement_text !== undefined) appConfigRef.value.home_announcement_text = d.home_announcement_text;
        if (d.onboarding_steps !== undefined) appConfigRef.value.onboarding_steps = d.onboarding_steps;
        if (d.guide_steps !== undefined) appConfigRef.value.guide_steps = d.guide_steps;
        if (d.contact !== undefined) appConfigRef.value.contact = d.contact;
        if (d.privacy_policy !== undefined) appConfigRef.value.privacy_policy = d.privacy_policy;
        if (d.terms_of_use !== undefined) appConfigRef.value.terms_of_use = d.terms_of_use;
        if (d.about_us !== undefined) appConfigRef.value.about_us = d.about_us;
        if (d.rate_us !== undefined) appConfigRef.value.rate_us = d.rate_us;
        if (d.welcome_modal_active !== undefined || d.welcome_modal_title !== undefined || d.welcome_modal_text !== undefined || d.welcome_modal_frequency !== undefined) {
            appConfigRef.value.announcement_modal = {
                active: !!d.welcome_modal_active,
                title: d.welcome_modal_title || 'Important Notice',
                text: d.welcome_modal_text || '',
                frequency: d.welcome_modal_frequency || 'daily'
            };
        }
        // CSS Değişkenleri
        const root = document.documentElement;
        if (d.primary_color) root.style.setProperty('--color-primary', d.primary_color);
        if (d.secondary_color) root.style.setProperty('--color-secondary', d.secondary_color);
        if (d.accent_color) root.style.setProperty('--color-accent', d.accent_color);
        if (d.bg_color) root.style.setProperty('--bg-base', d.bg_color);
        // Tema
        if (d.theme) {
            let themeLink = document.getElementById('theme-style');
            if (!themeLink) {
                themeLink = document.createElement('link');
                themeLink.rel = 'stylesheet';
                themeLink.id = 'theme-style';
                document.head.appendChild(themeLink);
            }
            themeLink.href = 'themes/' + d.theme + '.css';
        }
        // Font
        if (d.font_family) {
            let fontLink = document.getElementById('dynamic-font-link');
            if (!fontLink) {
                fontLink = document.createElement('link');
                fontLink.id = 'dynamic-font-link';
                fontLink.rel = 'stylesheet';
                document.head.appendChild(fontLink);
            }
            fontLink.href = `https://fonts.googleapis.com/css2?family=${encodeURIComponent(d.font_family)}:wght@400;500;600;700;800&display=swap`;
            document.documentElement.style.setProperty('--font-family', `'${d.font_family}', sans-serif`);
            document.body.style.fontFamily = `'${d.font_family}', sans-serif`;
        }
    }
    // 2. Canlı Rota Yönlendirme (Navigasyon)
    if (event.data.type === 'ACMS_PREVIEW_NAVIGATE' && event.data.route) {
        try {
            router.push(event.data.route);
        } catch (e) {
            console.warn('Preview navigation error:', e);
        }
    }
    // 3. Rate Us Canlı Önizleme Tetikleme
    if (event.data.type === 'ACMS_TRIGGER_RATE_US') {
        window.dispatchEvent(new CustomEvent('acms-trigger-rate-us'));
    }
    // 4. Welcome Modal Canlı Önizleme Tetikleme
    if (event.data.type === 'ACMS_TRIGGER_WELCOME_MODAL') {
        window.dispatchEvent(new CustomEvent('acms-trigger-welcome-modal'));
    }
});

function fixAssetUrl(url) {
    if (!url) return '';
    if (url.startsWith('http://localhost/')) {
        return url.replace('http://localhost/', 'http://192.168.1.35/');
    }
    if (url.startsWith('/acms/')) {
        return 'http://192.168.1.35' + url;
    }
    return url;
}

function dismissLoader() {
    const loader = document.getElementById('app-loader');
    if (loader) {
        loader.classList.add('slide-up');
        setTimeout(() => { if (loader) loader.style.display = 'none'; }, 400);
    }
    const appEl = document.getElementById('app');
    if (appEl) {
        appEl.style.opacity = '1';
    }
}

// Initialization logic
app.mount('#app');

// Fail-safe: Always reveal the app within 2 seconds even if offline or slow network
const loaderFallbackTimeout = setTimeout(() => {
    dismissLoader();
}, 2000);

// Fetch logic moved outside component to init globally
(async () => {
    try {
        const urlParams = new URLSearchParams(window.location.search);
        const appId = urlParams.get('app_id') || '1';
        const isPreview = urlParams.get('preview') === 'true' || urlParams.get('preview') === '1';

        const response = await fetch(getApiEndpoint('init.php?app_id=' + appId));
        const resData = await response.json();
        
        if (resData.status === 'success' && resData.data) {
            const appData = resData.data;
            if (appData.logo_url) appData.logo_url = fixAssetUrl(appData.logo_url);
            appConfigRef.value = appData;
            
            // Initialize OneSignal Push Client
            PushClient.init(appData, router);
            
            const root = document.documentElement;
            if (appData.primary_color) root.style.setProperty('--color-primary', appData.primary_color);
            if (appData.accent_color) root.style.setProperty('--color-accent', appData.accent_color);
            if (appData.bg_color) root.style.setProperty('--bg-base', appData.bg_color);
            const secondary = appData.secondary_color || (appData.primary_color ? shadeColor(appData.primary_color, -20) : '#cc8800');
            root.style.setProperty('--color-secondary', secondary);
            
            if (appData.font_family) {
                let fontLink = document.getElementById('dynamic-font-link');
                if (!fontLink) {
                    fontLink = document.createElement('link');
                    fontLink.id = 'dynamic-font-link';
                    fontLink.rel = 'stylesheet';
                    document.head.appendChild(fontLink);
                }
                fontLink.href = `https://fonts.googleapis.com/css2?family=${encodeURIComponent(appData.font_family)}:wght@400;500;600;700;800&display=swap`;
                root.style.setProperty('--font-family', `'${appData.font_family}', sans-serif`);
                document.body.style.fontFamily = `'${appData.font_family}', sans-serif`;
            }
            
            if (appData.theme) {
                let themeLink = document.getElementById('theme-style');
                if (!themeLink) {
                    themeLink = document.createElement('link');
                    themeLink.rel = 'stylesheet';
                    themeLink.id = 'theme-style';
                    document.head.appendChild(themeLink);
                }
                themeLink.href = 'themes/' + appData.theme + '.css';
            }
    
            const loaderLogo = document.getElementById('loader-logo');
            if (loaderLogo && appData.logo_url) {
                loaderLogo.src = appData.logo_url;
                loaderLogo.style.display = 'block';
                setTimeout(() => { loaderLogo.classList.add('fade-in'); }, 50);
            }
            
            clearTimeout(loaderFallbackTimeout);
            setTimeout(() => {
                dismissLoader();
            }, isPreview ? 0 : 800);
        } else {
            console.error("Init API Failed:", resData?.message);
            dismissLoader();
        }

    } catch (error) {
        console.error("Init API Failed:", error);
        dismissLoader();
    }
})();

function shadeColor(color, percent) {
    if (!color || color.length < 7) return '#cc8800';
    let R = parseInt(color.substring(1,3),16);
    let G = parseInt(color.substring(3,5),16);
    let B = parseInt(color.substring(5,7),16);
    R = parseInt(R * (100 + percent) / 100);
    G = parseInt(G * (100 + percent) / 100);
    B = parseInt(B * (100 + percent) / 100);
    R = (R<255)?R:255; G = (G<255)?G:255; B = (B<255)?B:255;  
    let RR = ((R.toString(16).length==1)?"0"+R.toString(16):R.toString(16));
    let GG = ((G.toString(16).length==1)?"0"+G.toString(16):G.toString(16));
    let BB = ((B.toString(16).length==1)?"0"+B.toString(16):B.toString(16));
    return "#"+RR+GG+BB;
}
