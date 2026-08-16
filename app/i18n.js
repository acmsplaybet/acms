// app/i18n.js - Centralized Multi-Language (i18n) & Timezone & Keep-Awake Engine for ACMS App
(function() {
    const { ref, reactive, computed } = Vue;

    const SUPPORTED_LANGUAGES = [
        { code: 'en', name: 'English', nativeName: 'English (US/UK)', flag: '🇬🇧' },
        { code: 'tr', name: 'Turkish', nativeName: 'Türkçe', flag: '🇹🇷' },
        { code: 'de', name: 'German', nativeName: 'Deutsch', flag: '🇩🇪' },
        { code: 'es', name: 'Spanish', nativeName: 'Español', flag: '🇪🇸' },
        { code: 'pt', name: 'Portuguese', nativeName: 'Português', flag: '🇵🇹' },
        { code: 'fr', name: 'French', nativeName: 'Français', flag: '🇫🇷' },
        { code: 'it', name: 'Italian', nativeName: 'Italiano', flag: '🇮🇹' },
        { code: 'ru', name: 'Russian', nativeName: 'Русский', flag: '🇷🇺' }
    ];

    // Detect device system language
    function detectDeviceLanguage() {
        try {
            const saved = localStorage.getItem('app_lang');
            if (saved && SUPPORTED_LANGUAGES.some(l => l.code === saved)) {
                return saved;
            }
            const navLang = (navigator.language || navigator.userLanguage || 'en').toLowerCase();
            if (navLang.startsWith('tr')) return 'tr';
            if (navLang.startsWith('de')) return 'de';
            if (navLang.startsWith('es')) return 'es';
            if (navLang.startsWith('pt')) return 'pt';
            if (navLang.startsWith('fr')) return 'fr';
            if (navLang.startsWith('it')) return 'it';
            if (navLang.startsWith('ru')) return 'ru';
            return 'en'; // Default Global English
        } catch (e) {
            return 'en';
        }
    }

    const currentLang = ref(detectDeviceLanguage());
    const autoTimezone = ref(localStorage.getItem('app_auto_timezone') !== '0'); // Default: true (1)
    const keepScreenAwake = ref(localStorage.getItem('app_keep_awake') !== '0'); // Default: true (1)

    /**
     * Translate a key with optional fallback & parameter replacement
     * Usage: t('nav.home') or t('vip.order_code_desc', { app_name: 'Real Bet' })
     */
    function t(key, params = {}) {
        if (!key) return '';
        const lang = currentLang.value;
        const locales = window.ACMS_LOCALES || {};
        
        const langDict = locales[lang] || {};
        const fallbackDict = locales['en'] || {};

        let text = getNestedValue(langDict, key);
        if (text === undefined || text === null) {
            text = getNestedValue(fallbackDict, key);
        }
        if (text === undefined || text === null) {
            text = key; // Fallback to raw key
        }

        // Variable interpolation {var_name}
        if (typeof text === 'string' && params && typeof params === 'object') {
            Object.keys(params).forEach(p => {
                text = text.replace(new RegExp(`\\{${p}\\}`, 'g'), params[p]);
            });
        }
        return text;
    }

    function getNestedValue(obj, keyPath) {
        if (!obj || typeof obj !== 'object') return undefined;
        return keyPath.split('.').reduce((prev, curr) => (prev && prev[curr] !== undefined) ? prev[curr] : undefined, obj);
    }

    /**
     * Change active language
     */
    function setLanguage(langCode) {
        if (!SUPPORTED_LANGUAGES.some(l => l.code === langCode)) {
            langCode = 'en';
        }
        currentLang.value = langCode;
        try {
            localStorage.setItem('app_lang', langCode);
            document.documentElement.lang = langCode;
        } catch (e) {}

        // Sync with OneSignal tag if available
        try {
            if (window.OneSignal) {
                window.OneSignal.User.addTag('language', langCode);
            }
        } catch (e) {}
    }

    /**
     * Toggle Local Timezone auto-adjustment
     */
    function setAutoTimezone(enabled) {
        autoTimezone.value = !!enabled;
        try {
            localStorage.setItem('app_auto_timezone', autoTimezone.value ? '1' : '0');
        } catch (e) {}
    }

    /**
     * Toggle Keep Screen Awake setting
     */
    function setKeepScreenAwake(enabled) {
        keepScreenAwake.value = !!enabled;
        try {
            localStorage.setItem('app_keep_awake', keepScreenAwake.value ? '1' : '0');
        } catch (e) {}
        
        // Immediately enforce native screen state if on tips/vip
        try {
            if (window.Native && typeof window.Native.syncScreenAwake === 'function') {
                window.Native.syncScreenAwake();
            }
        } catch (e) {}
    }

    /**
     * Format Match Kickoff Date & Time with Timezone support
     */
    function formatKickoffTime(dateString) {
        if (!dateString) return '';
        try {
            const dateObj = new Date(dateString.replace(/-/g, '/'));
            if (isNaN(dateObj.getTime())) {
                return dateString;
            }

            if (autoTimezone.value) {
                // Device local time (e.g. 19:45)
                return dateObj.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', hour12: false });
            } else {
                // Show standard time (UTC/Server)
                const hours = String(dateObj.getUTCHours()).padStart(2, '0');
                const minutes = String(dateObj.getUTCMinutes()).padStart(2, '0');
                return `${hours}:${minutes} UTC`;
            }
        } catch (e) {
            return dateString;
        }
    }

    /**
     * Format Match Date
     */
    function formatMatchDate(dateString) {
        if (!dateString) return '';
        try {
            const dateObj = new Date(dateString.replace(/-/g, '/'));
            if (isNaN(dateObj.getTime())) return dateString;

            const lang = currentLang.value;
            const localeCodeMap = {
                en: 'en-US',
                tr: 'tr-TR',
                de: 'de-DE',
                es: 'es-ES',
                pt: 'pt-PT',
                fr: 'fr-FR',
                it: 'it-IT',
                ru: 'ru-RU'
            };
            const localeCode = localeCodeMap[lang] || 'en-US';
            
            return dateObj.toLocaleDateString(localeCode, {
                day: '2-digit',
                month: 'short',
                year: 'numeric'
            });
        } catch (e) {
            return dateString;
        }
    }

    /**
     * Translate Prediction / Market Type code
     */
    function translatePick(pickName) {
        if (!pickName) return '';
        const normalized = String(pickName).trim().toLowerCase();

        // Exact market mappings
        if (normalized === '1' || normalized === 'ms 1' || normalized === 'home win' || normalized === 'п1' || normalized === 'victoire 1') return t('picks.home_win');
        if (normalized === '2' || normalized === 'ms 2' || normalized === 'away win' || normalized === 'п2' || normalized === 'victoire 2') return t('picks.away_win');
        if (normalized === 'x' || normalized === 'ms x' || normalized === 'draw' || normalized === 'х' || normalized === 'nul' || normalized === 'empate') return t('picks.draw');
        if (normalized.includes('2.5') && (normalized.includes('üst') || normalized.includes('over') || normalized.includes('más') || normalized.includes('plus') || normalized.includes('больше') || normalized.includes('tb'))) return t('picks.over_25');
        if (normalized.includes('2.5') && (normalized.includes('alt') || normalized.includes('under') || normalized.includes('menos') || normalized.includes('moins') || normalized.includes('меньше') || normalized.includes('tm'))) return t('picks.under_25');
        if (normalized.includes('1.5') && (normalized.includes('üst') || normalized.includes('over') || normalized.includes('más') || normalized.includes('plus') || normalized.includes('больше') || normalized.includes('tb'))) return t('picks.over_15');
        if (normalized.includes('1.5') && (normalized.includes('alt') || normalized.includes('under') || normalized.includes('menos') || normalized.includes('moins') || normalized.includes('меньше') || normalized.includes('tm'))) return t('picks.under_15');
        if (normalized.includes('3.5') && (normalized.includes('üst') || normalized.includes('over') || normalized.includes('más') || normalized.includes('plus') || normalized.includes('больше') || normalized.includes('tb'))) return t('picks.over_35');
        if (normalized.includes('3.5') && (normalized.includes('alt') || normalized.includes('under') || normalized.includes('menos') || normalized.includes('moins') || normalized.includes('меньше') || normalized.includes('tm'))) return t('picks.under_35');
        if (normalized.includes('kg var') || normalized.includes('btts yes') || normalized.includes('both teams score') || normalized.includes('marcan ambos') || normalized.includes('ambas marcam') || normalized.includes('gg') || normalized.includes('оз да') || normalized.includes('les 2 marquent')) return t('picks.btts_yes');
        if (normalized.includes('kg yok') || normalized.includes('btts no') || normalized.includes('ng') || normalized.includes('no marcan') || normalized.includes('оз нет')) return t('picks.btts_no');
        if (normalized.includes('1x') || normalized.includes('1-x') || normalized.includes('1х')) return t('picks.double_chance_1x');
        if (normalized.includes('x2') || normalized.includes('x-2') || normalized.includes('х2')) return t('picks.double_chance_x2');
        if (normalized.includes('12') || normalized.includes('1-2')) return t('picks.double_chance_12');
        if (normalized.includes('ht/ft') || normalized.includes('iy/ms') || normalized.includes('hz/es') || normalized.includes('pt/fin') || normalized.includes('тайм/матч')) return t('picks.ht_ft');

        return pickName; // Default original
    }

    /**
     * Translate Status Badge
     */
    function translateStatus(statusCode) {
        if (!statusCode) return '';
        const s = String(statusCode).trim().toLowerCase();
        if (s === 'won' || s === 'kazandi' || s === '1' || s === 'ganada' || s === 'ganhou' || s === 'gagné' || s === 'vinto' || s === 'выигрыш') return t('status.won');
        if (s === 'lost' || s === 'kaybetti' || s === '2' || s === 'perdida' || s === 'perdeu' || s === 'perdu' || s === 'perso' || s === 'проигрыш') return t('status.lost');
        if (s === 'pending' || s === 'bekliyor' || s === '0' || s === 'pendiente' || s === 'en attente' || s === 'in attesa' || s === 'в ожидании') return t('status.pending');
        if (s === 'postponed' || s === 'ertelendi' || s === 'aplazado' || s === 'adiado' || s === 'reporté' || s === 'rinviata' || s === 'перенесен') return t('status.postponed');
        if (s === 'cancelled' || s === 'iptal' || s === 'cancelado' || s === 'annulé' || s === 'annullata' || s === 'отменен') return t('status.cancelled');
        if (s === 'refunded' || s === 'iade' || s === 'reembolsado' || s === 'remboursé' || s === 'rimborsato' || s === 'возврат') return t('status.refunded');
        return statusCode.toUpperCase();
    }

    // Expose Global i18n object
    window.i18n = {
        SUPPORTED_LANGUAGES,
        currentLang,
        autoTimezone,
        keepScreenAwake,
        t,
        setLanguage,
        setAutoTimezone,
        setKeepScreenAwake,
        formatKickoffTime,
        formatMatchDate,
        translatePick,
        translateStatus
    };
})();
