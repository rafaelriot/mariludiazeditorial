<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="space-y-6">

    <!-- Filtros de Estado de Comentarios -->
    <div class="flex flex-col sm:flex-row items-center justify-between gap-4 bg-white p-4 rounded-2xl border border-gray-200 shadow-sm">
        <div class="flex items-center gap-1.5 overflow-x-auto w-full sm:w-auto">
            <a href="<?= admin_url('comentarios') ?>"
               class="px-3.5 py-1.5 rounded-xl text-xs font-semibold transition-colors <?= ! $currentStatus ? 'bg-[#063B73] text-white' : 'text-[#5A6776] hover:bg-gray-100' ?>">
                Todos (<?= $counts['all'] ?>)
            </a>
            <a href="<?= admin_url('comentarios?status=pending') ?>"
               class="px-3.5 py-1.5 rounded-xl text-xs font-semibold transition-colors <?= $currentStatus === 'pending' ? 'bg-[#063B73] text-white' : 'text-[#5A6776] hover:bg-gray-100' ?>">
                Pendientes (<?= $counts['pending'] ?>)
            </a>
            <a href="<?= admin_url('comentarios?status=approved') ?>"
               class="px-3.5 py-1.5 rounded-xl text-xs font-semibold transition-colors <?= $currentStatus === 'approved' ? 'bg-[#063B73] text-white' : 'text-[#5A6776] hover:bg-gray-100' ?>">
                Aprobados (<?= $counts['approved'] ?>)
            </a>
            <a href="<?= admin_url('comentarios?status=spam') ?>"
               class="px-3.5 py-1.5 rounded-xl text-xs font-semibold transition-colors <?= $currentStatus === 'spam' ? 'bg-[#063B73] text-white' : 'text-[#5A6776] hover:bg-gray-100' ?>">
                Spam (<?= $counts['spam'] ?>)
            </a>
        </div>
    </div>

    <!-- Tabla de Comentarios -->
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-[#F8F9FA] text-[#5A6776] font-semibold border-b border-gray-200">
                    <tr>
                        <th class="p-4">Autor</th>
                        <th class="p-4">Comentario</th>
                        <th class="p-4">Historia</th>
                        <th class="p-4">Estado</th>
                        <th class="p-4">Fecha</th>
                        <th class="p-4 text-right">Moderación</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php if (empty($comments)): ?>
                        <tr>
                            <td colspan="6" class="p-8 text-center text-[#5A6776]">
                                No hay comentarios en esta bandeja.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($comments as $c): ?>
                            <tr class="hover:bg-gray-50/80 transition-colors">
                                <td class="p-4">
                                    <div class="flex items-center gap-2">
                                        <?php if ($c->author_avatar): ?>
                                            <img src="<?= esc($c->author_avatar) ?>" alt="" class="w-7 h-7 rounded-full object-cover">
                                        <?php else: ?>
                                            <div class="w-7 h-7 rounded-full bg-[#063B73]/10 text-[#063B73] flex items-center justify-center font-bold text-[10px]">
                                                <?= esc(substr($c->author_name, 0, 1)) ?>
                                            </div>
                                        <?php endif; ?>
                                        <div>
                                            <span class="font-semibold text-[#0B2038] block"><?= esc($c->author_name) ?></span>
                                            <span class="text-[10px] text-gray-400"><?= esc($c->author_email) ?></span>
                                        </div>
                                    </div>
                                </td>

                                <td class="p-4 max-w-xs">
                                    <p class="line-clamp-2 text-[#434750]"><?= esc($c->content) ?></p>
                                </td>

                                <td class="p-4 max-w-[180px]">
                                    <?php if ($c->post): ?>
                                        <a href="<?= base_url('historia/' . $c->post->slug) ?>" target="_blank" class="text-[#063B73] hover:underline truncate block">
                                            <?= esc($c->post->title ?: $c->post->slug) ?>
                                        </a>
                                    <?php else: ?>
                                        <span class="text-gray-400">Historia eliminada</span>
                                    <?php endif; ?>
                                </td>

                                <td class="p-4">
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-semibold
                                        <?= $c->status === 'approved' ? 'bg-emerald-50 text-emerald-700' : ($c->status === 'pending' ? 'bg-amber-50 text-amber-700' : 'bg-red-50 text-red-700') ?>">
                                        <?= ucfirst($c->status) ?>
                                    </span>
                                </td>

                                <td class="p-4 text-[#5A6776]">
                                    <?= esc($c->getRelativeTime()) ?>
                                </td>

                                <td class="p-4 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <?php if ($c->status !== 'approved'): ?>
                                            <form action="<?= admin_url('comentarios/' . $c->id . '/estado/approved') ?>" method="post">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="px-2 py-1 rounded bg-emerald-50 hover:bg-emerald-100 text-emerald-700 text-[11px] font-semibold" title="Aprobar">
                                                    ✓ Aprobar
                                                </button>
                                            </form>
                                        <?php endif; ?>

                                        <?php if ($c->status !== 'spam'): ?>
                                            <form action="<?= admin_url('comentarios/' . $c->id . '/estado/spam') ?>" method="post">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="px-2 py-1 rounded bg-amber-50 hover:bg-amber-100 text-amber-700 text-[11px]" title="Marcar como spam">
                                                    Spam
                                                </button>
                                            </form>
                                        <?php endif; ?>

                                        <form action="<?= admin_url('comentarios/' . $c->id . '/eliminar') ?>" method="post" onsubmit="return confirm('¿Eliminar este comentario permanentemente?');">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="p-1 text-gray-400 hover:text-red-600" title="Eliminar">
                                                ✕
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if ($pager): ?>
            <div class="p-4 border-t border-gray-100 flex justify-center">
                <?= $pager->links() ?>
            </div>
        <?php endif; ?>
    </div>

</div>

<?= $this->endSection() ?>

