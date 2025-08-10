/** @type {import('tailwindcss').Config} */
module.exports = {
  darkMode: 'class',
  content: [
    './resources/**/*.blade.php',
    './resources/**/*.js',
    './resources/**/*.vue',
  ],
  theme: {
    extend: {
      colors: {
        red: {
          DEFAULT: '#FF0000',
        },
        black: {
          DEFAULT: '#000000',
        },
        white: {
          DEFAULT: '#FFFFFF',
        },
      },
    },
  },
  plugins: [],
} 