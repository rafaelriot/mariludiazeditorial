<?php if (empty($posts)): ?>
    <div class="bg-white rounded-2xl p-12 text-center border border-[#063B73]/10 shadow-sm my-6">
        <div class="w-16 h-16 rounded-full bg-[#063B73]/5 text-[#063B73] mx-auto flex items-center justify-center mb-4">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path></svg>
        </div>
        <h3 class="font-serif text-lg text-[#063B73] font-bold mb-1">Aún no hay publicaciones</h3>
        <p class="text-sm text-[#5A6776]">Pronto Marilú compartirá nuevas historias y reflexiones en este espacio.</p>
    </div>
<?php else: ?>
    <?php foreach ($posts as $index => $post): ?>
        <?php
            $galleryCount = count($post->gallery);
            $toneClasses = $post->category ? $post->category->getToneClasses() : null;
            $postUrl = base_url('historia/' . $post->slug);
            $isEager = ($index === 0);
        ?>
        <article class="bg-white rounded-2xl border border-[#063B73]/10 shadow-sm overflow-hidden mb-6 transition-card hover:shadow-md"
                 x-data="feedPost({
                     postId: <?= $post->id ?>,
                     isLiked: <?= $post->has_liked ? 'true' : 'false' ?>,
                     likesCount: <?= (int) $post->likes_count ?>,
                     commentsCount: <?= (int) $post->comments_count ?>,
                     totalSlides: <?= max(1, $galleryCount) ?>,
                     comments: <?= json_encode(array_map(fn($c) => [
                         'id' => $c->id,
                         'author_name' => esc($c->author_name),
                         'avatar' => $c->author_avatar,
                         'content' => esc($c->content),
                         'time' => $c->getRelativeTime(),
                     ], $post->comments)) ?>
                 })">

            <!-- CABECERA DE LA HISTORIA (ESTILO INSTAGRAM) -->
            <div class="p-4 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <!-- Avatar con degradado corporativo estilo Story -->
                    <div class="story-ring">
                        <div class="story-ring-inner">
                            <img src="<?= base_url('assets/brand/isotipo.png') ?>" alt="Marilú Díaz" class="w-9 h-9 rounded-full object-cover">
                        </div>
                    </div>
                    <div>
                        <div class="flex items-center gap-1.5">
                            <span class="font-semibold text-sm text-[#0B2038]">Marilú Díaz</span>
                            <!-- Sello de verificación -->
                            <svg class="w-3.5 h-3.5 text-[#063B73]" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                            </svg>
                        </div>
                        <div class="flex items-center gap-2 text-xs text-[#5A6776]">
                            <span><?= esc($post->getRelativeTime()) ?></span>
                            <?php if ($post->is_pinned): ?>
                                <span>•</span>
                                <span class="text-[#063B73] font-medium flex items-center gap-0.5">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M5 4a2 2 0 012-2h6a2 2 0 012 2v14l-5-2.5L5 18V4z"></path></svg>
                                    Fijada
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Categoría con pastilla de tono -->
                <?php if ($post->category): ?>
                    <a href="<?= base_url('categoria/' . $post->category->slug) ?>" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium <?= $toneClasses['bg'] ?> <?= $toneClasses['text'] ?> hover:opacity-85 transition-opacity">
                        <span class="w-1.5 h-1.5 rounded-full <?= $toneClasses['dot'] ?>"></span>
                        <?= esc($post->category->name) ?>
                    </a>
                <?php endif; ?>
            </div>

            <!-- CONTENIDO MULTIMEDIA (FOTOGRAFÍAS DE LA HISTORIA) -->
            <?php if ($galleryCount === 1): ?>
                <!-- 1 Imagen: Ancho completo responsivo -->
                <div class="relative bg-gray-100 cursor-pointer overflow-hidden aspect-[4/3] sm:aspect-[16/10]"
                     @click="openModal('<?= $post->gallery[0]->getUrl('lg') ?>', '<?= esc($post->gallery[0]->alt_text) ?>')">
                    <?= render_media_picture($post->gallery[0], 'md', 'w-full h-full object-cover hover:scale-105 transition-transform duration-500', $isEager) ?>
                </div>

            <?php elseif ($galleryCount >= 2 && $galleryCount <= 4): ?>
                <!-- 2 a 4 Imágenes: Cuadrícula limpia tipo collage -->
                <div class="grid <?= $galleryCount === 2 ? 'grid-cols-2' : ($galleryCount === 3 ? 'grid-cols-2' : 'grid-cols-2') ?> gap-1 bg-gray-100">
                    <?php foreach ($post->gallery as $gIdx => $gItem): ?>
                        <div class="relative bg-gray-200 cursor-pointer overflow-hidden aspect-square <?= ($galleryCount === 3 && $gIdx === 0) ? 'row-span-2 aspect-auto' : '' ?>"
                             @click="openModal('<?= $gItem->getUrl('lg') ?>', '<?= esc($gItem->alt_text) ?>')">
                            <?= render_media_picture($gItem, 'sm', 'w-full h-full object-cover hover:scale-105 transition-transform duration-300', $isEager) ?>
                        </div>
                    <?php endforeach; ?>
                </div>

            <?php elseif ($galleryCount >= 5): ?>
                <!-- 5 o más Imágenes: Carrusel interactivo con Alpine.js -->
                <div class="relative bg-gray-100 overflow-hidden aspect-[4/3] sm:aspect-[16/10]">
                    <?php foreach ($post->gallery as $gIdx => $gItem): ?>
                        <div x-show="activeSlide === <?= $gIdx ?>"
                             x-transition:enter="transition ease-out duration-300"
                             x-transition:enter-start="opacity-0 transform translate-x-4"
                             x-transition:enter-end="opacity-100 transform translate-x-0"
                             class="absolute inset-0 cursor-pointer"
                             @click="openModal('<?= $gItem->getUrl('lg') ?>', '<?= esc($gItem->alt_text) ?>')">
                            <?= render_media_picture($gItem, 'md', 'w-full h-full object-cover', $isEager) ?>
                        </div>
                    <?php endforeach; ?>

                    <!-- Botones de Navegación del Carrusel -->
                    <button @click.stop="prevSlide()" class="absolute left-2 top-1/2 -translate-y-1/2 w-8 h-8 rounded-full bg-black/40 hover:bg-black/60 text-white flex items-center justify-center transition-colors" aria-label="Anterior">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"></path></svg>
                    </button>
                    <button @click.stop="nextSlide()" class="absolute right-2 top-1/2 -translate-y-1/2 w-8 h-8 rounded-full bg-black/40 hover:bg-black/60 text-white flex items-center justify-center transition-colors" aria-label="Siguiente">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path></svg>
                    </button>

                    <!-- Puntos indicadores -->
                    <div class="absolute bottom-3 left-0 right-0 flex items-center justify-center gap-1.5 z-10 pointer-events-none">
                        <?php for ($d = 0; $d < $galleryCount; $d++): ?>
                            <span class="w-2 h-2 rounded-full transition-all duration-200"
                                  :class="activeSlide === <?= $d ?> ? 'bg-white w-4' : 'bg-white/50'"></span>
                        <?php endfor; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- BARRA DE INTERACCIÓN (CORAZÓN, COMENTARIOS, COMPARTIR) -->
            <div class="px-4 pt-3 flex items-center justify-between text-[#0B2038]">
                <div class="flex items-center gap-4">
                    <!-- Botón Me Gusta (Corazón interactivo con animación) -->
                    <button @click="toggleLike()"
                            class="flex items-center gap-1.5 text-sm transition-transform active:scale-125 focus:outline-none"
                            :class="liked ? 'text-[#D996A6]' : 'text-[#5A6776] hover:text-[#063B73]'">
                        <svg class="w-6 h-6 transition-all"
                             :class="{ 'scale-125': heartPop }"
                             :fill="liked ? '#D996A6' : 'none'"
                             stroke="currentColor"
                             stroke-width="1.8"
                             viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                        </svg>
                        <span class="font-semibold text-xs text-[#0B2038]" x-text="likesCount"></span>
                    </button>

                    <!-- Botón Comentarios -->
                    <button @click="toggleComments()" class="flex items-center gap-1.5 text-sm text-[#5A6776] hover:text-[#063B73] transition-colors focus:outline-none">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                        </svg>
                        <span class="font-semibold text-xs text-[#0B2038]" x-text="commentsCount"></span>
                    </button>

                    <!-- Botón Compartir -->
                    <button @click="sharePost('<?= $postUrl ?>', '<?= esc($post->title ?: 'Historia de Marilú Díaz') ?>')" class="text-[#5A6776] hover:text-[#063B73] transition-colors focus:outline-none" title="Compartir historia">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"></path>
                        </svg>
                    </button>
                </div>

                <div class="text-xs text-[#5A6776]">
                    <span><?= (int) $post->reading_minutes ?> min de lectura</span>
                </div>
            </div>

            <!-- CUERPO DE TEXTO DE LA HISTORIA -->
            <div class="px-4 py-3">
                <?php if ($post->title): ?>
                    <h2 class="font-serif text-lg font-bold text-[#063B73] leading-snug mb-1">
                        <a href="<?= $postUrl ?>" class="hover:text-[#094E96] transition-colors">
                            <?= esc($post->title) ?>
                        </a>
                    </h2>
                <?php endif; ?>

                <div class="text-sm text-[#1B242D] leading-relaxed">
                    <?php if ($post->excerpt): ?>
                        <p class="inline"><?= esc($post->excerpt) ?></p>
                        <a href="<?= $postUrl ?>" class="text-xs font-semibold text-[#063B73] hover:text-[#D996A6] ml-1">
                            Leer completo &rarr;
                        </a>
                    <?php else: ?>
                        <?= $post->content ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- SECCIÓN DESPLEGABLE DE COMENTARIOS -->
            <div class="border-t border-gray-100 px-4 py-3 bg-[#FBF9F7]/50" x-show="showComments" x-cloak x-transition>
                <h4 class="text-xs font-semibold uppercase tracking-wider text-[#5A6776] mb-3">Comentarios</h4>

                <!-- Lista de Comentarios -->
                <div class="space-y-2.5 max-h-60 overflow-y-auto no-scrollbar mb-3">
                    <template x-for="c in comments" :key="c.id">
                        <div class="flex items-start gap-2.5 text-xs bg-white p-2.5 rounded-xl border border-gray-100">
                            <template x-if="c.avatar">
                                <img :src="c.avatar" :alt="c.author_name" class="w-6 h-6 rounded-full object-cover">
                            </template>
                            <template x-if="!c.avatar">
                                <div class="w-6 h-6 rounded-full bg-[#063B73]/10 text-[#063B73] flex items-center justify-center font-bold text-[10px]" x-text="c.author_name.charAt(0)"></div>
                            </template>
                            <div class="flex-1">
                                <div class="flex items-center justify-between">
                                    <span class="font-semibold text-[#0B2038]" x-text="c.author_name"></span>
                                    <span class="text-[10px] text-gray-400" x-text="c.time"></span>
                                </div>
                                <p class="text-[#434750] mt-0.5" x-text="c.content"></p>
                            </div>
                        </div>
                    </template>
                    <div x-show="comments.length === 0" class="text-xs text-gray-400 text-center py-2">
                        Sé la primera persona en dejar un comentario.
                    </div>
                </div>

                <!-- Input para comentar -->
                <?php if (auth()->loggedIn()): ?>
                    <form @submit.prevent="submitComment()" class="flex items-center gap-2">
                        <input type="text"
                               x-model="newCommentText"
                               placeholder="Escribe un comentario..."
                               class="flex-1 bg-white border border-gray-200 rounded-full px-3.5 py-1.5 text-xs text-[#0B2038] focus:outline-none focus:border-[#063B73]"
                               required>
                        <button type="submit"
                                :disabled="isSubmittingComment || !newCommentText.trim()"
                                class="bg-[#063B73] hover:bg-[#094E96] disabled:opacity-50 text-white font-medium text-xs px-3.5 py-1.5 rounded-full transition-colors flex items-center gap-1">
                            <span>Publicar</span>
                        </button>
                    </form>
                <?php else: ?>
                    <div class="bg-white p-3 rounded-xl border border-gray-100 flex items-center justify-between">
                        <span class="text-xs text-[#5A6776]">Inicia sesión con tu cuenta de Google para comentar</span>
                        <a href="<?= base_url('auth/google?return=' . urlencode($postUrl)) ?>" class="inline-flex items-center gap-1 text-xs font-semibold text-[#063B73] hover:text-[#D996A6]">
                            <span>Entrar con Gmail</span> &rarr;
                        </a>
                    </div>
                <?php endif; ?>
            </div>

        </article>
    <?php endforeach; ?>
<?php endif; ?>

