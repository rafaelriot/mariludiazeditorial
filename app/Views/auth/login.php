<!DOCTYPE html>
<html lang="es" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso Administrativo — Marilú Díaz</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Merriweather:wght@400;700&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" href="<?= base_url('assets/brand/isotipo.png') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/build/app.css') ?>">
</head>
<body class="bg-[#FBF9F7] text-[#0B2038] min-h-full flex items-center justify-center p-4">

    <div class="w-full max-w-md">
        <!-- Logo Apilado Oficial -->
        <div class="text-center mb-8">
            <a href="<?= base_url() ?>" class="inline-block hover:opacity-85 transition-opacity">
                <img src="<?= base_url('assets/brand/logo-apilado.png') ?>" alt="Marilú Díaz" class="h-20 w-auto mx-auto object-contain">
            </a>
            <h1 class="font-serif text-xl font-bold text-[#063B73] mt-4">Acceso Administrativo</h1>
            <p class="text-xs text-[#5A6776] mt-1">Plataforma de gestión editorial y publicación</p>
        </div>

        <!-- Tarjeta de Acceso -->
        <div class="bg-white rounded-3xl border border-[#063B73]/10 shadow-lg p-8">
            <?php if (session('error') !== null): ?>
                <div class="bg-red-50 border border-red-200 text-red-700 text-xs p-3.5 rounded-xl mb-5">
                    <?= session('error') ?>
                </div>
            <?php endif ?>

            <?php if (session('errors') !== null): ?>
                <div class="bg-red-50 border border-red-200 text-red-700 text-xs p-3.5 rounded-xl mb-5">
                    <?php if (is_array(session('errors'))): ?>
                        <ul class="list-disc list-inside space-y-1">
                            <?php foreach (session('errors') as $error): ?>
                                <li><?= $error ?></li>
                            <?php endforeach ?>
                        </ul>
                    <?php else: ?>
                        <?= session('errors') ?>
                    <?php endif ?>
                </div>
            <?php endif ?>

            <form action="<?= url_to('login') ?>" method="post" class="space-y-4">
                <?= csrf_field() ?>

                <!-- Correo o Usuario -->
                <div>
                    <label for="email" class="block text-xs font-semibold text-[#0B2038] mb-1.5">Correo electrónico</label>
                    <input type="email"
                           id="email"
                           name="email"
                           inputmode="email"
                           autocomplete="email"
                           value="<?= old('email') ?>"
                           placeholder="tu.correo@ejemplo.com"
                           class="w-full bg-[#FBF9F7] border border-gray-200 focus:border-[#063B73] focus:bg-white rounded-xl px-4 py-3 text-xs text-[#0B2038] focus:outline-none transition-all"
                           required>
                </div>

                <!-- Contraseña -->
                <div>
                    <label for="password" class="block text-xs font-semibold text-[#0B2038] mb-1.5">Contraseña</label>
                    <input type="password"
                           id="password"
                           name="password"
                           inputmode="text"
                           autocomplete="current-password"
                           placeholder="••••••••••••"
                           class="w-full bg-[#FBF9F7] border border-gray-200 focus:border-[#063B73] focus:bg-white rounded-xl px-4 py-3 text-xs text-[#0B2038] focus:outline-none transition-all"
                           required>
                </div>

                <!-- Recordarme -->
                <?php if (setting('Auth.sessionConfig')['allowRemembering']): ?>
                    <div class="flex items-center justify-between text-xs pt-1">
                        <label class="flex items-center gap-2 cursor-pointer text-[#5A6776]">
                            <input type="checkbox" name="remember" class="rounded text-[#063B73] focus:ring-[#063B73]" <?php if (old('remember')): ?> checked<?php endif ?>>
                            <span>Recordar sesión</span>
                        </label>
                    </div>
                <?php endif; ?>

                <!-- Botón de Entrar -->
                <button type="submit" class="w-full py-3 bg-[#063B73] hover:bg-[#094E96] text-white text-xs font-semibold rounded-xl shadow-md transition-all mt-2">
                    Iniciar Sesión
                </button>
            </form>

            <div class="mt-6 pt-5 border-t border-gray-100 text-center">
                <a href="<?= base_url() ?>" class="text-xs text-[#5A6776] hover:text-[#063B73]">
                    &larr; Volver a la página principal
                </a>
            </div>
        </div>
    </div>

</body>
</html>

