# Marilú Díaz — Design System & Guía Oficial de Componentes UI

Este documento establece la identidad visual, el sistema tipográfico, la paleta corporativa y la arquitectura de componentes de la plataforma blog editorial de **Marilú Díaz**.

---

## 1. Identidad de Marca & Principios de Diseño

La plataforma combina la sofisticación y calidez del diseño editorial clásico (estilo **Substack**, **Medium** y publicaciones de autor) con el dinamismo y cercanía de un feed moderno (estilo **Instagram** y **Threads**).

### Principios Clave
* **Claridad sobre Ruido:** Interfaces limpias de una sola columna donde la lectura y las fotografías tienen prioridad visual.
* **Calidez Editorial:** Fondos lino natural (`#FBF9F7`) que eliminan el brillo blanco artificial para lectura prolongada.
* **Microinteracciones Precisas:** Animaciones suaves tipo "pop" en corazones de me gusta, transiciones fluidas de carruseles fotográficos y lightbox de pantalla completa sin librerías pesadas.

---

## 2. Paleta Corporativa Oficial & Accesibilidad WCAG 2.1 (AA)

La paleta se rige por los cuatro colores oficiales suministrados:

| Color | Nombre | Código Hex | Rol en la Interfaz | Ratio de Contraste vs Blanco |
|---|---|---|---|---|
| **Primario** | Azul Marino Corporativo | `#063B73` | Títulos `font-serif`, botones principales, navbar y landmarks | **11.4:1** (Pasa AAA) |
| **Secundario** | Rosa Empolvado | `#D996A6` | Acentos decorativos, anillo de historia, corazones activos y bordes suaves | Decorativo / Fondos suaves |
| **Complementario** | Verde Salvia | `#A8B58A` | Distintivos temáticos, estado de éxito y categorías de crecimiento | Decorativo / Badges |
| **Base** | Blanco Puro | `#FFFFFF` | Tarjetas de historias, modales y fondos elevados | Base de contenedores |
| **Lino Suave** | Fondo Editorial | `#FBF9F7` | Fondo principal de la página | Confort óptico |

> **Nota de Accesibilidad:** Conforme a las pautas WCAG 2.1 AA, el texto sobre blanco se redacta en Azul Marino `#063B73` o `#0B2038`. Los tonos rosa y salvia se emplean como fondos y pastillas con texto de contraste alto (`#8E4B5B` para rosa y `#4F5C38` para salvia).

### Degradado de Historia (Story Ring)
El isotipo oficial incorpora un degradado característico de Rosa Empolvado a Azul Marino:
```css
background: linear-gradient(135deg, #D996A6 0%, #063B73 80%);
```

---

## 3. Sistema Tipográfico

Combinación editorial optimizada:

* **Títulos y Citas:** `Merriweather` (Serif con autoridad y calidez literaria).
* **Cuerpo e Interfaz:** `Inter` (Sans-Serif de alta legibilidad en pantallas retina y móviles).

---

## 4. Biblioteca de Componentes Reutilizables

### 4.1. Navbar Sticky
* Anclado en la parte superior con `backdrop-blur`.
* Contiene el logotipo horizontal oficial, enlace "Sobre Mí" y botón de acceso Google/Admin.

### 4.2. Perfil Editorial (Instagram Style Header)
* Avatar con anillo de historia degradado oficial (`.story-ring`).
* Nombre oficial con distintivo de verificación.
* Métricas en tiempo real de historias publicadas, lectores y trayectoria.

### 4.3. Tarjeta de Historia (Feed Card)
* Cabecera con avatar, tiempo relativo ("hace 2 h") y badge de categoría.
* Título opcional en `Merriweather`.
* Layout de multimedia inteligente:
  * 1 imagen: ancho completo responsivo.
  * 2–4 imágenes: cuadrícula tipo collage.
  * 5+ imágenes: carrusel interactivo con puntos indicadores.
  * Al hacer clic en cualquier imagen, se abre el **Lightbox** en pantalla completa.
* Barra de acción: corazón me gusta interactivo, botón de comentarios y compartir (Web Share API).
* Caja de comentarios plegable con input interactivo para usuarios autenticados con Google.

### 4.4. Editor Administrativo
* Integración con CKEditor 5.
* Gestor de imágenes con arrastre (Drag & Drop) y conversión automática a WebP.
* Configuración de estado, fecha programada, categoría y simulador de Google SEO.

