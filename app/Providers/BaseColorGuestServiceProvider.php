<?php

namespace App\Providers;

use App\Services\BaseColorGuestManager;
use Illuminate\Support\ServiceProvider;

class BaseColorGuestServiceProvider extends ServiceProvider
{
    /**
     * Register the BaseColorGuestManager as a singleton so every consumer —
     * Blade partial, Livewire component, controller — receives the same instance.
     */
    public function register(): void
    {
        $this->app->singleton(BaseColorGuestManager::class);
    }

    /**
     * Load persisted overrides into the application config so that
     * config('basecolorguest.*') stays in sync with saved admin choices.
     *
     * When migrating to a database:
     *   1. Inject an Eloquent repository into BaseColorGuestManager.
     *   2. Remove the file_exists / include logic from loadOverrides().
     *   3. The foreach that calls config(['basecolorguest.…']) stays unchanged.
     */
    public function boot(): void
    {
        $this->loadOverrides();
    }

    private function loadOverrides(): void
    {
        $path = storage_path('app/settings/basecolorguest-override.php');

        if (! file_exists($path)) {
            return;
        }

        $overrides = include $path;

        if (! is_array($overrides)) {
            return;
        }

        foreach ($overrides as $key => $value) {
            if (in_array($key, BaseColorGuestManager::KEYS, true)) {
                config(["basecolorguest.{$key}" => $value]);
            }
        }
    }
}
