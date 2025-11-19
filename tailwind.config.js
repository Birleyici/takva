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
            colors: {
                // Shaha Teması İlhamlı Modern İslami Palet
                primary: {
                    50: '#f0fdfa',
                    100: '#ccfbf1',
                    200: '#99f6e4',
                    300: '#5eead4',
                    400: '#2dd4bf',
                    500: '#0f766e',  // Ana teal/koyu yeşil - Shaha tarzı
                    600: '#0d9488',
                    700: '#0f766e',
                    800: '#115e59',
                    900: '#134e4a',
                },
                secondary: {
                    50: '#fafaf9',
                    100: '#f5f5f4',
                    200: '#e7e5e4',
                    300: '#d6d3d1',
                    400: '#a8a29e',
                    500: '#374151',  // Ana koyu gri - Modern metin rengi
                    600: '#4b5563',
                    700: '#374151',
                    800: '#1f2937',
                    900: '#111827',
                },
                accent: {
                    50: '#fffbeb',
                    100: '#fef3c7',
                    200: '#fde68a',
                    300: '#fcd34d',
                    400: '#fbbf24',
                    500: '#d4af37',  // Ana altın - Shaha tarzı elegant altın
                    600: '#f59e0b',
                    700: '#d97706',
                    800: '#b45309',
                    900: '#92400e',
                },
                neutral: {
                    50: '#f9fafb',
                    100: '#f3f4f6',
                    200: '#e5e7eb',
                    300: '#d1d5db',
                    400: '#9ca3af',
                    500: '#6b7280',  // Ana açık gri - Subtle backgrounds
                    600: '#4b5563',
                    700: '#374151',
                    800: '#1f2937',
                    900: '#111827',
                }
            },
            fontFamily: {
                sans: ['Inter', 'system-ui', 'sans-serif'],
                'heading': ['Cinzel Decorative', 'serif'],
                'decorative': ['Cinzel Decorative', 'serif'],
                'arabic': ['Amiri', 'serif'],
                'body': ['Inter', 'system-ui', 'sans-serif'],
            },
        },
    },

    plugins: [forms],
};
