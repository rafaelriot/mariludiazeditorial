<!DOCTYPE html>
<html lang="es" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso con Google (Simulador Local) — Marilú Díaz</title>
    <link rel="icon" type="image/png" href="<?= base_url('assets/brand/isotipo.png') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/build/app.css') ?>">
</head>
<body class="bg-[#FBF9F7] text-[#0B2038] min-h-full flex items-center justify-center p-4">

    <div class="w-full max-w-md bg-white rounded-3xl border border-[#063B73]/10 shadow-lg p-8">
        <div class="text-center mb-6">
            <svg class="w-10 h-10 mx-auto mb-3" viewBox="0 0 24 24">
                <path fill="#4285F4" d="M23.745 12.27c0-.7-.06-1.4-.19-2.07H12v4.51h6.6c-.29 1.52-1.14 2.8-2.4 3.66v3.05h3.88c2.27-2.09 3.665-5.17 3.665-9.15z"/>
                <path fill="#34A853" d="M12 24c3.24 0 5.95-1.08 7.93-2.91l-3.88-3.05c-1.08.72-2.45 1.16-4.05 1.16-3.12 0-5.77-2.1-6.72-4.93H1.26v3.15C3.27 21.36 7.35 24 12 24z"/>
                <path fill="#FBBC05" d="M5.28 14.27c-.25-.72-.38-1.49-.38-2.27s.13-1.55.38-2.27V6.58H1.26C.46 8.17 0 9.99 0 12s.46 3.83 1.26 5.42l4.02-3.15z"/>
                <path fill="#EA4335" d="M12 4.75c1.77 0 3.35.61 4.6 1.8l3.42-3.42C17.95 1.19 15.24 0 12 0 7.35 0 3.27 2.64 1.26 6.58l4.02 3.15c.95-2.83 3.6-4.98 6.72-4.98z"/>
            </svg>
            <h1 class="font-serif text-lg font-bold text-[#063B73]">Iniciar Sesión con Google</h1>
            <p class="text-xs text-[#5A6776] mt-1">Acceso para comentar y guardar historias favoritas</p>
        </div>

        <form action="<?= base_url('auth/google/mock') ?>" method="post" class="space-y-4">
            <?= csrf_field() ?>

            <div>
                <label class="block text-xs font-semibold text-[#0B2038] mb-1">Nombre</label>
                <input type="text" name="name" value="Lector Editorial" class="w-full bg-[#FBF9F7] border border-gray-200 rounded-xl px-3.5 py-2.5 text-xs text-[#0B2038] focus:outline-none focus:border-[#063B73]" required>
            </div>

            <div>
                <label class="block text-xs font-semibold text-[#0B2038] mb-1">Correo de Gmail</label>
                <input type="email" name="email" value="lector@gmail.com" class="w-full bg-[#FBF9F7] border border-gray-200 rounded-xl px-3.5 py-2.5 text-xs text-[#0B2038] focus:outline-none focus:border-[#063B73]" required>
            </div>

            <button type="submit" class="w-full py-2.5 bg-[#063B73] hover:bg-[#094E96] text-white text-xs font-semibold rounded-xl shadow transition-colors">
                Continuar con Google
            </button>
        </form>

        <div class="mt-4 text-center">
            <a href="<?= esc($returnTo) ?>" class="text-xs text-[#5A6776] hover:text-[#063B73]">Cancelar y volver</a>
        </div>
    </div>

</body>
</html>

