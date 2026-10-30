/** @type {import('tailwindcss').Config} */
module.exports = {
  darkMode: 'class',
  content: [
    './app/Views/**/*.{php,html}',
    './app/Cells/**/*.{php,html}',
    './resources/js/**/*.js'
  ],
  theme: {
    extend: {
      colors: {
        'brand-primary': '#063B73',      // Azul Marino Corporativo
        'brand-secondary': '#D996A6',    // Rosa Empolvado
        'brand-accent': '#A8B58A',       // Verde Salvia
        'brand-light': '#FFFFFF',        // Blanco Puro
        'brand-surface': '#FBF9F7',      // Lino editorial suave
        'brand-charcoal': '#0B2038',     // Tinta texto alta legibilidad
        'brand-slate': '#5A6776',        // Texto secundario
      },
      fontFamily: {
        serif: ['Merriweather', 'Georgia', 'serif'],
        sans: ['Inter', 'system-ui', '-apple-system', 'sans-serif'],
      },
      maxWidth: {
        'feed': '640px',                 // Feed limpio estilo Instagram/Threads
        'reading': '720px',
      },
      boxShadow: {
        'soft': '0 2px 10px -2px rgba(6, 59, 115, 0.05), 0 1px 3px 0 rgba(6, 59, 115, 0.03)',
        'card': '0 4px 20px -4px rgba(6, 59, 115, 0.08)',
        'pop': '0 12px 32px -6px rgba(6, 59, 115, 0.12)',
      }
    },
  },
  plugins: [],
};
