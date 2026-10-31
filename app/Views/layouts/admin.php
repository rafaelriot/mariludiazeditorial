<!DOCTYPE html>
<html lang="es" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($pageTitle ?? 'Panel Administrativo') ?> — Marilú Díaz</title>
    <meta name="csrf-token-name" content="<?= csrf_token() ?>">
    <meta name="csrf-token" content="<?= csrf_hash() ?>">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Merriweather:wght@400;700&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" href="<?= base_url('assets/brand/isotipo.png') ?>">

    <link rel="stylesheet" href="<?= base_url('assets/build/admin.css') ?>">
    <script defer src="<?= base_url('assets/build/admin.js') ?>"></script>

    <!-- CKEditor 5 CDN para panel administrativo -->
    <script src="https://cdn.ckeditor.com/ckeditor5/41.1.0/classic/ckeditor.js"></script>
</head>
<body class="bg-[#F8F9FA] text-[#0B2038] min-h-full flex flex-col md:flex-row antialiased">

    <!-- BARRA LATERAL (SIDEBAR) -->
    <aside class="w-full md:w-64 bg-[#063B73] text-white flex-shrink-0 flex flex-col justify-between p-6">
        <div>
            <!-- Logo de Marca -->
            <div class="mb-8 flex items-center justify-between">
                <a href="<?= admin_url() ?>" class="flex items-center gap-2.5">
                    <img src="<?= base_url('assets/brand/isotipo.png') ?>" alt="MD" class="w-8 h-8 rounded-full bg-white p-0.5 object-contain">
                    <span class="font-serif font-bold text-lg text-white">Marilú Díaz</span>
                </a>
            </div>

            <!-- Navegación Administrativa -->
            <nav class="space-y-1.5 text-xs font-medium">
                <a href="<?= admin_url() ?>" class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-white/10 transition-colors <?= current_url() === admin_url() ? 'bg-white/15 text-white font-semibold' : 'text-gray-300' ?>">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                    <span>Dashboard</span>
                </a>

                <a href="<?= admin_url('historias') ?>" class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-white/10 transition-colors <?= str_contains(current_url(), admin_path() . '/historias') && ! str_contains(current_url(), 'nueva') ? 'bg-white/15 text-white font-semibold' : 'text-gray-300' ?>">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path></svg>
                    <span>Todas las Historias</span>
                </a>

                <a href="<?= admin_url('historias/nueva') ?>" class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-white/10 transition-colors <?= str_contains(current_url(), admin_path() . '/historias/nueva') ? 'bg-white/15 text-white font-semibold' : 'text-gray-300' ?>">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    <span>Nueva Historia</span>
                </a>

                <a href="<?= admin_url('categorias') ?>" class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-white/10 transition-colors <?= str_contains(current_url(), admin_path() . '/categorias') ? 'bg-white/15 text-white font-semibold' : 'text-gray-300' ?>">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                    <span>Categorías</span>
                </a>

                <a href="<?= admin_url('comentarios') ?>" class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-white/10 transition-colors <?= str_contains(current_url(), admin_path() . '/comentarios') ? 'bg-white/15 text-white font-semibold' : 'text-gray-300' ?>">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                    <span>Comentarios</span>
                </a>

                <a href="<?= admin_url('suscriptores') ?>" class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-white/10 transition-colors <?= str_contains(current_url(), admin_path() . '/suscriptores') ? 'bg-white/15 text-white font-semibold' : 'text-gray-300' ?>">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                    <span>Newsletter</span>
                </a>
            </nav>
        </div>

        <!-- Acciones Inferiores -->
        <div class="pt-6 border-t border-white/10 space-y-2">
            <a href="<?= base_url() ?>" target="_blank" class="flex items-center gap-2 text-xs text-gray-300 hover:text-white px-2 py-1.5 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                <span>Ver Blog Público</span>
            </a>
            <a href="<?= url_to('logout') ?>" class="flex items-center gap-2 text-xs text-red-300 hover:text-red-100 px-2 py-1.5 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                <span>Cerrar Sesión</span>
            </a>
        </div>
    </aside>

    <!-- ÁREA DE CONTENIDO -->
    <div class="flex-1 flex flex-col min-h-screen">
        <!-- Barra Superior -->
        <header class="h-16 bg-white border-b border-gray-200 px-6 flex items-center justify-between">
            <h2 class="text-sm font-semibold text-[#0B2038]">
                <?= esc($pageTitle ?? 'Panel de Administración') ?>
            </h2>

            <div class="flex items-center gap-3">
                <a href="<?= admin_url('historias/nueva') ?>" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-[#063B73] hover:bg-[#094E96] text-white text-xs font-semibold rounded-lg shadow-sm transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    <span>Publicar</span>
                </a>
            </div>
        </header>

        <!-- Mensajes Flash del Admin -->
        <?php if (session()->has('message')): ?>
            <div class="px-6 pt-4">
                <div class="bg-green-50 border border-green-200 text-green-800 text-xs px-4 py-3 rounded-xl flex items-center justify-between">
                    <span><?= session('message') ?></span>
                    <button onclick="this.parentElement.remove()" class="font-bold">✕</button>
                </div>
            </div>
        <?php endif; ?>

        <?php if (session()->has('error')): ?>
            <div class="px-6 pt-4">
                <div class="bg-red-50 border border-red-200 text-red-800 text-xs px-4 py-3 rounded-xl flex items-center justify-between">
                    <span><?= session('error') ?></span>
                    <button onclick="this.parentElement.remove()" class="font-bold">✕</button>
                </div>
            </div>
        <?php endif; ?>

        <!-- Contenido de la vista -->
        <main class="flex-1 p-6">
            <?= $this->renderSection('content') ?>
        </main>
    </div>

</body>
</html>

