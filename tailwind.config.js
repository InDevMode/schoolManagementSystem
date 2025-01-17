/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
        "./node_modules/flowbite/**/*.js"
    ],
    theme: {
        extend: {
            keyframes: {
                slideIn: {
                    '0%': {transform: 'translateX(-100%)', opacity: '0'},
                    '100%': {transform: 'translateX(0)', opacity: '1'},
                },
            },
            animation: {
                'custom-slide-in': 'slideIn 0.8s ease-out',
            },
            colors: {
                emerald: {
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
                },
            },
        },
    },
    plugins: [
        require('flowbite/plugin')
    ],
}

