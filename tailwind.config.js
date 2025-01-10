const defaultTheme = require('tailwindcss/defaultTheme')
import preset from './vendor/filament/support/tailwind.config.preset'

module.exports = {
    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter var', ...defaultTheme.fontFamily.sans],
            },
        },
    },
    variants: {
        extend: {
            backgroundColor: ['active'],
        }
    },
    content: [
        './app/Filament/**/*.php',
        './resources/views/filament/**/*.blade.php',
        './vendor/filament/**/*.blade.php',
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',
    ],
    plugins: [
        require('@tailwindcss/forms'),
        require('@tailwindcss/typography'),
    ],
}
