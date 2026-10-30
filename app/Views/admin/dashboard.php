<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="space-y-6">

    <!-- Tarjetas de Métricas -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Publicadas -->
        <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between text-xs text-[#5A6776] mb-2 font-medium">
                <span>Publicadas</span>
                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
            </div>
            <div class="text-2xl font-bold text-[#063B73]"><?= $totalPublished ?></div>
            <p class="text-[11px] text-[#5A6776] mt-1">Visibles en el feed público</p>
        </div>

        <!-- Borradores -->
        <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between text-xs text-[#5A6776] mb-2 font-medium">
                <span>Borradores</span>
                <span class="w-2 h-2 rounded-full bg-amber-500"></span>
            </div>
            <div class="text-2xl font-bold text-[#0B2038]"><?= $totalDrafts ?></div>
            <p class="text-[11px] text-[#5A6776] mt-1">En proceso de edición</p>
        </div>

        <!-- Programadas -->
        <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between text-xs text-[#5A6776] mb-2 font-medium">
                <span>Programadas</span>
                <span class="w-2 h-2 rounded-full bg-blue-500"></span>
            </div>
            <div class="text-2xl font-bold text-[#0B2038]"><?= $totalScheduled ?></div>
            <p class="text-[11px] text-[#5A6776] mt-1">Fechas futuras</p>
        </div>

        <!-- Comentarios de Lectores -->
        <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between text-xs text-[#5A6776] mb-2 font-medium">
                <span>Comentarios</span>
                <span class="w-2 h-2 rounded-full bg-[#D996A6]"></span>
            </div>
            <div class="text-2xl font-bold text-[#063B73]"><?= $totalComments ?></div>
            <p class="text-[11px] text-[#5A6776] mt-1">Interacciones de lectores</p>
        </div>
    </div>

    <!-- Acciones Rápidas & Historias Recientes -->
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-gray-200 flex items-center justify-between">
            <h3 class="font-serif font-bold text-base text-[#063B73]">Últimas Historias Creadas</h3>
            <a href="<?= base_url('admin/historias/nueva') ?>" class="text-xs font-semibold text-[#063B73] hover:text-[#D996A6]">
                + Nueva Historia
            </a>
        </div>

        <div class="divide-y divide-gray-100">
            <?php if (empty($recentPosts)): ?>
                <div class="p-8 text-center text-xs text-[#5A6776]">
                    Aún no hay historias registradas. Haz clic en <a href="<?= base_url('admin/historias/nueva') ?>" class="text-[#063B73] font-semibold">Nueva Historia</a> para publicar la primera.
                </div>
            <?php else: ?>
                <?php foreach ($recentPosts as $p): ?>
                    <div class="p-4 flex items-center justify-between hover:bg-gray-50 transition-colors">
                        <div class="flex items-center gap-3">
                            <?php if (! empty($p->gallery)): ?>
                                <img src="<?= $p->gallery[0]->getUrl('thumb') ?>" alt="" class="w-12 h-12 rounded-lg object-cover">
                            <?php else: ?>
                                <div class="w-12 h-12 rounded-lg bg-gray-100 flex items-center justify-center text-gray-400 text-xs">
                                    Texto
                                </div>
                            <?php endif; ?>

                            <div>
                                <h4 class="text-xs font-semibold text-[#0B2038]">
                                    <?= esc($p->title ?: ($p->excerpt ?: 'Historia sin título')) ?>
                                </h4>
                                <div class="flex items-center gap-2 text-[11px] text-[#5A6776] mt-0.5">
                                    <span><?= esc($p->getRelativeTime()) ?></span>
                                    <span>•</span>
                                    <span><?= (int) $p->likes_count ?> likes</span>
                                    <span>•</span>
                                    <span><?= (int) $p->comments_count ?> comentarios</span>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center gap-3">
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-semibold
                                <?= $p->status === 'published' ? 'bg-emerald-50 text-emerald-700' : ($p->status === 'scheduled' ? 'bg-blue-50 text-blue-700' : 'bg-amber-50 text-amber-700') ?>">
                                <?= ucfirst($p->status) ?>
                            </span>

                            <a href="<?= base_url('admin/historias/' . $p->id . '/editar') ?>" class="text-xs font-medium text-[#063B73] hover:underline">
                                Editar
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

</div>

<?= $this->endSection() ?>

