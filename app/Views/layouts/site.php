<!DOCTYPE html>
<html lang="es" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($pageTitle ?? 'Marilú Díaz — Historias y Liderazgo con Propósito') ?></title>
    <meta name="description" content="<?= esc($metaDescription ?? 'Blog personal de Marilú Díaz: historias, reflexiones y liderazgo consciente.') ?>">
    <meta name="csrf-token-name" content="<?= csrf_token() ?>">
    <meta name="csrf-token" content="<?= csrf_hash() ?>">

    <!-- Tipografías de Google Fonts: Merriweather + Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Merriweather:ital,wght@0,300;0,400;0,700;1,300;1,400&display=swap" rel="stylesheet">

    <!-- Favicon con el isotipo oficial -->
    <link rel="icon" type="image/png" href="<?= base_url('assets/brand/isotipo.png') ?>">

    <!-- Canonical URL -->
    <link rel="canonical" href="<?= current_url() ?>">

    <!-- Open Graph / Redes Sociales -->
    <meta property="og:type" content="<?= isset($post) ? 'article' : 'website' ?>">
    <meta property="og:site_name" content="Marilú Díaz">
    <meta property="og:title" content="<?= esc($pageTitle ?? 'Marilú Díaz') ?>">
    <meta property="og:description" content="<?= esc($metaDescription ?? 'Blog personal de Marilú Díaz') ?>">
    <meta property="og:image" content="<?= esc($ogImage ?? base_url('assets/brand/isotipo.png')) ?>">
    <meta property="og:url" content="<?= current_url() ?>">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= esc($pageTitle ?? 'Marilú Díaz') ?>">
    <meta name="twitter:description" content="<?= esc($metaDescription ?? 'Blog personal de Marilú Díaz') ?>">
    <meta name="twitter:image" content="<?= esc($ogImage ?? base_url('assets/brand/isotipo.png')) ?>">

    <!-- Schema.org Datos Estructurados JSON-LD -->
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@graph": [
        {
          "@type": "Person",
          "@id": "<?= base_url('#author') ?>",
          "name": "Marilú Díaz",
          "jobTitle": "Mentora y Autora",
          "url": "<?= base_url('sobre-mi') ?>",
          "image": "<?= base_url('assets/brand/isotipo.png') ?>",
          "description": "Liderazgo consciente, estrategia y mentalidades con propósito."
        },
        {
          "@type": "WebSite",
          "@id": "<?= base_url('#website') ?>",
          "url": "<?= base_url() ?>",
          "name": "Marilú Díaz — Plataforma Editorial",
          "publisher": { "@id": "<?= base_url('#author') ?>" }
        }
        <?php if (isset($post)): ?>
        ,{
          "@type": "BlogPosting",
          "headline": "<?= esc($post->title ?: ($post->excerpt ?: 'Historia de Marilú Díaz')) ?>",
          "description": "<?= esc($post->meta_description ?: ($post->excerpt ?: 'Historia de Marilú Díaz')) ?>",
          "image": "<?= esc($ogImage ?? base_url('assets/brand/isotipo.png')) ?>",
          "datePublished": "<?= ($post->published_at ?? $post->created_at)->toIso8601String() ?>",
          "dateModified": "<?= ($post->updated_at ?? $post->created_at)->toIso8601String() ?>",
          "author": { "@id": "<?= base_url('#author') ?>" },
          "publisher": { "@id": "<?= base_url('#author') ?>" },
          "mainEntityOfPage": "<?= current_url() ?>"
        }
        <?php endif; ?>
      ]
    }
    </script>

    <!-- Assets compilados con Tailwind CSS v4 y Alpine.js -->
    <link rel="stylesheet" href="<?= base_url('assets/build/app.css') ?>">
    <script defer src="<?= base_url('assets/build/app.js') ?>"></script>
