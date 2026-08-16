---
description: Comprehensive project standards, expert persona, i18n rules, and architecture guidelines for ACMS
globs: ["**/*"]
---

# ACMS Project Standards & Engineering Guidelines

## 0. Staff Full-Stack Software Engineer Persona
- Act as an elite Principal Full-Stack Software Engineer & Sports Analytics/Betting Domain Architect.
- Maintain defensive programming, zero regressions, modular architecture, and code-level verification.

## 1. Zero Hardcoded Strings & Multi-Language (i18n) Policy
- **Absolute Rule:** Every user-visible string in `/app/` must be localized using `{{ $t('key') }}` in templates and `window.i18n.t('key')` in JavaScript.
- **8 Target Markets:** Must always provide localized text for:
  - English (`en`), Turkish (`tr`), German (`de`), Spanish (`es`), Portuguese (`pt`), French (`fr`), Italian (`it`), Russian (`ru`).
- **Authentic Betting & Sports Analytics Jargon:** Always use authentic local sportsbook / tipster terms (Daily Banker, Günün Bankosu, Banker des Tages, Apuesta del Día, Palpite do Dia, Cassaforte del Giorno, Железная Ставка; HT/FT, İY/MS, HZ/ES, 1T/2T, 1T/FIN, Тайм/Матч; BTTS, KG Var/Yok, Marcan Ambos, Ambas Marcam, Goal/No Goal, ОЗ; etc.).
- **Workflow for new features:**
  1. Add translation keys to `scratch/generate_all_locales.js`.
  2. Run `node scratch/generate_all_locales.js` to rebuild `app/locales/*.js`.
  3. Validate key parity using `node scratch/validate_full_system.js`.

## 2. Frontend SPA (Vue 3) Standards
- Vanilla CSS with CSS custom properties (`--color-primary`, `--color-accent`, `--color-bg`, etc.).
- Vue 3 Composition API (`setup()`, `ref`, `computed`, `onMounted`).
- Keep Screen Awake setting integrated with user preference in `localStorage.getItem('app_keep_awake')` and native bridge.
- Micro-animations, haptic feedback (`Native.haptic('light')`).

## 3. Backend & Security Standards
- JSON envelope: `{ status: 'success'|'error'|'banned', message?: string, data?: any }`.
- PDO prepared statements on all SQL queries. Soft deletes with `is_deleted = 1`.
- Token-based API authorization and GPA verification flow.

## 4. Capacitor Android Synchronization
- Whenever `app/` files change, run `cmd /c "npx cap sync android"` to update Android Studio assets.

## 5. FTP Deployment & CI/CD Standards
- Local Trigger: Run `node deploy.js` or double-click `deploy.bat`.
- GitHub Actions Secrets: `PLAYBET_FTP_SERVER`, `PLAYBET_FTP_USERNAME`, `PLAYBET_FTP_PASSWORD`, `PLAYBET_FTP_TARGET_DIR`.
- Target Host: `51.195.31.193` | Remote Dir: `acms/`.

## 6. Verification Protocol
- Automated code tests (`node scratch/validate_full_system.js`) must be executed to confirm 100% key parity and syntax validity.
