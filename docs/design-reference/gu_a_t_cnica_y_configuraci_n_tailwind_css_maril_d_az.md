# Marilú Díaz — Arquitectura Técnica, Tailwind CSS Config & Guía de Componentes

## 1. Configuración de Tailwind CSS (Tailwind v4 / v3.4 Compatible)

```javascript
/** @type {import('tailwindcss').Config} */
module.exports = {
  darkMode: 'class',
  content: [
    './app/Views/**/*.{php,html}',
    './public/assets/js/**/*.js',
  ],
  theme: {
    extend: {
      colors: {
        brand: {
          primary: '#063B73',      // Azul Marino Corporativo
          'primary-dark': '#042850',
          'primary-light': '#0D4E96',
          secondary: '#D996A6',    // Rosa Empolvado
          'secondary-light': '#F6E6EB',
          accent: '#A8B58A',       // Verde Salvia
          'accent-light': '#EDF2E8',
          light: '#FFFFFF',        // Blanco puro
          surface: '#FDFCFB',      // Fondo suave editorial
          muted: '#F4EFEA',        // Contenedor suave
          border: '#E8E2DC',
          charcoal: '#1B242D',     // Texto principal alto contraste
          slate: '#5A6776',        // Texto secundario
        }
      },
      fontFamily: {
        serif: ['"Playfair Display"', 'Georgia', 'serif'],
        sans: ['"Plus Jakarta Sans"', 'Inter', 'system-ui', 'sans-serif'],
      },
      boxShadow: {
        'soft-sm': '0 2px 8px -2px rgba(6, 59, 115, 0.05)',
        'soft-md': '0 8px 24px -4px rgba(6, 59, 115, 0.08)',
        'soft-lg': '0 16px 40px -6px rgba(6, 59, 115, 0.10)',
        'soft-rose': '0 10px 30px -5px rgba(217, 150, 166, 0.25)',
      },
      borderRadius: {
        'editorial': '1.25rem',
      }
    },
  },
  plugins: [
    require('@tailwindcss/typography'),
    require('@tailwindcss/forms'),
  ],
}
```

---

## 2. Pila Tecnológica & Arquitectura del Proyecto

* **Backend:** PHP 8.3 + CodeIgniter 4 (MVC, Repository Pattern, Services Layer).
* **Base de Datos:** MariaDB.
* **Autenticación:** CodeIgniter Shield.
* **Frontend:** HTML5 semántico, Tailwind CSS v4, Alpine.js (para reactividad y modales livianos), JavaScript ES6+.
* **Gestión Multimedia:** Conversión automática a formato `.webp`, compresión inteligente, `loading="lazy"`.
* **Editor de Contenido:** CKEditor 5 Community integrado en panel administrativo.
* **Optimización Lighthouse:** Puntuación > 95 en Performance, SEO, Accesibilidad y Buenas Prácticas.

---

## 3. Catálogo de Componentes Reutilizables

1. **Navbar Sticky Editorial:** Logotipo vectorial oficial de Marilú Díaz, menú contextual con enlaces a Artículos, Mentorías, Sobre Mí, Podcast/Recursos y botón de llamada a la acción en Rosa Empolvado con acento Marino.
2. **Hero Section Personal:** Presentación de impacto, propuesta de valor, badges en Verde Salvia (`#A8B58A`) y suscripción rápida a newsletter estilo Substack.
3. **Cards de Historias & Artículos (Medium / Substack style):** Tiempos de lectura estimados, etiquetas temáticas con fondos pasteles suaves, tipografía serif en títulos y avatar de autora.
4. **Carrusel / Masonry de Logros y Testimonios:** Prueba social, citas elegantes con comillas estilizadas y calificaciones.
5. **CTA de Conversión Premium:** Formulario para agendar sesión o unirse al club privado con microinteracciones Alpine.js.
6. **Footer Corporativo Integral:** Enlaces legales, suscripción, créditos de marca y redes sociales.
