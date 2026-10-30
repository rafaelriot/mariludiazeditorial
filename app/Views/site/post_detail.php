<?= $this->extend('layouts/site') ?>

<?= $this->section('content') ?>

<article class="bg-white rounded-3xl border border-[#063B73]/10 shadow-sm p-6 sm:p-10 mb-8"
         x-data="feedPost({
             postId: <?= $post->id ?>,
             isLiked: <?= $post->has_liked ? 'true' : 'false' ?>,
             likesCount: <?= (int) $post->likes_count ?>,
             commentsCount: <?= (int) $post->comments_count ?>,
             totalSlides: <?= max(1, count($post->gallery)) ?>,
             comments: <?= json_encode(array_map(fn($c) => [
                 'id' => $c->id,
                 'author_name' => esc($c->author_name),
                 'avatar' => $c->author_avatar,
                 'content' => esc($c->content),
                 'time' => $c->getRelativeTime(),
             ], $comments)) ?>
         })">

    <!-- Navegación de retorno al feed -->
    <div class="mb-6">
        <a href="<?= base_url() ?>" class="inline-flex items-center gap-1.5 text-xs font-semibold text-[#063B73] hover:text-[#D996A6] transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            <span>Volver al inicio</span>
        </a>
    </div>

    <!-- Metadatos de la historia -->
    <div class="flex items-center justify-between pb-6 border-b border-gray-100">
        <div class="flex items-center gap-3">
            <div class="story-ring">
                <div class="story-ring-inner">
                    <img src="<?= base_url('assets/brand/isotipo.png') ?>" alt="Marilú Díaz" class="w-10 h-10 rounded-full object-cover">
                </div>
            </div>
            <div>
                <h3 class="font-semibold text-sm text-[#0B2038]">Marilú Díaz</h3>
                <p class="text-xs text-[#5A6776]">
                    <?= esc($post->getFormattedDate()) ?> • <?= (int) $post->reading_minutes ?> min de lectura
                </p>
            </div>
        </div>

        <?php if ($post->category): ?>
            <?php $tone = $post->category->getToneClasses(); ?>
            <a href="<?= base_url('categoria/' . $post->category->slug) ?>" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium <?= $tone['bg'] ?> <?= $tone['text'] ?>">
                <span class="w-1.5 h-1.5 rounded-full <?= $tone['dot'] ?>"></span>
                <?= esc($post->category->name) ?>
            </a>
        <?php endif; ?>
    </div>

    <!-- Título Editorial -->
    <?php if ($post->title): ?>
        <h1 class="font-serif text-2xl sm:text-3xl font-bold text-[#063B73] leading-tight my-6">
            <?= esc($post->title) ?>
        </h1>
    <?php endif; ?>

    <!-- Galería Completa de Fotografías -->
    <?php if (! empty($post->gallery)): ?>
        <div class="my-6 space-y-4">
            <?php foreach ($post->gallery as $media): ?>
                <figure class="rounded-2xl overflow-hidden bg-gray-50 border border-gray-100 cursor-pointer"
                        @click="openModal('<?= $media->getUrl('lg') ?>', '<?= esc($media->alt_text) ?>')">
                    <?= render_media_picture($media, 'lg', 'w-full h-auto object-cover') ?>
                    <?php if ($media->caption): ?>
                        <figcaption class="p-3 text-xs text-center text-[#5A6776] bg-white border-t border-gray-50">
                            <?= esc($media->caption) ?>
                        </figcaption>
                    <?php endif; ?>
                </figure>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- Texto de la historia -->
    <div class="prose max-w-none text-[#1B242D] leading-relaxed my-8 text-base">
        <?= $post->content ?>
    </div>

    <!-- ETIQUETAS (TAGS) DE LA HISTORIA -->
    <?php if (! empty($post->tags)): ?>
        <div class="flex items-center flex-wrap gap-2 my-6 pt-4 border-t border-gray-100">
            <span class="text-xs font-semibold text-[#5A6776]">Temas:</span>
            <?php foreach ($post->tags as $t): ?>
                <a href="<?= base_url('etiqueta/' . $t->slug) ?>" class="text-xs px-3 py-1 rounded-full bg-[#FBF9F7] hover:bg-[#063B73] text-[#5A6776] hover:text-white border border-gray-200 transition-colors">
                    #<?= esc($t->name) ?>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- BOTONES DE COMPARTIR EN REDES SOCIALES (1 CLIC) -->
    <?= view('site/partials/share_buttons', ['post' => $post]) ?>

    <!-- Barra de interacción -->
    <div class="pt-6 border-t border-gray-100 flex items-center justify-between">
        <div class="flex items-center gap-4">
            <!-- Botón Me Gusta -->
            <button @click="toggleLike()"
                    class="flex items-center gap-1.5 text-sm transition-transform active:scale-125 focus:outline-none"
                    :class="liked ? 'text-[#D996A6]' : 'text-[#5A6776] hover:text-[#063B73]'">
                <svg class="w-6 h-6" :fill="liked ? '#D996A6' : 'none'" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                </svg>
                <span class="font-semibold text-xs text-[#0B2038]" x-text="likesCount"></span>
            </button>

            <!-- Botón Compartir -->
            <button @click="sharePost('<?= current_url() ?>', '<?= esc($post->title ?: 'Historia de Marilú Díaz') ?>')" class="flex items-center gap-1 text-xs text-[#5A6776] hover:text-[#063B73] transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"></path></svg>
                <span>Compartir</span>
            </button>
        </div>

        <div class="text-xs text-[#5A6776]">
            <?= (int) $post->views_count ?> lecturas
        </div>
    </div>

    <!-- SECCIÓN DE COMENTARIOS DE LA HISTORIA -->
    <div id="comentarios" class="mt-10 pt-8 border-t border-gray-100">
        <h3 class="font-serif text-lg font-bold text-[#063B73] mb-6 flex items-center gap-2">
            <span>Conversación</span>
            <span class="text-xs font-sans font-normal text-[#5A6776] bg-gray-100 px-2 py-0.5 rounded-full" x-text="commentsCount"></span>
        </h3>

        <!-- Formulario para agregar comentario -->
        <?php if (auth()->loggedIn()): ?>
            <form @submit.prevent="submitComment()" class="mb-8">
                <div class="flex items-start gap-3">
                    <img src="<?= session()->get('reader_profile')['avatar'] ?? base_url('assets/brand/isotipo.png') ?>" alt="Tú" class="w-8 h-8 rounded-full object-cover">
                    <div class="flex-1">
                        <textarea x-model="newCommentText"
                                  rows="3"
                                  placeholder="Comparte tu perspectiva sobre esta historia..."
                                  class="w-full bg-[#FBF9F7] border border-gray-200 rounded-2xl p-3.5 text-xs text-[#0B2038] focus:outline-none focus:border-[#063B73] leading-relaxed"
                                  required></textarea>
                        <div class="flex justify-end mt-2">
                            <button type="submit"
                                    :disabled="isSubmittingComment || !newCommentText.trim()"
                                    class="bg-[#063B73] hover:bg-[#094E96] disabled:opacity-50 text-white font-medium text-xs px-4 py-2 rounded-full transition-colors">
                                <span x-text="isSubmittingComment ? 'Publicando...' : 'Publicar comentario'"></span>
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        <?php else: ?>
            <div class="bg-[#FBF9F7] rounded-2xl p-6 text-center border border-dashed border-[#063B73]/20 mb-8">
                <p class="text-xs text-[#5A6776] mb-3">Únete a la conversación con tu cuenta de Google.</p>
                <a href="<?= base_url('auth/google?return=' . urlencode(current_url())) ?>" class="inline-flex items-center gap-2 px-5 py-2.5 bg-white text-[#0B2038] border border-gray-300 hover:border-[#063B73] text-xs font-semibold rounded-full shadow-sm hover:shadow transition-all">
                    <svg class="w-4 h-4" viewBox="0 0 24 24">
                        <path fill="#4285F4" d="M23.745 12.27c0-.7-.06-1.4-.19-2.07H12v4.51h6.6c-.29 1.52-1.14 2.8-2.4 3.66v3.05h3.88c2.27-2.09 3.665-5.17 3.665-9.15z"/>
                        <path fill="#34A853" d="M12 24c3.24 0 5.95-1.08 7.93-2.91l-3.88-3.05c-1.08.72-2.45 1.16-4.05 1.16-3.12 0-5.77-2.1-6.72-4.93H1.26v3.15C3.27 21.36 7.35 24 12 24z"/>
                        <path fill="#FBBC05" d="M5.28 14.27c-.25-.72-.38-1.49-.38-2.27s.13-1.55.38-2.27V6.58H1.26C.46 8.17 0 9.99 0 12s.46 3.83 1.26 5.42l4.02-3.15z"/>
                        <path fill="#EA4335" d="M12 4.75c1.77 0 3.35.61 4.6 1.8l3.42-3.42C17.95 1.19 15.24 0 12 0 7.35 0 3.27 2.64 1.26 6.58l4.02 3.15c.95-2.83 3.6-4.98 6.72-4.98z"/>
                    </svg>
                    <span>Iniciar sesión con Google para comentar</span>
                </a>
            </div>
        <?php endif; ?>

        <!-- Hilo de comentarios publicados -->
        <div class="space-y-4">
            <template x-for="c in comments" :key="c.id">
                <div class="flex items-start gap-3 p-4 rounded-2xl bg-[#FBF9F7] border border-gray-100">
                    <template x-if="c.avatar">
                        <img :src="c.avatar" :alt="c.author_name" class="w-8 h-8 rounded-full object-cover">
                    </template>
                    <template x-if="!c.avatar">
                        <div class="w-8 h-8 rounded-full bg-[#063B73]/10 text-[#063B73] flex items-center justify-center font-bold text-xs" x-text="c.author_name.charAt(0)"></div>
                    </template>
                    <div class="flex-1">
                        <div class="flex items-center justify-between mb-1">
                            <span class="font-semibold text-xs text-[#0B2038]" x-text="c.author_name"></span>
                            <span class="text-[10px] text-gray-400" x-text="c.time"></span>
                        </div>
                        <p class="text-xs text-[#434750] leading-relaxed" x-text="c.content"></p>
                    </div>
                </div>
            </template>
            <div x-show="comments.length === 0" class="text-center py-6 text-xs text-[#5A6776]">
                Aún no hay comentarios en esta historia.
            </div>
        </div>
    </div>

