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
                // Primary matte greenish theme
                primary: {
                    50: '#f0f9f4',
                    100: '#daf2e4',
                    200: '#b8e4cd',
                    300: '#88d0ad',
                    400: '#56b689',
                    500: '#3a9b6f',
                    600: '#2a7d59',
                    700: '#236349',
                    800: '#1e4f3b',
                    900: '#1a4232',
                    950: '#0d251c',
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
