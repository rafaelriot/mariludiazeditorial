<?= $this->extend('layouts/site') ?>

<?= $this->section('content') ?>

<div class="bg-white rounded-3xl border border-[#063B73]/10 shadow-sm p-6 sm:p-12 mb-8">
    <!-- Encabezado con Isotipo y Retrato -->
    <div class="flex flex-col sm:flex-row items-center sm:items-start gap-8 pb-8 border-b border-gray-100">
        <div class="story-ring p-1">
            <div class="story-ring-inner p-1">
                <img src="<?= base_url('assets/brand/isotipo.png') ?>" alt="Marilú Díaz" class="w-24 h-24 sm:w-28 sm:h-28 rounded-full object-cover">
            </div>
        </div>
        <div class="text-center sm:text-left">
            <h1 class="font-serif text-3xl font-bold text-[#063B73] mb-2">Marilú Díaz</h1>
            <p class="text-sm font-medium text-[#D996A6] tracking-wide uppercase mb-3">Pensamiento, Liderazgo & Alta Dirección</p>
            <p class="text-sm text-[#434750] leading-relaxed max-w-lg">
                Acompañando a directivos, CEOs y equipos estratégicos a encontrar claridad en tiempos de aceleración, construyendo culturas sostenibles con empatía y rigor.
            </p>
        </div>
    </div>

    <!-- Contenido Editorial -->
    <div class="prose max-w-none text-[#1B242D] leading-relaxed my-8 text-base">
        <?= $site->about ?>
    </div>

    <!-- Pilares y Enfoque -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 my-8">
        <div class="p-5 rounded-2xl bg-[#FBF9F7] border border-[#063B73]/10">
            <div class="w-8 h-8 rounded-full bg-[#063B73]/10 text-[#063B73] flex items-center justify-center font-serif font-bold text-sm mb-3">01</div>
            <h3 class="font-serif font-bold text-sm text-[#063B73] mb-1">Serenidad Táctica</h3>
            <p class="text-xs text-[#5A6776]">Pausas estratégicas para calibrar decisiones ante la hiperaceleración.</p>
        </div>
        <div class="p-5 rounded-2xl bg-[#FBF9F7] border-[#D996A6]/30 border">
            <div class="w-8 h-8 rounded-full bg-[#D996A6]/20 text-[#8E4B5B] flex items-center justify-center font-serif font-bold text-sm mb-3">02</div>
            <h3 class="font-serif font-bold text-sm text-[#063B73] mb-1">Empatía & Excelencia</h3>
            <p class="text-xs text-[#5A6776]">Desarmando la falsa dicotomía entre compasión y altos estándares.</p>
        </div>
        <div class="p-5 rounded-2xl bg-[#FBF9F7] border-[#A8B58A]/30 border">
            <div class="w-8 h-8 rounded-full bg-[#A8B58A]/20 text-[#4F5C38] flex items-center justify-center font-serif font-bold text-sm mb-3">03</div>
            <h3 class="font-serif font-bold text-sm text-[#063B73] mb-1">Propósito Nítido</h3>
            <p class="text-xs text-[#5A6776]">Construcción de marca y reputación para líderes que no temen destacar.</p>
        </div>
    </div>

    <!-- Regreso -->
    <div class="text-center pt-6 border-t border-gray-100">
        <a href="<?= base_url() ?>" class="inline-flex items-center gap-2 px-6 py-2.5 bg-[#063B73] text-white text-xs font-semibold rounded-full hover:bg-[#094E96] transition-colors shadow-sm">
            <span>Explorar las historias del blog</span>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
        </a>
    </div>
</div>

<?= $this->endSection() ?>
