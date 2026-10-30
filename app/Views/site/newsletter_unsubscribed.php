<?= $this->extend('layouts/site') ?>

<?= $this->section('content') ?>

<div class="bg-white rounded-3xl border border-[#063B73]/10 shadow-sm p-8 sm:p-12 text-center max-w-lg mx-auto my-12">
    <div class="w-16 h-16 rounded-full bg-gray-100 text-[#5A6776] mx-auto flex items-center justify-center mb-4">
        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
    </div>
    <h1 class="font-serif text-2xl font-bold text-[#063B73] mb-2">Suscripción Cancelada</h1>
    <p class="text-xs text-[#5A6776] leading-relaxed mb-6">
        Has sido dada/o de baja de la correspondencia dominical. No recibirás más correos electrónicos automáticos.
    </p>
    <a href="<?= base_url() ?>" class="inline-flex items-center gap-2 px-6 py-2.5 bg-[#063B73] text-white text-xs font-semibold rounded-full hover:bg-[#094E96] transition-colors">
        <span>Volver a la página principal</span>
    </a>
</div>

<?= $this->endSection() ?>
