<?php

namespace App\Services;

class BaseColorGuestManager
{
    /**
     * All available base color keys.
     */
    public const KEYS = [
        'primary',
        'secondary',
        'background',
        'surface',
        'text',
        'success',
        'warning',
        'danger',
        'info',
    ];

    /**
     * Cached override values loaded from the persisted PHP file.
     */
    private ?array $overrides = null;

    /**
     * Get a single base color value.
     *
     * Checks the persisted override file first, then falls back to
     * config/basecolorguest.php defaults.
     *
     * When migrating to a database, replace getOverrides() with:
     *   BaseColorSetting::pluck('value', 'key')->toArray()
     * and the rest of this method stays unchanged.
     */
    public function get(string $key, ?string $default = null): string
    {
        $overrides = $this->getOverrides();

        if (isset($overrides[$key])) {
            return $overrides[$key];
        }

        return config("basecolorguest.{$key}", $default ?? '');
    }

    /**
     * Get all base colors as an associative array (overrides merged on top).
     */
    public function all(): array
    {
        $values = [];

        foreach (static::KEYS as $key) {
            $values[$key] = $this->get($key);
        }

        return $values;
    }

    /**
     * Get the light-mode CSS variable map.
     */
    public function cssVariables(): array
    {
        return [
            '--theme-primary' => $this->get('primary'),
            '--theme-secondary' => $this->get('secondary'),
            '--theme-background' => $this->get('background'),
            '--theme-surface' => $this->get('surface'),
            '--theme-text' => $this->get('text'),
            '--theme-success' => $this->get('success'),
            '--theme-warning' => $this->get('warning'),
            '--theme-danger' => $this->get('danger'),
            '--theme-info' => $this->get('info'),
        ];
    }

    /**
     * Dark-mode CSS variable map.
     *
     * Brand colours (Primary, Secondary, etc.) are kept identical to light
     * mode so the admin CMS always resolves the exact same accent
     * regardless of the Appearance setting.  The Appearance system alone
     * is responsible for background / surface / text contrast.
     */
    public function darkCssVariables(): array
    {
        return $this->cssVariables();
    }

    /**
     * The predefined color options available in the picker.
     */
    public function options(): array
    {
        return config('basecolorguest.options', []);
    }

    /**
     * Read persisted overrides from the PHP config-override file.
     *
     * Future database migration: replace this file read with:
     *   return BaseColorSetting::pluck('value', 'key')->toArray();
     */
    private function getOverrides(): array
    {
        if ($this->overrides !== null) {
            return $this->overrides;
        }

        $path = storage_path('app/settings/basecolorguest-override.php');

        if (! file_exists($path)) {
            $this->overrides = [];

            return $this->overrides;
        }

        $data = include $path;
        $this->overrides = is_array($data) ? $data : [];

        return $this->overrides;
    }
}
