<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<form id="post-form"
      action="<?= $isEdit ? base_url('admin/historias/' . $post->id) : base_url('admin/historias') ?>"
      method="post">
    <?= csrf_field() ?>

    <!-- Barra Superior de Acciones de Publicación -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6 bg-white p-4 rounded-2xl border border-gray-200 shadow-sm">
        <div>
            <h1 class="font-serif text-lg font-bold text-[#063B73]">
                <?= $isEdit ? 'Editar Historia' : 'Crear Nueva Historia' ?>
            </h1>
            <p class="text-xs text-[#5A6776]">Publica historias, ensayos y reflexiones con galería fotográfica</p>
        </div>

        <div class="flex items-center gap-2 w-full sm:w-auto">
            <a href="<?= base_url('admin/historias') ?>" class="px-4 py-2 border border-gray-200 text-xs font-semibold rounded-xl text-[#5A6776] hover:bg-gray-50 transition-colors">
                Descartar
            </a>
            <button type="submit" name="status" value="draft" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-xs font-semibold rounded-xl text-[#0B2038] transition-colors">
                Guardar Borrador
            </button>
            <button type="submit" name="status" value="published" class="px-5 py-2 bg-[#063B73] hover:bg-[#094E96] text-xs font-semibold rounded-xl text-white shadow-sm transition-colors flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                <span>Publicar Ahora</span>
            </button>
        </div>
    </div>

    <!-- Rejilla Principal de 2 Columnas (70% Editor Creativo, 30% Configuración) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

        <!-- COLUMNA IZQUIERDA: CANVAS CREATIVO -->
        <div class="lg:col-span-8 space-y-6">

            <!-- Título y Slug -->
            <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm space-y-4">
                <div>
                    <label class="block text-xs font-bold text-[#063B73] uppercase tracking-wider mb-1.5">
                        Título de la Historia <span class="text-gray-400 font-normal lowercase">(opcional para notas breves)</span>
                    </label>
                    <input type="text"
                           name="title"
                           id="post-title-input"
                           value="<?= old('title', $post->title ?? '') ?>"
                           placeholder="Escribe un título memorable para tu historia..."
                           class="w-full text-base sm:text-lg font-serif font-bold text-[#063B73] bg-[#F8F9FA] focus:bg-white border border-gray-200 focus:border-[#063B73] rounded-xl px-4 py-3 focus:outline-none transition-all">
                </div>

                <!-- Slug Permanente -->
                <div class="flex items-center gap-2 bg-[#F8F9FA] p-2.5 rounded-xl border border-gray-200 text-xs text-[#5A6776]">
                    <span class="font-mono text-gray-400"><?= base_url('historia/') ?></span>
                    <input type="text"
                           name="slug"
                           id="post-slug-input"
                           value="<?= old('slug', $post->slug ?? '') ?>"
                           placeholder="enlace-permanente"
                           class="flex-1 bg-transparent border-0 font-mono text-[#0B2038] focus:outline-none p-0 text-xs"
                           required>
                </div>

                <!-- Subtítulo / Extracto -->
                <div>
                    <label class="block text-xs font-bold text-[#5A6776] uppercase tracking-wider mb-1.5">
                        Extracto / Resumen para el Feed
                    </label>
                    <textarea name="excerpt"
                              rows="2"
                              placeholder="Una síntesis reflexiva para acompañar en el feed..."
                              class="w-full bg-[#F8F9FA] focus:bg-white border border-gray-200 focus:border-[#063B73] rounded-xl p-3 text-xs text-[#0B2038] focus:outline-none transition-all"><?= old('excerpt', $post->excerpt ?? '') ?></textarea>
                </div>
            </div>

            <!-- Editor de Contenido Rico CKEditor 5 -->
            <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm">
                <label class="block text-xs font-bold text-[#063B73] uppercase tracking-wider mb-2">
                    Contenido de la Historia
                </label>
                <textarea name="content" id="editor" class="w-full"><?= old('content', $post->content ?? '') ?></textarea>
            </div>

            <!-- GESTOR DE GALERÍA MULTIMEDIA (ARRASTRAR, SUBIR, REORDENAR, WEBP) -->
            <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm" id="gallery-manager">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="font-serif font-bold text-sm text-[#063B73]">Fotografías de la Historia</h3>
                        <p class="text-xs text-[#5A6776]">Agrega de 1 a 10 imágenes. Se convierten automáticamente a WebP de alta velocidad.</p>
                    </div>
                    <span class="px-2.5 py-1 rounded-full text-[11px] font-semibold bg-[#A8B58A]/20 text-[#4F5C38]">
                        WebP Auto-Optimizado
                    </span>
                </div>

                <!-- Zona de Arrastre (Dropzone) -->
                <div id="dropzone"
                     class="border-2 border-dashed border-gray-300 hover:border-[#063B73] rounded-2xl p-6 text-center cursor-pointer transition-colors bg-[#F8F9FA] hover:bg-[#063B73]/5">
                    <input type="file" id="file-input" multiple accept="image/jpeg,image/png,image/webp,image/gif" class="hidden">
                    <svg class="w-10 h-10 mx-auto text-[#063B73]/60 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    <p class="text-xs font-semibold text-[#0B2038]">Haz clic para subir o arrastra tus imágenes aquí</p>
                    <p class="text-[11px] text-[#5A6776] mt-1">JPG, PNG o WebP hasta 10 MB cada una</p>
                </div>

                <!-- Contenedor Oculto de IDs de Galería -->
                <input type="hidden" name="gallery_ids" id="gallery-ids" value="<?= esc(implode(',', array_map(fn($m) => $m->id, $gallery))) ?>">

                <!-- Cuadrícula de Previsualización de Imágenes Subidas -->
                <div id="gallery-preview" class="grid grid-cols-2 sm:grid-cols-4 gap-3 mt-4">
                    <?php foreach ($gallery as $media): ?>
                        <div class="relative group rounded-xl overflow-hidden bg-gray-100 border border-gray-200 aspect-square gallery-item" data-id="<?= $media->id ?>">
                            <img src="<?= $media->getUrl('thumb') ?>" alt="<?= esc($media->alt_text) ?>" class="w-full h-full object-cover">
                            <button type="button" onclick="removeGalleryItem(<?= $media->id ?>)" class="absolute top-1.5 right-1.5 w-6 h-6 rounded-full bg-black/60 hover:bg-red-600 text-white flex items-center justify-center text-xs transition-colors" title="Eliminar imagen">
                                ✕
                            </button>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

        </div>

        <!-- COLUMNA DERECHA: CONFIGURACIÓN, CATEGORÍAS & SEO -->
        <div class="lg:col-span-4 space-y-6">

            <!-- Estado y Programación -->
            <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm space-y-4">
                <h3 class="font-serif font-bold text-sm text-[#063B73]">Estado & Calendario</h3>

                <div>
                    <label class="block text-xs font-semibold text-[#0B2038] mb-1.5">Estado</label>
                    <select name="status" class="w-full bg-[#F8F9FA] border border-gray-200 rounded-xl px-3 py-2 text-xs text-[#0B2038] focus:outline-none focus:border-[#063B73]">
                        <option value="published" <?= (old('status', $post->status ?? 'published') === 'published') ? 'selected' : '' ?>>Publicada en el Feed</option>
                        <option value="draft" <?= (old('status', $post->status ?? '') === 'draft') ? 'selected' : '' ?>>Borrador privado</option>
                        <option value="scheduled" <?= (old('status', $post->status ?? '') === 'scheduled') ? 'selected' : '' ?>>Programar publicación</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-[#0B2038] mb-1.5">Fecha de publicación</label>
                    <input type="datetime-local"
                           name="published_at"
                           value="<?= old('published_at', isset($post->published_at) ? $post->published_at->format('Y-m-d\TH:i') : date('Y-m-d\TH:i')) ?>"
                           class="w-full bg-[#F8F9FA] border border-gray-200 rounded-xl px-3 py-2 text-xs text-[#0B2038] focus:outline-none focus:border-[#063B73]">
                </div>

                <!-- Interruptor Fijar en la parte superior -->
                <div class="pt-2 border-t border-gray-100 flex items-center justify-between">
                    <div>
                        <span class="text-xs font-semibold text-[#0B2038]">Fijar arriba</span>
                        <p class="text-[11px] text-[#5A6776]">Mantener en la cima del feed</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="is_pinned" value="1" class="sr-only peer" <?= old('is_pinned', $post->is_pinned ?? false) ? 'checked' : '' ?>>
                        <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-[#063B73]"></div>
                    </label>
                </div>
            </div>

            <!-- Categoría -->
            <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm space-y-3">
                <h3 class="font-serif font-bold text-sm text-[#063B73]">Categoría Editorial</h3>

                <select name="category_id" class="w-full bg-[#F8F9FA] border border-gray-200 rounded-xl px-3 py-2 text-xs text-[#0B2038] focus:outline-none focus:border-[#063B73]">
                    <option value="">Sin categoría</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat->id ?>" <?= (old('category_id', $post->category_id ?? '') == $cat->id) ? 'selected' : '' ?>>
                            <?= esc($cat->name) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Etiquetas / Tags -->
            <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm space-y-3">
                <h3 class="font-serif font-bold text-sm text-[#063B73]">Etiquetas & Descriptores</h3>
                <p class="text-[11px] text-[#5A6776]">Separa los temas con comas.</p>
                <input type="text"
                       name="tags"
                       value="<?= old('tags', isset($post->tags) ? implode(', ', array_map(fn($t) => $t->name, $post->tags)) : '') ?>"
                       placeholder="Liderazgo, Criterio, Equipos"
                       class="w-full bg-[#F8F9FA] border border-gray-200 rounded-xl px-3.5 py-2 text-xs text-[#0B2038] focus:outline-none focus:border-[#063B73]">
            </div>

            <!-- SEO & Meta Tags -->
            <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm space-y-4">
                <h3 class="font-serif font-bold text-sm text-[#063B73]">SEO & Redes Sociales</h3>

                <div>
                    <div class="flex items-center justify-between text-xs text-[#5A6776] mb-1">
                        <label for="meta_title" class="font-semibold">Título SEO</label>
                        <span id="meta-title-counter" class="text-[11px]">0 / 70</span>
                    </div>
                    <input type="text"
                           name="meta_title"
                           id="meta_title"
                           maxlength="70"
                           value="<?= old('meta_title', $post->meta_title ?? '') ?>"
                           placeholder="Título para buscadores..."
                           class="w-full bg-[#F8F9FA] border border-gray-200 rounded-xl px-3 py-2 text-xs text-[#0B2038] focus:outline-none focus:border-[#063B73]">
                </div>

                <div>
                    <div class="flex items-center justify-between text-xs text-[#5A6776] mb-1">
                        <label for="meta_description" class="font-semibold">Descripción SEO</label>
                        <span id="meta-desc-counter" class="text-[11px]">0 / 160</span>
                    </div>
                    <textarea name="meta_description"
                              id="meta_description"
                              maxlength="160"
                              rows="3"
                              placeholder="Breve resumen para Google y redes sociales..."
                              class="w-full bg-[#F8F9FA] border border-gray-200 rounded-xl p-3 text-xs text-[#0B2038] focus:outline-none focus:border-[#063B73]"><?= old('meta_description', $post->meta_description ?? '') ?></textarea>
                </div>
            </div>

        </div>

    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // 1. Inicializar CKEditor 5
    if (document.querySelector('#editor')) {
        ClassicEditor
            .create(document.querySelector('#editor'), {
                toolbar: ['heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote', '|', 'undo', 'redo'],
                heading: {
                    options: [
                        { model: 'paragraph', title: 'Párrafo', class: 'ck-heading_paragraph' },
                        { model: 'heading2', view: 'h2', title: 'Subtítulo Principal', class: 'ck-heading_heading2' },
                        { model: 'heading3', view: 'h3', title: 'Subsección', class: 'ck-heading_heading3' }
                    ]
                }
            })
            .catch(error => {
                console.error(error);
            });
    }

    // 2. Subida de Galería Interactiva con Auto-Conversión WebP
    const dropzone = document.getElementById('dropzone');
    const fileInput = document.getElementById('file-input');
    const previewGrid = document.getElementById('gallery-preview');
    const galleryIdsInput = document.getElementById('gallery-ids');

    let currentIds = galleryIdsInput.value ? galleryIdsInput.value.split(',').filter(Boolean) : [];

    dropzone.addEventListener('click', () => fileInput.click());

    dropzone.addEventListener('dragover', (e) => {
        e.preventDefault();
        dropzone.classList.add('border-[#063B73]', 'bg-[#063B73]/10');
    });

    dropzone.addEventListener('dragleave', () => {
        dropzone.classList.remove('border-[#063B73]', 'bg-[#063B73]/10');
    });

    dropzone.addEventListener('drop', (e) => {
        e.preventDefault();
        dropzone.classList.remove('border-[#063B73]', 'bg-[#063B73]/10');
        if (e.dataTransfer.files.length > 0) {
            handleFiles(e.dataTransfer.files);
        }
    });

    fileInput.addEventListener('change', () => {
        if (fileInput.files.length > 0) {
            handleFiles(fileInput.files);
        }
    });

    function handleFiles(files) {
        for (let file of files) {
            uploadMedia(file);
        }
    }

    function uploadMedia(file) {
        const formData = new FormData();
        formData.append('file', file);

        const csrfName = document.querySelector('meta[name="csrf-token-name"]')?.content || 'csrf_test_name';
        const csrfHash = document.querySelector('meta[name="csrf-token"]')?.content;
        if (csrfHash) formData.append(csrfName, csrfHash);

        fetch('<?= base_url('admin/media') ?>', {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success && data.media) {
                currentIds.push(data.media.id);
                galleryIdsInput.value = currentIds.join(',');

                const item = document.createElement('div');
                item.className = 'relative group rounded-xl overflow-hidden bg-gray-100 border border-gray-200 aspect-square gallery-item';
                item.dataset.id = data.media.id;
                item.innerHTML = `
                    <img src="${data.media.thumb_url}" alt="${data.media.alt_text}" class="w-full h-full object-cover">
                    <button type="button" onclick="removeGalleryItem(${data.media.id})" class="absolute top-1.5 right-1.5 w-6 h-6 rounded-full bg-black/60 hover:bg-red-600 text-white flex items-center justify-center text-xs transition-colors" title="Eliminar imagen">
                        ✕
                    </button>
                `;
                previewGrid.appendChild(item);
            } else {
                alert(data.message || 'Error al procesar la imagen.');
            }
            if (data.csrf_hash) {
                document.querySelectorAll('meta[name="csrf-token"]').forEach(m => m.content = data.csrf_hash);
            }
        })
        .catch(err => console.error(err));
    }

    window.removeGalleryItem = function(mediaId) {
        currentIds = currentIds.filter(id => String(id) !== String(mediaId));
        galleryIdsInput.value = currentIds.join(',');
        const el = previewGrid.querySelector(`[data-id="${mediaId}"]`);
        if (el) el.remove();
    };
});
</script>

<?= $this->endSection() ?>

