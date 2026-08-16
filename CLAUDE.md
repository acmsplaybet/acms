# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## What this is

ACMS (App Content Management System) — a PHP/MySQL backend plus Minia Bootstrap 5 admin panel that manages many white-label betting-tips webview apps from one place. One central match pool is distributed to N apps; each app carries its own branding, colors, legal texts, and security settings. Runs under XAMPP at `C:\xampp\htdocs\acms`, served at `http://localhost/acms`.

There is no build step, no package manager, no test framework, and no git repo. Edits to `.php` files are live on next request.

## Environment and commands

No PHP or MySQL client on the Linux `PATH`; use the XAMPP Windows binaries:

```bash
/mnt/c/xampp/php/php.exe -l admin/app_add.php          # syntax check a file (do this after every edit)
/mnt/c/xampp/php/php.exe script.php                    # run a maintenance script
/mnt/c/xampp/mysql/bin/mysql.exe -u root acms -e "..." # query the DB
```

Rebuild the database from scratch (drops nothing, but re-runs the full schema and re-seeds the admin):

```bash
/mnt/c/xampp/php/php.exe setup_db.php   # CREATE DATABASE acms + database_schema.sql + insert_admin.sql
```

`setup_db.php` hardcodes Windows paths (`c:/xampp/htdocs/acms/...`). Since `database_schema.sql` has no `DROP TABLE`/`IF NOT EXISTS`, re-running it against a populated DB fails on the first existing table.

Verification here means: `php -l` the changed files, then exercise the endpoint or page in the browser (or with `curl http://localhost/acms/api/admin/...`). Say so plainly when something can't be verified without a browser.

## Architecture

**API-first.** Everything server-side returns JSON only. Admin pages are static HTML shells that call the API over `fetch()`.

```
api/config/config.php    DB_* constants, timezone, error display, CORS headers, JSON Content-Type,
                         OPTIONS preflight short-circuit — all emitted as a side effect of inclusion
api/config/Database.php  PDO singleton (Database::getInstance()->getConnection()); requires config.php
api/admin/*.php          one file per resource: login.php, apps.php, matches.php
admin/*.php              Minia theme pages, each a complete standalone HTML document
admin/assets/            theme CSS/JS/fonts, copied from minia-bootstrap-.../Minia_HTML_v2.4.0
app/                     planned Vue 3 (CDN, no bundler) webview SPA — does not exist yet
```

Root `assets/`, `config/`, and `includes/` are empty leftovers. The `minia-bootstrap-5-...` directory is the untouched vendor theme download — read it for reference markup, don't edit it.

**Endpoint shape.** Each API file dispatches on `$_SERVER['REQUEST_METHOD']` and then on an `action` value (`?action=get_brands`, or `action` inside the POST body). Bodies arrive as either JSON or form-data, so endpoints do `json_decode(file_get_contents("php://input"))` with a fallback to `$_POST`. Every response is `{status: 'success'|'error', message?, data?}` — keep that envelope. All SQL goes through prepared statements; multi-table writes use `beginTransaction`/`commit` with rollback in the `PDOException` handler (see `matches.php` adding a match plus its `app_matches` rows).

**Auth is client-side only, and the API is unauthenticated.** `api/admin/login.php` verifies a bcrypt password, starts a PHP session, and returns a random token. Admin pages store it as `localStorage.acms_admin_token` and each page top has a JS route guard redirecting to `login.php` when it's missing. `match_add.php` sends `Authorization: Bearer <token>`, but **no endpoint validates it** — anyone who can reach `/acms/api/admin/apps.php` can read and write app data. Treat this as a known gap: flag it rather than silently relying on the guard, and if you add an endpoint, assume server-side auth still needs building.

**Data model** (`database_schema.sql`, 15 tables). The joins that matter: `brands` → `apps` (a brand is a theme family: Real, Alex, Pep), `apps` ↔ `matches` via `app_matches`, `users` ↔ `apps` via `user_apps` (with `pending`/`approved`/`rejected` status for Google Play order-code approval), `promotions` ↔ `apps` via `app_promotions`. `apps` is wide on purpose — colors, `nav_names_json`, `legal_texts_json`, `user_agent`, `min_version`, OneSignal keys, and `frontend_url` (the app's own domain, used for CORS and webview) all live per-app rather than as global settings.

**Soft delete throughout.** `apps` and `matches` use `is_deleted`; deletes are `UPDATE ... SET is_deleted = 1` and every read filters `WHERE is_deleted = 0`. Preserve this — don't introduce hard deletes.

**Schema drift to watch.** `api/admin/matches.php` queries `leagues.is_deleted` and inserts `leagues.slug`, but the `leagues` table in `database_schema.sql` has neither column. `?action=get_leagues` will fail against a DB built purely from the schema file. Check the live table before trusting either source.

**Auto-seeding.** `apps.php?action=get_brands` and `matches.php?action=get_leagues` insert sample rows when their table is empty, so lookups are never blank on a fresh install.

**Uploads.** `apps.php` creates `admin/uploads/apps/` on demand and stores paths as `uploads/apps/<uniqid>.<ext>`, relative to `admin/`. On update, missing upload fields fall back to the existing DB value so edits don't blank out a logo. Extensions come straight from the client filename with no validation — worth tightening if you touch this path.

## Conventions

**API URLs are hardcoded absolute** as `/acms/api/admin/...` in admin pages (`login.php` is the exception, using relative `../api/admin/login.php`). Renaming the htdocs folder breaks every page.

**Admin pages are large and duplicated by design.** `apps_list.php` and `index.php` are 150KB+ because each inlines the full Minia sidebar, topbar, and footer. Sidebar markup, the route guard, the admin-name fill, and the logout handler are copy-pasted across all of them — a change to shared chrome must be applied to every page in `admin/`. Use targeted edits; never rewrite one of these files wholesale.

**Root-level `patch_*.php`, `copy.php`, `setup_matches.php`, `cleanup.php`, `truncate_brands.php` are one-off scaffolding scripts,** not part of the app. They generate and regex-patch admin pages — `setup_matches.php` produced `match_add.php` and `matches_list.php` by copying `app_add.php`/`apps_list.php` and substituting titles and form bodies. They are not idempotent and will corrupt files if re-run against already-patched output. Prefer editing the target `admin/*.php` directly over writing another patch script.

**Language.** UI strings, API `message` values, and code comments are currently Turkish; the docs specify the shipped admin and SPA should eventually be English. Match the surrounding file's language rather than mixing within a page. Respond to the user in Turkish when they write in Turkish.

**`config.php` sends headers on include,** so nothing may output before it — no stray whitespace outside `<?php ?>`, no BOM. It also sets `display_errors = 1` and `Access-Control-Allow-Origin: *`; both are dev-only and marked for tightening before production.

## Documentation is a living log

`acms_documentation.md` is the spec and the project history. It defines the module list, sidebar hierarchy, per-module business rules, and the webview JS-bridge plans (haptics, `FLAG_SECURE`, back-button handling, in-app review). Read the relevant `## 6.x` section before building a module.

Its `## PROJE GELİŞTİRME LOGLARI` tail is an append-only build log; the doc explicitly asks that each completed module add an entry recording what was done, which files were coded, and what the next pending step is. Follow that convention when finishing a module. Current state per the log: schema, PDO layer, login, admin shell, and apps CRUD are done; match management is in progress. Users, tickets, promotions, email templates, settings, backups, and the entire webview SPA are unbuilt.
