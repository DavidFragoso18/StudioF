import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['DM Sans', ...defaultTheme.fontFamily.sans],
                serif: ['Playfair Display', 'serif'], // Elegant font for headers
            },
            colors: {
                'mocha': '#A47864',      // Primary
                'concrete': '#D6D1CD',   // Secondary
                'chalk': '#F9F7F4',      // Base
                'sage': '#8FA895',       // Accent
                'charcoal': '#3D3B38',   // Text
            },
            backgroundImage: {
                'hero-pattern': "url('/images/hero-bg.jpg')", // Placeholder
            },
            keyframes: {
                fadeIn: {
                    '0%': { opacity: '0' },
                    '100%': { opacity: '1' },
                },
                slideUp: {
                    '0%': { transform: 'translateY(20px)', opacity: '0' },
                    '100%': { transform: 'translateY(0)', opacity: '1' },
                },
                slideDown: {
                    '0%': { transform: 'translateY(-20px)', opacity: '0' },
                    '100%': { transform: 'translateY(0)', opacity: '1' },
                }
            },
            animation: {
                'fade-in': 'fadeIn 1s ease-out',
                'slide-up': 'slideUp 0.8s ease-out',
                'slide-down': 'slideDown 0.5s ease-out',
            }
        },
    },

    plugins: [forms],
};
