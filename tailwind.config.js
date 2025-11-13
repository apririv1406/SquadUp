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
                colors: {
                    lime: '#84CC16', // Verde Lima brillante
                    primary: '#84CC16', // Lima como color principal de acción
                },
            },
        },

    plugins: [forms],
};
