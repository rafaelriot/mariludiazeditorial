<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="space-y-6">

    <!-- Encabezado con Botón de Exportar a CSV -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 bg-white p-5 rounded-2xl border border-gray-200 shadow-sm">
        <div>
            <h1 class="font-serif font-bold text-base text-[#063B73]">Suscriptores de la Correspondencia</h1>
            <p class="text-xs text-[#5A6776]">Lectores que reciben los ensayos dominicales (Total: <?= $total ?>)</p>
        </div>

        <a href="<?= admin_url('suscriptores/exportar') ?>" class="inline-flex items-center gap-2 px-4 py-2 bg-[#063B73] hover:bg-[#094E96] text-white text-xs font-semibold rounded-xl transition-colors shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
            <span>Exportar CSV (Excel / Substack)</span>
        </a>
    </div>

    <!-- Tabla de Suscriptores -->
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-[#F8F9FA] text-[#5A6776] font-semibold border-b border-gray-200">
                    <tr>
                        <th class="p-4">Correo Electrónico</th>
                        <th class="p-4">Estado</th>
                        <th class="p-4">Origen</th>
                        <th class="p-4">Fecha de Registro</th>
                        <th class="p-4 text-right">Acción</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php if (empty($subscribers)): ?>
                        <tr>
                            <td colspan="5" class="p-8 text-center text-[#5A6776]">
                                Aún no hay suscriptores registrados en el boletín.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($subscribers as $s): ?>
                            <tr class="hover:bg-gray-50/80 transition-colors">
                                <td class="p-4 font-semibold text-[#0B2038]">
                                    <?= esc($s->email) ?>
                                </td>
                                <td class="p-4">
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-semibold
                                        <?= $s->status === 'active' ? 'bg-emerald-50 text-emerald-700' : 'bg-gray-100 text-gray-500' ?>">
                                        <?= ucfirst($s->status) ?>
                                    </span>
                                </td>
                                <td class="p-4 text-gray-500">
                                    <?= esc($s->source) ?>
                                </td>
                                <td class="p-4 text-[#5A6776]">
                                    <?= $s->created_at ? $s->created_at->toLocalizedString('d MMM yyyy, HH:mm') : '—' ?>
                                </td>
                                <td class="p-4 text-right">
                                    <form action="<?= admin_url('suscriptores/' . $s->id . '/eliminar') ?>" method="post" onsubmit="return confirm('¿Eliminar a este suscriptor?');">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="text-gray-400 hover:text-red-600 p-1" title="Eliminar">
                                            ✕
                                        </button>
                                    </form>
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

