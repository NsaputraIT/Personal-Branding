<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Theme Colors
    |--------------------------------------------------------------------------
    |
    | Default theme colours used across both the admin panel and the guest
    | frontend.  ThemeManager is the single source of truth for these values;
    | it currently reads from this file but is ready to be swapped to a
    | database source with no changes to the UI or CSS architecture.
    |
    | Only Primary and Secondary are editable through the admin Theme
    | settings page.  The remaining colours use these hardcoded defaults.
    |
    */

    'primary' => env('THEME_PRIMARY', '#e87532'),
    'secondary' => env('THEME_SECONDARY', '#0f2943'),
    'background' => env('THEME_BACKGROUND', '#ffffff'),
    'surface' => env('THEME_SURFACE', '#ffffff'),
    'text' => env('THEME_TEXT', '#0a0f14'),
    'success' => env('THEME_SUCCESS', '#059652'),
    'warning' => env('THEME_WARNING', '#f59e0b'),
    'danger' => env('THEME_DANGER', '#df1529'),
    'info' => env('THEME_INFO', '#3b82f6'),

    /*
    |--------------------------------------------------------------------------
    | Available color options for the theme picker
    |--------------------------------------------------------------------------
    |
    | Each entry has a label (shown in the UI) and a value (the hex color).
    |
    */

    'options' => [
        ['label' => 'Orange', 'value' => '#FFA500'],
        ['label' => 'Dark Blue', 'value' => '#00008B'],
        ['label' => 'Green', 'value' => '#008000'],
        ['label' => 'Red', 'value' => '#FF0000'],
        ['label' => 'Purple', 'value' => '#800080'],
        ['label' => 'Black', 'value' => '#000000'],
    ],

];