</head>
<body class="bg-[#FBF9F7] text-[#0B2038] min-h-full flex flex-col font-sans antialiased selection:bg-[#D996A6]/30 selection:text-[#063B73]" x-data="lightbox">

    <!-- NAVBAR STICKY TIPO INSTAGRAM / SUBSTACK -->
    <header class="sticky top-0 z-40 w-full bg-[#FBF9F7]/90 backdrop-blur-md border-b border-[#063B73]/10">
        <div class="max-w-2xl mx-auto px-4 h-16 flex items-center justify-between">
            <!-- Logo Oficial de Marilú Díaz -->
            <a href="<?= base_url() ?>" class="flex items-center gap-2 group transition-opacity hover:opacity-85" aria-label="Ir al inicio de Marilú Díaz">
                <img src="<?= base_url('assets/brand/logo-horizontal.png') ?>" alt="Marilú Díaz" class="h-8 w-auto object-contain">
            </a>

            <!-- Acciones de Cabecera (Sobre Mí, Auth Google, Acceso Admin) -->
            <div class="flex items-center gap-3">
                <a href="<?= base_url('sobre-mi') ?>" class="text-xs sm:text-sm font-medium text-[#5A6776] hover:text-[#063B73] px-2.5 py-1.5 rounded-full hover:bg-white/60 transition-colors">
                    Sobre Mí
                </a>

                <?php if (auth()->loggedIn()): ?>
                    <?php
                        $user = auth()->user();
                        $profile = session()->get('reader_profile');
                        $displayName = $profile['name'] ?? $user->username;
                        $avatarUrl = $profile['avatar'] ?? null;
                    ?>
                    <!-- Usuario Autenticado (Lector o Admin) -->
                    <div class="flex items-center gap-2 pl-1 border-l border-gray-200">
                        <?php if ($avatarUrl): ?>
                            <img src="<?= esc($avatarUrl) ?>" alt="<?= esc($displayName) ?>" class="w-8 h-8 rounded-full border border-gray-300 object-cover" title="<?= esc($displayName) ?>">
                        <?php else: ?>
                            <div class="w-8 h-8 rounded-full bg-[#063B73] text-white flex items-center justify-center text-xs font-semibold" title="<?= esc($displayName) ?>">
                                <?= esc(strtoupper(substr($displayName, 0, 1))) ?>
                            </div>
                        <?php endif; ?>

                        <?php if (auth()->user()->can('admin.access')): ?>
                            <a href="<?= base_url('admin') ?>" class="hidden sm:inline-flex items-center gap-1 text-xs font-semibold bg-[#063B73] text-white px-3 py-1.5 rounded-full hover:bg-[#094E96] transition-colors">
                                Panel
                            </a>
                        <?php endif; ?>

                        <a href="<?= base_url('auth/logout') ?>" class="text-xs text-[#5A6776] hover:text-red-600 px-1 py-1" title="Cerrar sesión">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                        </a>
                    </div>
                <?php else: ?>
                    <!-- Botón para Iniciar Sesión con Google / Gmail -->
                    <a href="<?= base_url('auth/google?return=' . urlencode(current_url())) ?>" class="inline-flex items-center gap-1.5 text-xs font-medium bg-white text-[#0B2038] border border-gray-300 hover:border-[#063B73] px-3 py-1.5 rounded-full shadow-sm hover:shadow transition-all">
                        <!-- Icono Google SVG -->
                        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24">
                            <path fill="#4285F4" d="M23.745 12.27c0-.7-.06-1.4-.19-2.07H12v4.51h6.6c-.29 1.52-1.14 2.8-2.4 3.66v3.05h3.88c2.27-2.09 3.665-5.17 3.665-9.15z"/>
                            <path fill="#34A853" d="M12 24c3.24 0 5.95-1.08 7.93-2.91l-3.88-3.05c-1.08.72-2.45 1.16-4.05 1.16-3.12 0-5.77-2.1-6.72-4.93H1.26v3.15C3.27 21.36 7.35 24 12 24z"/>
                            <path fill="#FBBC05" d="M5.28 14.27c-.25-.72-.38-1.49-.38-2.27s.13-1.55.38-2.27V6.58H1.26C.46 8.17 0 9.99 0 12s.46 3.83 1.26 5.42l4.02-3.15z"/>
                            <path fill="#EA4335" d="M12 4.75c1.77 0 3.35.61 4.6 1.8l3.42-3.42C17.95 1.19 15.24 0 12 0 7.35 0 3.27 2.64 1.26 6.58l4.02 3.15c.95-2.83 3.6-4.98 6.72-4.98z"/>
                        </svg>
                        <span>Entrar</span>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <!-- MENSAJES FLASH -->
    <?php if (session()->has('message')): ?>
        <div class="max-w-2xl mx-auto px-4 mt-3 w-full">
            <div class="bg-[#A8B58A]/20 border border-[#A8B58A] text-[#4F5C38] text-sm px-4 py-2.5 rounded-xl flex items-center justify-between">
                <span><?= session('message') ?></span>
                <button onclick="this.parentElement.remove()" class="text-xs font-bold ml-2">✕</button>
            </div>
        </div>
    <?php endif; ?>

    <?php if (session()->has('error')): ?>
        <div class="max-w-2xl mx-auto px-4 mt-3 w-full">
            <div class="bg-red-50 border border-red-200 text-red-800 text-sm px-4 py-2.5 rounded-xl flex items-center justify-between">
                <span><?= session('error') ?></span>
                <button onclick="this.parentElement.remove()" class="text-xs font-bold ml-2">✕</button>
            </div>
        </div>
    <?php endif; ?>

    <!-- CONTENIDO PRINCIPAL -->
    <main class="flex-1 w-full max-w-2xl mx-auto px-4 py-6">
        <?= $this->renderSection('content') ?>
    </main>

    <!-- LIGHTBOX MODAL PARA FOTOGRAFÍAS A PANTALLA COMPLETA -->
    <div x-show="open"
         x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @keydown.escape.window="closeModal()"
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/90 p-4">
        <button @click="closeModal()" class="absolute top-4 right-4 text-white hover:text-[#D996A6] text-3xl font-light z-10 w-10 h-10 flex items-center justify-center rounded-full bg-white/10" aria-label="Cerrar vista completa">
            ✕
        </button>
        <div class="relative max-w-4xl max-h-[90vh] flex items-center justify-center" @click.away="closeModal()">
            <img :src="imageUrl" :alt="imageAlt" class="max-h-[85vh] max-w-full rounded-lg object-contain shadow-2xl">
        </div>
    </div>

    <!-- FOOTER MINIMALISTA MARILÚ DÍAZ -->
    <footer class="w-full border-t border-[#063B73]/10 bg-white/50 py-8 mt-12">
        <div class="max-w-2xl mx-auto px-4 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-[#5A6776]">
            <div class="flex items-center gap-2">
                <img src="<?= base_url('assets/brand/isotipo.png') ?>" alt="MD" class="w-6 h-6 object-contain">
                <span>© <?= date('Y') ?> Marilú Díaz. Todos los derechos reservados.</span>
            </div>
            <div class="flex items-center gap-4">
                <a href="<?= base_url('sobre-mi') ?>" class="hover:text-[#063B73]">Sobre Mí</a>
                <a href="<?= base_url('feed.xml') ?>" class="hover:text-[#063B73]">RSS</a>
                <a href="<?= base_url('sitemap.xml') ?>" class="hover:text-[#063B73]">Sitemap</a>
                <a href="<?= base_url('login') ?>" class="text-gray-400 hover:text-[#063B73]" title="Acceso Administrativo">Admin</a>
            </div>
        </div>
    </footer>

</body>
</html>

