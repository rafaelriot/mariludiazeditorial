<?php if (! empty($pinnedPosts)): ?>
<div class="mb-8"
     x-data="featuredCarousel(<?= count($pinnedPosts) ?>)"
     @mouseenter="stopTimer()"
     @mouseleave="startTimer()"
     @touchstart="stopTimer()"
     @touchend="startTimer()">
    <div class="flex items-center justify-between mb-3 px-1">
        <div class="flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-[#D996A6] animate-pulse"></span>
            <h2 class="text-xs font-bold uppercase tracking-wider text-[#063B73]">Historias Destacadas</h2>
        </div>
        <div class="flex items-center gap-1.5" x-show="total > 1">
            <button @click="prev()"
                    class="w-7 h-7 rounded-full bg-white border border-gray-200 text-[#063B73] hover:bg-[#063B73] hover:text-white flex items-center justify-center transition-all text-xs shadow-sm cursor-pointer"
                    aria-label="Historia anterior">
                ‹
            </button>
            <button @click="next()"
                    class="w-7 h-7 rounded-full bg-white border border-gray-200 text-[#063B73] hover:bg-[#063B73] hover:text-white flex items-center justify-center transition-all text-xs shadow-sm cursor-pointer"
                    aria-label="Siguiente historia">
                ›
            </button>
        </div>
    </div>

    <!-- Contenedor Deslizante con scroll-snap -->
    <div x-ref="carousel"
         @scroll.debounce.100ms="onScroll()"
         class="flex items-stretch gap-4 overflow-x-auto no-scrollbar snap-x snap-mandatory py-1 scroll-smooth">
        <?php foreach ($pinnedPosts as $idx => $p): ?>
            <?php
                $cover = $p->getCover();
                $tone = $p->category ? $p->category->getToneClasses() : null;
                $url = base_url('historia/' . $p->slug);
            ?>
            <div class="w-full flex-shrink-0 snap-center bg-white rounded-2xl border border-[#063B73]/10 overflow-hidden shadow-sm hover:shadow-md transition-shadow flex flex-col sm:flex-row group">
                <!-- Imagen de Portada -->
                <a href="<?= $url ?>" class="sm:w-2/5 aspect-[16/10] sm:aspect-auto bg-gray-100 overflow-hidden relative block min-h-[160px]">
                    <?php if ($cover): ?>
                        <?= render_media_picture($cover, 'sm', 'w-full h-full object-cover group-hover:scale-105 transition-transform duration-500') ?>
                    <?php else: ?>
                        <div class="w-full h-full bg-gradient-to-br from-[#063B73] to-[#0D4E96] flex items-center justify-center text-white/50 text-xs font-serif p-4 text-center">
                            Marilú Díaz
                        </div>
                    <?php endif; ?>
                    <span class="absolute top-3 left-3 bg-[#063B73] text-white text-[10px] font-semibold uppercase tracking-wider px-2.5 py-0.5 rounded-full shadow">
                        Destacada
                    </span>
                </a>

                <!-- Contenido -->
                <div class="sm:w-3/5 p-5 sm:p-6 flex flex-col justify-between">
                    <div>
                        <?php if ($p->category): ?>
                            <span class="inline-block text-[10px] font-semibold uppercase tracking-wider <?= $tone['text'] ?? 'text-[#063B73]' ?> mb-1">
                                <?= esc($p->category->name) ?>
                            </span>
                        <?php endif; ?>

                        <h3 class="font-serif font-bold text-base sm:text-lg text-[#063B73] leading-snug line-clamp-2 mb-2 group-hover:text-[#094E96] transition-colors">
                            <a href="<?= $url ?>">
                                <?= esc($p->title ?: ($p->excerpt ?: 'Historia destacada')) ?>
                            </a>
                        </h3>

                        <p class="text-xs text-[#5A6776] line-clamp-2 leading-relaxed">
                            <?= esc($p->excerpt ?: strip_tags($p->content)) ?>
                        </p>
                    </div>

                    <div class="flex items-center justify-between pt-3 mt-3 border-t border-gray-100 text-[11px] text-[#5A6776]">
                        <span><?= esc($p->getRelativeTime()) ?></span>
                        <a href="<?= $url ?>" class="font-semibold text-[#063B73] hover:text-[#D996A6] flex items-center gap-1 transition-colors">
                            <span>Leer historia</span>
                            <span>&rarr;</span>
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Indicadores de puntos (dots) interactivos -->
    <?php if (count($pinnedPosts) > 1): ?>
        <div class="flex items-center justify-center gap-2 mt-3">
            <?php for ($i = 0; $i < count($pinnedPosts); $i++): ?>
                <button @click="goTo(<?= $i ?>)"
                        class="h-2 rounded-full transition-all duration-300 cursor-pointer"
                        :class="currentSlide === <?= $i ?> ? 'w-6 bg-[#063B73]' : 'w-2 bg-gray-300 hover:bg-gray-400'"
                        aria-label="Ir a diapositiva <?= $i + 1 ?>"></button>
            <?php endfor; ?>
        </div>
    <?php endif; ?>
</div>
<?php endif; ?>