</article>

<!-- HISTORIAS RELACIONADAS -->
<?php if (! empty($relatedPosts)): ?>
    <div class="my-10">
        <div class="flex items-center gap-2 mb-4">
            <span class="w-2 h-2 rounded-full bg-[#063B73]"></span>
            <h3 class="font-serif font-bold text-lg text-[#063B73]">Historias Relacionadas</h3>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <?php foreach ($relatedPosts as $rel): ?>
                <?php
                    $relCover = $rel->getCover();
                    $relUrl = base_url('historia/' . $rel->slug);
                ?>
                <a href="<?= $relUrl ?>" class="bg-white rounded-2xl border border-[#063B73]/10 overflow-hidden shadow-sm hover:shadow-md transition-shadow group flex flex-col justify-between">
                    <div>
                        <div class="aspect-[16/10] bg-gray-100 overflow-hidden">
                            <?php if ($relCover): ?>
                                <?= render_media_picture($relCover, 'thumb', 'w-full h-full object-cover group-hover:scale-105 transition-transform duration-300') ?>
                            <?php else: ?>
                                <div class="w-full h-full bg-[#063B73]/10 flex items-center justify-center text-xs font-serif text-[#063B73]">
                                    Marilú Díaz
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="p-4">
                            <?php if ($rel->category): ?>
                                <span class="text-[10px] font-semibold uppercase tracking-wider text-[#D996A6] block mb-1">
                                    <?= esc($rel->category->name) ?>
                                </span>
                            <?php endif; ?>
                            <h4 class="font-serif font-bold text-xs text-[#063B73] line-clamp-2 leading-snug group-hover:text-[#094E96]">
                                <?= esc($rel->title ?: ($rel->excerpt ?: 'Historia recomendada')) ?>
                            </h4>
                        </div>
                    </div>

                    <div class="px-4 pb-3 text-[11px] text-[#5A6776]">
                        <span><?= esc($rel->getRelativeTime()) ?></span>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>

<!-- BOLETÍN NEWSLETTER -->
<?= view('site/partials/newsletter_box') ?>

<?= $this->endSection() ?>

