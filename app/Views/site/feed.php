<?= $this->extend('layouts/site') ?>

<?= $this->section('content') ?>

<!-- 1. PERFIL EDITORIAL DE MARILÚ DÍAZ (ESTILO INSTAGRAM / SUBSTACK) -->
<div class="bg-white rounded-3xl border border-[#063B73]/10 p-6 sm:p-8 shadow-sm mb-6">
    <div class="flex flex-col sm:flex-row items-center sm:items-start gap-6 text-center sm:text-left">
        <!-- Avatar con Anillo de Historia (Degradado Oficial Rosa a Marino) -->
        <div class="story-ring p-1 flex-shrink-0">
            <div class="story-ring-inner p-1">
                <img src="<?= base_url('assets/brand/isotipo.png') ?>" alt="Marilú Díaz" class="w-20 h-20 sm:w-24 sm:h-24 rounded-full object-cover shadow-inner">
            </div>
        </div>

        <!-- Biografía y Propuesta de Valor -->
        <div class="flex-1">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-2">
                <div>
                    <h1 class="font-serif text-xl sm:text-2xl font-bold text-[#063B73] flex items-center justify-center sm:justify-start gap-1.5">
                        <span>Marilú Díaz</span>
                        <svg class="w-4 h-4 text-[#063B73]" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                        </svg>
                    </h1>
                    <p class="text-xs text-[#5A6776]">Pensamiento & Alta Dirección • Autora & Mentora</p>
                </div>

                <a href="<?= base_url('sobre-mi') ?>" class="inline-flex items-center justify-center gap-1 text-xs font-semibold px-4 py-2 bg-[#063B73]/5 hover:bg-[#063B73] text-[#063B73] hover:text-white rounded-full transition-colors border border-[#063B73]/20">
                    <span>Conoce mi trayectoria</span>
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </a>
            </div>

            <!-- Manifiesto corto -->
            <p class="text-sm text-[#434750] leading-relaxed max-w-xl mx-auto sm:mx-0">
                Transformando liderazgos, negocios y mentalidades con propósito. Un espacio editorial donde la claridad supera al ruido.
            </p>

            <!-- Métricas sutiles -->
            <div class="flex items-center justify-center sm:justify-start gap-6 pt-4 mt-2 border-t border-gray-100 text-xs text-[#5A6776]">
                <div>
                    <span class="font-bold text-[#0B2038]"><?= count($posts) ?>+</span> historias
                </div>
                <div>
                    <span class="font-bold text-[#0B2038]">15k+</span> lectores
                </div>
                <div>
                    <span class="font-bold text-[#0B2038]">12+</span> años de liderazgo
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 2. HISTORIAS DESTACADAS EN CARRUSEL (SUPERIOR) -->
<?= view('site/partials/featured_carousel', ['pinnedPosts' => $pinnedPosts ?? []]) ?>

<!-- 3. BUSCADOR INTELIGENTE EN TIEMPO REAL -->
<?= view('site/partials/search_bar', ['currentSearch' => $currentSearch ?? '']) ?>

<!-- 4. PASTILLAS DE CATEGORÍAS FILTRABLES (SCROLL HORIZONTAL) -->
<?php if (! empty($categories)): ?>
    <div class="flex items-center gap-2 overflow-x-auto no-scrollbar pb-3 mb-4">
        <a href="<?= base_url() ?>"
           class="flex-shrink-0 px-4 py-1.5 rounded-full text-xs font-medium transition-all <?= (! $activeCategory && ! $activeTag) ? 'bg-[#063B73] text-white shadow-sm' : 'bg-white text-[#5A6776] border border-gray-200 hover:border-[#063B73]' ?>">
            Todas
        </a>
        <?php foreach ($categories as $cat): ?>
            <?php
                $isActive = $activeCategory && $activeCategory->id === $cat->id;
                $tone = $cat->getToneClasses();
            ?>
            <a href="<?= base_url('categoria/' . $cat->slug) ?>"
               class="flex-shrink-0 flex items-center gap-1.5 px-4 py-1.5 rounded-full text-xs font-medium transition-all <?= $isActive ? 'bg-[#063B73] text-white shadow-sm' : 'bg-white text-[#5A6776] border border-gray-200 hover:border-[#063B73]' ?>">
                <span class="w-1.5 h-1.5 rounded-full <?= $tone['dot'] ?>"></span>
                <span><?= esc($cat->name) ?></span>
            </a>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<!-- Indicador de filtro por etiqueta activa -->
<?php if ($activeTag): ?>
    <div class="mb-4 flex items-center justify-between bg-white px-4 py-2 rounded-2xl border border-gray-200 text-xs">
        <span class="text-[#5A6776]">Filtrado por etiqueta: <strong class="text-[#063B73]">#<?= esc($activeTag->name) ?></strong></span>
        <a href="<?= base_url() ?>" class="text-xs text-red-600 hover:underline">Quitar filtro</a>
    </div>
<?php endif; ?>

<!-- 5. FEED DE HISTORIAS DE UNA SOLA COLUMNA (ESTILO INSTAGRAM) -->
<div id="feed-container">
    <?= view('site/partials/feed_items', ['posts' => $posts]) ?>
</div>

<!-- 6. BOLETÍN NEWSLETTER ESTILO SUBSTACK -->
<?= view('site/partials/newsletter_box') ?>

<!-- 4. CARGAR MÁS / SCROLL INFINITO PROGRESIVO -->
<?php if ($hasMore): ?>
    <div class="py-6 text-center"
         x-data="{
             page: <?= $nextPage ?>,
             loading: false,
             hasMore: true,
             loadNext() {
                 if (this.loading || !this.hasMore) return;
                 this.loading = true;
                 fetch('<?= current_url() ?>?page=' + this.page, {
                     headers: { 'X-Requested-With': 'XMLHttpRequest' }
                 })
                 .then(res => res.text())
                 .then(html => {
                     this.loading = false;
                     if (html.trim().length > 0) {
                         document.getElementById('feed-container').insertAdjacentHTML('beforeend', html);
                         this.page++;
                     } else {
                         this.hasMore = false;
                     }
                 })
                 .catch(() => this.loading = false);
             }
         }">
        <button @click="loadNext()"
                x-show="hasMore"
                :disabled="loading"
                class="inline-flex items-center gap-2 px-6 py-2.5 bg-white border border-[#063B73]/20 hover:border-[#063B73] text-[#063B73] text-xs font-semibold rounded-full shadow-sm hover:shadow transition-all">
            <span x-text="loading ? 'Cargando historias...' : 'Ver más historias'"></span>
            <svg x-show="!loading" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
        </button>
    </div>
<?php endif; ?>

<?= $this->endSection() ?>

