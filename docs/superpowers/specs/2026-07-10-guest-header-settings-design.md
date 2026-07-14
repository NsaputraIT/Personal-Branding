# Guest Header Settings — Design Spec

**Date:** 2026-07-10
**Project:** personal-branding
**Status:** Superseded — the implementation evolved from this early spec.
See the current live modules at `pages::admin.site` and `pages::admin.medsos`.

## Overview

Add a CMS module that lets the admin edit the guest site header's site name and social media URLs (Twitter/X, Facebook, Instagram, LinkedIn) via a new admin page. The guest header renders these values dynamically instead of using hardcoded strings.

## Scope

- **New files:** `config/header.php`, `app/Services/HeaderManager.php`,
  `resources/views/pages/admin/⚡header.blade.php`
- **Modified files:** `routes/web.php`, `resources/views/layouts/app/sidebar.blade.php`,
  `resources/views/guest/sections/header.blade.php`
- **Not touched:** Any other guest sections, auth, user settings, or theme system.

## Architecture

Follow the project's existing file-based config-override pattern (as used by `ThemeManager`).

### Config defaults (`config/header.php`)

```php
return [
    'site_name'    => env('HEADER_SITE_NAME', 'Indra Paradana'),
    'twitter_url'  => env('HEADER_TWITTER_URL', '#'),
    'facebook_url' => env('HEADER_FACEBOOK_URL', '#'),
    'instagram_url'=> env('HEADER_INSTAGRAM_URL', '#'),
    'linkedin_url' => env('HEADER_LINKEDIN_URL', '#'),
];
```

### HeaderManager service (`app/Services/HeaderManager.php`)

- Same structure as `ThemeManager`
- `get(string $key, ?string $default = null): string` — reads from
  `storage/app/settings/header-override.php`, falls back to `config/header.php`
- `all(): array` — returns all header values as an associative array
- Internal cache (`$overrides`) so the file is read at most once per request

### Admin Livewire page (`resources/views/pages/admin/⚡header.blade.php`)

- Single-file component (`pages::admin.header`)
- Properties: `$siteName`, `$twitterUrl`, `$facebookUrl`, `$instagramUrl`, `$linkedinUrl`
- `boot(HeaderManager)` injects the service
- `mount()` loads current values
- `save()` validates (required, URL format for social links) and persists via
  `file_put_contents` to `storage/app/settings/header-override.php`, then shows Flux toast
- Uses the same text input + save button layout as other admin pages

### Route (`routes/web.php`)

```php
Route::livewire('admin/header', 'pages::admin.header')->name('admin.header');
```

Placed inside the existing `auth, verified` middleware group alongside `admin/theme`.

### Sidebar (`resources/views/layouts/app/sidebar.blade.php`)

Add a `flux:sidebar.item` for "Header" under the "Platform" group, using an
appropriate icon, linking to `route('admin.header')` with `wire:navigate`.

### Guest header (`resources/views/guest/sections/header.blade.php`)

- Inject `HeaderManager` at the top via `@php` / `app(HeaderManager::class)`
- Replace hardcoded `"Indra Paradana"` with dynamic value
- Replace `href="#"` on each social icon with the corresponding URL
- Keep all existing markup, classes, and structure unchanged

## Files Changed

| File | Action |
|------|--------|
| `config/header.php` | **Create** — default values |
| `app/Services/HeaderManager.php` | **Create** — config-override reader |
| `resources/views/pages/admin/⚡header.blade.php` | **Create** — Livewire form |
| `routes/web.php` | **Edit** — add `admin/header` route |
| `resources/views/layouts/app/sidebar.blade.php` | **Edit** — add sidebar link |
| `resources/views/guest/sections/header.blade.php` | **Edit** — dynamic values |

## Out of Scope

- Database migration (the existing codebase uses file-based overrides; the
  `ThemeManager` comment already notes the DB path as a future step)
- Multi-language / locale support
- Additional social platforms beyond the four listed
- Any changes to the guest footer or other sections
