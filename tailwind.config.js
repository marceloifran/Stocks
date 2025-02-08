/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    './resources/**/*.blade.php',
    './resources/**/*.js',
    './resources/**/*.vue',
    './vendor/awcodes/palette/resources/views/**/*.blade.php',
    './vendor/assistant-engine/filament-assistant/resources/**/*.blade.php',
    './vendor/assistant-engine/laravel-assistant/resources/**/*.blade.php',

  ],
  theme: {
    extend: {},
  },
  plugins: [],
}

