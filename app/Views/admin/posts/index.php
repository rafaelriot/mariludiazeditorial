<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="space-y-6">

    <!-- Barra de Búsqueda y Filtros de Estado -->
    <div class="flex flex-col sm:flex-row items-center justify-between gap-4 bg-white p-4 rounded-2xl border border-gray-200 shadow-sm">
        <!-- Pestañas de Estado -->
        <div class="flex items-center gap-1.5 overflow-x-auto w-full sm:w-auto">
            <a href="<?= base_url('admin/historias') ?>"
               class="px-3.5 py-1.5 rounded-xl text-xs font-semibold transition-colors <?= ! $currentStatus ? 'bg-[#063B73] text-white' : 'text-[#5A6776] hover:bg-gray-100' ?>">
                Todas
            </a>
            <a href="<?= base_url('admin/historias?status=published') ?>"
               class="px-3.5 py-1.5 rounded-xl text-xs font-semibold transition-colors <?= $currentStatus === 'published' ? 'bg-[#063B73] text-white' : 'text-[#5A6776] hover:bg-gray-100' ?>">
                Publicadas
            </a>
            <a href="<?= base_url('admin/historias?status=draft') ?>"
               class="px-3.5 py-1.5 rounded-xl text-xs font-semibold transition-colors <?= $currentStatus === 'draft' ? 'bg-[#063B73] text-white' : 'text-[#5A6776] hover:bg-gray-100' ?>">
                Borradores
            </a>
            <a href="<?= base_url('admin/historias?status=scheduled') ?>"
               class="px-3.5 py-1.5 rounded-xl text-xs font-semibold transition-colors <?= $currentStatus === 'scheduled' ? 'bg-[#063B73] text-white' : 'text-[#5A6776] hover:bg-gray-100' ?>">
                Programadas
            </a>
        </div>

        <!-- Buscador -->
        <form method="get" action="<?= base_url('admin/historias') ?>" class="w-full sm:w-72 relative">
            <?php if ($currentStatus): ?>
                <input type="hidden" name="status" value="<?= esc($currentStatus) ?>">
            <?php endif; ?>
            <input type="text"
                   name="q"
                   value="<?= esc($search ?? '') ?>"
                   placeholder="Buscar historias..."
                   class="w-full bg-[#F8F9FA] border border-gray-200 rounded-xl pl-9 pr-4 py-2 text-xs focus:outline-none focus:border-[#063B73]">
            <svg class="w-4 h-4 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
        </form>
    </div>

    <!-- Tabla de Historias -->
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-[#F8F9FA] text-[#5A6776] font-semibold border-b border-gray-200">
                    <tr>
                        <th class="p-4">Historia</th>
                        <th class="p-4">Categoría</th>
                        <th class="p-4">Estado</th>
                        <th class="p-4">Interacción</th>
                        <th class="p-4">Fecha</th>
                        <th class="p-4 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php if (empty($posts)): ?>
                        <tr>
                            <td colspan="6" class="p-8 text-center text-[#5A6776]">
                                No se encontraron historias con los filtros seleccionados.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($posts as $post): ?>
                            <tr class="hover:bg-gray-50/80 transition-colors">
                                <!-- Miniatura y Título -->
                                <td class="p-4">
                                    <div class="flex items-center gap-3 max-w-md">
                                        <?php if (! empty($post->gallery)): ?>
                                            <img src="<?= $post->gallery[0]->getUrl('thumb') ?>" alt="" class="w-12 h-12 rounded-xl object-cover flex-shrink-0">
                                        <?php else: ?>
                                            <div class="w-12 h-12 rounded-xl bg-gray-100 flex items-center justify-center text-gray-400 font-semibold text-[10px] flex-shrink-0">
                                                Sin foto
                                            </div>
                                        <?php endif; ?>
                                        <div class="min-w-0">
                                            <a href="<?= base_url('admin/historias/' . $post->id . '/editar') ?>" class="font-semibold text-[#0B2038] hover:text-[#063B73] truncate block">
                                                <?= esc($post->title ?: ($post->excerpt ?: 'Historia sin título')) ?>
                                            </a>
                                            <p class="text-[11px] text-gray-400 font-mono truncate">
                                                /historia/<?= esc($post->slug) ?>
                                            </p>
                                        </div>
                                    </div>
                                </td>

                                <!-- Categoría -->
                                <td class="p-4">
                                    <?php if ($post->category): ?>
                                        <span class="inline-block px-2.5 py-0.5 rounded-full text-[11px] bg-gray-100 text-[#0B2038]">
                                            <?= esc($post->category->name) ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="text-gray-400">—</span>
                                    <?php endif; ?>
                                </td>

                                <!-- Estado -->
                                <td class="p-4">
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-semibold
                                        <?= $post->status === 'published' ? 'bg-emerald-50 text-emerald-700' : ($post->status === 'scheduled' ? 'bg-blue-50 text-blue-700' : 'bg-amber-50 text-amber-700') ?>">
                                        <?= ucfirst($post->status) ?>
                                    </span>
                                </td>

                                <!-- Métricas (Likes, Comentarios, Vistas) -->
                                <td class="p-4 text-[#5A6776]">
                                    <div class="flex items-center gap-2">
                                        <span>❤️ <?= (int) $post->likes_count ?></span>
                                        <span>💬 <?= (int) $post->comments_count ?></span>
                                        <span>👁️ <?= (int) $post->views_count ?></span>
                                    </div>
                                </td>

                                <!-- Fecha -->
                                <td class="p-4 text-[#5A6776]">
                                    <?= esc($post->getRelativeTime()) ?>
                                </td>

                                <!-- Acciones -->
                                <td class="p-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="<?= base_url('historia/' . $post->slug) ?>" target="_blank" class="p-1.5 text-gray-400 hover:text-[#063B73]" title="Ver historia en el blog">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                        </a>

                                        <a href="<?= base_url('admin/historias/' . $post->id . '/editar') ?>" class="p-1.5 text-gray-400 hover:text-[#063B73]" title="Editar historia">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        </a>

                                        <form action="<?= base_url('admin/historias/' . $post->id . '/eliminar') ?>" method="post" onsubmit="return confirm('¿Seguro que deseas eliminar esta historia?');">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="p-1.5 text-gray-400 hover:text-red-600" title="Eliminar historia">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
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
