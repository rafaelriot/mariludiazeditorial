<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="space-y-6">

    <!-- Encabezado con Botón Nueva Categoría -->
    <div class="flex items-center justify-between bg-white p-5 rounded-2xl border border-gray-200 shadow-sm">
        <div>
            <h1 class="font-serif font-bold text-base text-[#063B73]">Categorías Editoriales</h1>
            <p class="text-xs text-[#5A6776]">Organiza las historias y ensayos por pilares temáticos</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        <!-- Formulario Crear Categoría -->
        <div class="lg:col-span-5 bg-white p-6 rounded-2xl border border-gray-200 shadow-sm">
            <h3 class="font-serif font-bold text-sm text-[#063B73] mb-4">Nueva Categoría</h3>

            <form action="<?= base_url('admin/categorias') ?>" method="post" class="space-y-4">
                <?= csrf_field() ?>

                <div>
                    <label class="block text-xs font-semibold text-[#0B2038] mb-1">Nombre</label>
                    <input type="text" name="name" placeholder="p. ej. Liderazgo Consciente" class="w-full bg-[#F8F9FA] border border-gray-200 rounded-xl px-3.5 py-2 text-xs focus:outline-none focus:border-[#063B73]" required>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-[#0B2038] mb-1">Slug (opcional)</label>
                    <input type="text" name="slug" placeholder="liderazgo-consciente" class="w-full bg-[#F8F9FA] border border-gray-200 rounded-xl px-3.5 py-2 text-xs font-mono focus:outline-none focus:border-[#063B73]">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-[#0B2038] mb-1">Tono de Marca (Color del distintivo)</label>
                    <select name="tone" class="w-full bg-[#F8F9FA] border border-gray-200 rounded-xl px-3 py-2 text-xs focus:outline-none focus:border-[#063B73]">
                        <option value="navy">Azul Marino Corporativo (#063B73)</option>
                        <option value="rose">Rosa Empolvado (#D996A6)</option>
                        <option value="sage">Verde Salvia (#A8B58A)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-[#0B2038] mb-1">Orden de visualización</label>
                    <input type="number" name="sort_order" value="0" class="w-full bg-[#F8F9FA] border border-gray-200 rounded-xl px-3.5 py-2 text-xs focus:outline-none focus:border-[#063B73]">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-[#0B2038] mb-1">Descripción breve</label>
                    <textarea name="description" rows="2" class="w-full bg-[#F8F9FA] border border-gray-200 rounded-xl p-3 text-xs focus:outline-none focus:border-[#063B73]"></textarea>
                </div>

                <button type="submit" class="w-full py-2.5 bg-[#063B73] hover:bg-[#094E96] text-white text-xs font-semibold rounded-xl shadow-sm transition-colors">
                    Crear Categoría
                </button>
            </form>
        </div>

        <!-- Lista de Categorías -->
        <div class="lg:col-span-7 bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="p-5 border-b border-gray-200 font-serif font-bold text-sm text-[#063B73]">
                Categorías Existentes (<?= count($categories) ?>)
            </div>

            <div class="divide-y divide-gray-100">
                <?php if (empty($categories)): ?>
                    <div class="p-6 text-center text-xs text-gray-400">
                        No hay categorías creadas aún.
                    </div>
                <?php else: ?>
                    <?php foreach ($categories as $cat): ?>
                        <?php $tone = $cat->getToneClasses(); ?>
                        <div class="p-4 flex items-center justify-between hover:bg-gray-50 transition-colors">
                            <div class="flex items-center gap-3">
                                <span class="w-3 h-3 rounded-full <?= $tone['dot'] ?>"></span>
                                <div>
                                    <h4 class="text-xs font-semibold text-[#0B2038]"><?= esc($cat->name) ?></h4>
                                    <span class="text-[11px] font-mono text-gray-400">/categoria/<?= esc($cat->slug) ?></span>
                                </div>
                            </div>

                            <form action="<?= base_url('admin/categorias/' . $cat->id . '/eliminar') ?>" method="post" onsubmit="return confirm('¿Eliminar categoría? Las historias quedarán sin categoría.');">
                                <?= csrf_field() ?>
                                <button type="submit" class="text-gray-400 hover:text-red-600 p-1.5" title="Eliminar">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

</div>

<?= $this->endSection() ?>

