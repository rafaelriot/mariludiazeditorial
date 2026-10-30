// Script interactivo para el panel administrativo de Marilú Díaz

document.addEventListener('DOMContentLoaded', () => {
  // Generador dinámico de Slugs a partir del título
  const titleInput = document.getElementById('post-title-input');
  const slugInput = document.getElementById('post-slug-input');

  if (titleInput && slugInput) {
    let manualSlugEdit = slugInput.value.trim().length > 0;

    slugInput.addEventListener('input', () => {
      manualSlugEdit = slugInput.value.trim().length > 0;
    });

    titleInput.addEventListener('input', () => {
      if (!manualSlugEdit) {
        const slug = titleInput.value
          .toLowerCase()
          .normalize('NFD')
          .replace(/[\u0300-\u036f]/g, '')
          .replace(/[^a-z0-9]+/g, '-')
          .replace(/(^-|-$)+/g, '');
        slugInput.value = slug;
      }
    });
  }

  // Contador de caracteres para SEO
  const metaTitle = document.getElementById('meta_title');
  const metaDesc = document.getElementById('meta_description');

  if (metaTitle) {
    const counter = document.getElementById('meta-title-counter');
    metaTitle.addEventListener('input', () => {
      if (counter) counter.textContent = `${metaTitle.value.length} / 70`;
    });
  }

  if (metaDesc) {
    const counter = document.getElementById('meta-desc-counter');
    metaDesc.addEventListener('input', () => {
      if (counter) counter.textContent = `${metaDesc.value.length} / 160`;
    });
  }
});
