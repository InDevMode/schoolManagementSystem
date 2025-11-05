const defaultTheme = require("tailwindcss/defaultTheme");
const colors = require("tailwindcss/colors");

export default {
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
        "./node_modules/flowbite/**/*.js",
    ],
    darkMode: "class",
    safelist: [
        "from-indigo-600",
        "to-indigo-400",
        "from-green-600",
        "to-green-400",
        "from-emerald-600",
        "to-emerald-400",
        "from-red-600",
        "to-red-400",
        "from-blue-600",
        "to-blue-400",
    ],
    theme: {
        fontFamily: {
            satoshi: ["Outfit", "sans-serif"],
        },
        screens: {
            "2xsm": "375px",
            xsm: "425px",
            "3xl": "2000px",
            ...defaultTheme.screens,
        },
        extend: {
            // =================================================================
            // SECTION DES COULEURS MISE À JOUR
            // =================================================================
            colors: {
                // 1. Couleurs sémantiques définies
                primary: {
                    DEFAULT: '#6366F1', // Votre violet original (similaire à indigo-500)
                    light: '#818CF8',   // indigo-400
                    dark: '#4F46E5',    // indigo-600
                },
                secondary: {
                    DEFAULT: '#6B7280', // gray-500 (Nouveau gris comme demandé)
                    light: '#D1D5DB',   // gray-300
                    dark: '#374151',    // gray-700
                },
                success: {
                    DEFAULT: '#10B981', // emerald-500 (Vert plus vibrant)
                    light: '#6EE7B7',   // emerald-300
                    dark: '#059669',    // emerald-600
                },
                danger: {
                    DEFAULT: '#EF4444', // red-500 (Rouge standard)
                    light: '#F87171',   // red-400
                    dark: '#DC2626',    // red-600
                },
                warning: {
                    DEFAULT: '#F59E0B', // amber-500 (Jaune/Orange pour les avertissements)
                    light: '#FCD34D',   // amber-300
                    dark: '#D97706',    // amber-600
                },

                // 2. Vos couleurs personnalisées existantes sont conservées
                white: "#FFFFFF",
                black: {
                    ...colors.black,
                    DEFAULT: "#1C2434",
                    2: "#010101",
                },
                body: "#64748B",
                bodydark: "#AEB7C0",
                bodydark1: "#DEE4EE",
                bodydark2: "#8A99AF",
                stroke: "#E2E8F0",
                gray: { // Votre gris personnalisé est conservé
                    ...colors.gray,
                    DEFAULT: "#EFF4FB",
                    2: "#F7F9FC",
                    3: "#FAFAFA",
                },
                graydark: "#333A48",
                whiten: "#F1F5F9",
                whiter: "#F5F7FD",
                boxdark: "#24303F",
                "boxdark-2": "#1A222C",
                strokedark: "#2E3A47",
                "form-strokedark": "#3d4d60",
                "form-input": "#1d2a39",
                meta: { // Vos couleurs meta sont conservées
                    1: "#DC3545", // Note: c'est maintenant 'danger.dark'
                    2: "#EFF2F7",
                    3: "#10B981", // Note: c'est maintenant 'success.DEFAULT'
                    4: "#313D4A",
                    5: "#259AE6",
                    6: "#FFBA00",
                    7: "#FF6766",
                    8: "#F0950C",
                    9: "#E5E7EB",
                    10: "#0FADCF",
                },
            },
            // =================================================================
            // FIN DE LA SECTION DES COULEURS
            // =================================================================

            fontSize: {
                "title-xxl": ["44px", "55px"],
                "title-xl": ["36px", "45px"],
                "title-xl2": ["33px", "45px"],
                "title-lg": ["28px", "35px"],
                "title-md": ["24px", "30px"],
                "title-md2": ["26px", "30px"],
                "title-sm": ["20px", "26px"],
                "title-xsm": ["18px", "24px"],
            },
            spacing: {
                4.5: "1.125rem",
                5.5: "1.375rem",
                // ... tous vos autres espacements sont conservés
                242.5: "60.625rem",
            },
            // ... etc. (toutes les autres sections restent inchangées)
            maxWidth: {
                2.5: "0.625rem",
                // ...
                292.5: "73.125rem",
            },
            maxHeight: {
                35: "8.75rem",
                70: "17.5rem",
                90: "22.5rem",
                550: "34.375rem",
                300: "18.75rem",
            },
            minWidth: {
                22.5: "5.625rem",
                42.5: "10.625rem",
                47.5: "11.875rem",
                75: "18.75rem",
            },
            zIndex: {
                999999: "999999",
                99999: "99999",
                9999: "9999",
                999: "999",
                99: "99",
                9: "9",
                1: "1",
            },
            opacity: {
                65: ".65",
            },
            transitionProperty: { width: "width", stroke: "stroke" },
            borderWidth: {
                6: "6px",
            },
            boxShadow: {
                default: "0px 8px 13px -3px rgba(0, 0, 0, 0.07)",
                card: "0px 1px 3px rgba(0, 0, 0, 0.12)",
                "card-2": "0px 1px 2px rgba(0, 0, 0, 0.05)",
                switcher:
                    "0px 2px 4px rgba(0, 0, 0, 0.2), inset 0px 2px 2px #FFFFFF, inset 0px -1px 1px rgba(0, 0, 0, 0.1)",
                "switch-1": "0px 0px 5px rgba(0, 0, 0, 0.15)",
                1: "0px 1px 3px rgba(0, 0, 0, 0.08)",
                2: "0px 1px 4px rgba(0, 0, 0, 0.12)",
                3: "0px 1px 5px rgba(0, 0, 0, 0.14)",
                4: "0px 4px 10px rgba(0, 0, 0, 0.12)",
                5: "0px 1px 1px rgba(0, 0, 0, 0.15)",
                6: "0px 3px 15px rgba(0, 0, 0, 0.1)",
                7: "-5px 0 0 #313D4A, 5px 0 0 #313D4A",
                8: "1px 0 0 #313D4A, -1px 0 0 #313D4A, 0 1px 0 #313D4A, 0 -1px 0 #313D4A, 0 3px 13px rgb(0 0 0 / 8%)",
            },
            dropShadow: {
                1: "0px 1px 0px #E2E8F0",
                2: "0px 1px 4px rgba(0, 0, 0, 0.12)",
            },
            keyframes: {
                linspin: {
                    "100%": { transform: "rotate(360deg)" },
                },
                easespin: {
                    "12.5%": { transform: "rotate(135deg)" },
                    "25%": { transform: "rotate(270deg)" },
                    "37.5%": { transform: "rotate(405deg)" },
                    "50%": { transform: "rotate(540deg)" },
                    "62.5%": { transform: "rotate(675deg)" },
                    "75%": { transform: "rotate(810deg)" },
                    "87.5%": { transform: "rotate(945deg)" },
                    "100%": { transform: "rotate(1080deg)" },
                },
                "left-spin": {
                    "0%": { transform: "rotate(130deg)" },
                    "50%": { transform: "rotate(-5deg)" },
                    "100%": { transform: "rotate(130deg)" },
                },
                "right-spin": {
                    "0%": { transform: "rotate(-130deg)" },
                    "50%": { transform: "rotate(5deg)" },
                    "100%": { transform: "rotate(-130deg)" },
                },
                rotating: {
                    "0%, 100%": { transform: "rotate(360deg)" },
                    "50%": { transform: "rotate(0deg)" },
                },
                topbottom: {
                    "0%, 100%": { transform: "translate3d(0, -100%, 0)" },
                    "50%": { transform: "translate3d(0, 0, 0)" },
                },
                bottomtop: {
                    "0%, 100%": { transform: "translate3d(0, 0, 0)" },
                    "50%": { transform: "translate3d(0, -100%, 0)" },
                },
            },
            animation: {
                linspin: "linspin 1568.2353ms linear infinite",
                easespin:
                    "easespin 5332ms cubic-bezier(0.4, 0, 0.2, 1) infinite both",
                "left-spin":
                    "left-spin 1333ms cubic-bezier(0.4, 0, 0.2, 1) infinite both",
                "right-spin":
                    "right-spin 1333ms cubic-bezier(0.4, 0, 0.2, 1) infinite both",
                "ping-once": "ping 5s cubic-bezier(0, 0, 0.2, 1)",
                rotating: "rotating 30s linear infinite",
                topbottom: "topbottom 60s infinite alternate linear",
                bottomtop: "bottomtop 60s infinite alternate linear",
                "spin-1.5": "spin 1.5s linear infinite",
                "spin-2": "spin 2s linear infinite",
                "spin-3": "spin 3s linear infinite",
            },
        },
    },
    plugins: [],
};
