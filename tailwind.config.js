const defaultTheme = require('tailwindcss/defaultTheme')

module.exports = {

    theme: {
        extend: {
            screens: {
                'custom': '800px', // Custom breakpoint at exactly 800px
            },
            fontFamily: {
                sans: ['Lato', 'sans-serif'],
            },

            colors: {
                nkgreen: '#4caf50'
            }

        }
    },

    flyonui: {
        themes: ["gourmet", "dark", "soft"]
    },
    variants: {
        extend: {

        }
    },
    content: [
        './app/Filament/**/*.php',
        './resources/views/filament/**/*.blade.php',
        './vendor/filament/**/*.blade.php',
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',
        "./node_modules/flowbite/**/*.js",
        "./node_modules/flyonui/dist/js/*.js"
    ],
    plugins: [
        require('@tailwindcss/forms'),
        require('@tailwindcss/typography'),
        require('flowbite/plugin'),
        require("flyonui"),
        require("flyonui/plugin"),
        require('tailwind-scrollbar'),
    ],
}
