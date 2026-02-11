import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                // Primary bold greenish theme
                primary: {
                    50: '#ecfdf5',
                    100: '#d1fae5',
                    200: '#a7f3d0',
                    300: '#6ee7b7',
                    400: '#34d399',
                    500: '#10b981',
                    600: '#059669',
                    700: '#047857',
                    800: '#065f46',
                    900: '#064e3b',
                    950: '#022c22',
                },
                // Secondary colors for accents
                secondary: {
                    50: '#f5f7fa',
                    100: '#eaeef4',
                    200: '#d0dae6',
                    300: '#a7bbd1',
                    400: '#7896b8',
                    500: '#567a9f',
                    600: '#436185',
                    700: '#374e6d',
                    800: '#30435b',
                    900: '#2c3a4d',
                    950: '#1d2633',
                },
                // Neutral grays
                neutral: {
                    50: '#f8f9fa',
                    100: '#f1f3f5',
                    200: '#e9ecef',
                    300: '#dee2e6',
                    400: '#ced4da',
                    500: '#adb5bd',
                    600: '#6c757d',
                    700: '#495057',
                    800: '#343a40',
                    900: '#212529',
                    950: '#0d0f12',
                },
            },
        },
    },

    plugins: [forms],
};
