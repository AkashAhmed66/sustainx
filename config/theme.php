<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Application Theme Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains all the theme-related configurations for your
    | application. You can easily modify colors, dimensions, and other
    | theme settings from this central location.
    |
    */

    // Primary Colors (Matte Greenish Theme)
    'colors' => [
        'primary' => [
            'main' => '#3a9b6f',
            'light' => '#56b689',
            'dark' => '#2a7d59',
            'hover' => '#2a7d59',
        ],
        'secondary' => [
            'main' => '#567a9f',
            'light' => '#7896b8',
            'dark' => '#436185',
        ],
        'background' => [
            'main' => '#f8f9fa',
            'sidebar' => '#1e4f3b',
            'card' => '#ffffff',
            'hover' => '#f1f3f5',
        ],
        'text' => [
            'primary' => '#212529',
            'secondary' => '#6c757d',
            'light' => '#ffffff',
            'muted' => '#adb5bd',
        ],
        'border' => [
            'main' => '#e9ecef',
            'dark' => '#dee2e6',
        ],
    ],

    // Layout Dimensions
    'dimensions' => [
        'sidebar' => [
            'width' => '280px',
            'collapsed_width' => '80px',
        ],
        'topbar' => [
            'height' => '64px',
        ],
    ],

    // Animation & Transitions
    'transitions' => [
        'speed' => '300ms',
        'timing' => 'cubic-bezier(0.4, 0, 0.2, 1)',
    ],

    // Application Name
    'app_name' => env('APP_NAME', 'SustainX'),
    'app_short_name' => env('APP_SHORT_NAME', 'S'),

    // Sidebar Configuration
    'sidebar' => [
        'show_logo' => true,
        'collapsible' => true,
        'default_collapsed' => false,
    ],

    // Additional Theme Options
    'options' => [
        'enable_dark_mode' => false,
        'enable_animations' => true,
        'enable_shadows' => true,
        'border_radius' => 'rounded-lg', // Tailwind class
    ],

    /*
    |--------------------------------------------------------------------------
    | Quick Theme Change Guide
    |--------------------------------------------------------------------------
    |
    | To change the main color theme:
    | 1. Update the 'colors.primary' values above
    | 2. Update corresponding values in tailwind.config.js 'primary' colors
    | 3. Update CSS variables in resources/css/app.css (:root section)
    |
    | To change sidebar style:
    | 1. Modify 'colors.background.sidebar' for sidebar background
    | 2. Adjust 'dimensions.sidebar' for width settings
    |
    | After making changes, run: npm run build
    |
    */
];
