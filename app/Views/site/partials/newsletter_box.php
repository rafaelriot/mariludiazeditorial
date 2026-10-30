<div class="my-8 bg-gradient-to-br from-[#063B73] to-[#042850] rounded-3xl p-6 sm:p-8 text-white relative overflow-hidden shadow-xl"
     x-data="{
         email: '',
         isSubmitting: false,
         subscribed: false,
         errorMessage: '',
         submit() {
             if (!this.email.trim() || this.isSubmitting) return;
             this.isSubmitting = true;
             this.errorMessage = '';

             const csrfName = document.querySelector('meta[name="csrf-token-name"]')?.content || 'csrf_test_name';
             const csrfHash = document.querySelector('meta[name="csrf-token"]')?.content;

             const formData = new FormData();
             formData.append('email', this.email);
             if (csrfHash) formData.append(csrfName, csrfHash);

             fetch('<?= base_url('newsletter/suscribir') ?>', {
                 method: 'POST',
                 body: formData,
                 headers: { 'X-Requested-With': 'XMLHttpRequest' }
             })
             .then(res => res.json())
             .then(data => {
                 this.isSubmitting = false;
                 if (data.success) {
                     this.subscribed = true;
                     this.email = '';
                 } else {
                     this.errorMessage = data.message || 'No se pudo registrar la suscripción.';
                 }
                 if (data.csrf_hash) {
                     document.querySelectorAll('meta[name="csrf-token"]').forEach(m => m.content = data.csrf_hash);
                 }
             })
             .catch(() => {
                 this.isSubmitting = false;
                 this.errorMessage = 'Ocurrió un error. Inténtalo nuevamente.';
             });
         }
     }">
    <!-- Aura decorativa -->
    <div class="absolute -right-16 -bottom-16 w-60 h-60 rounded-full bg-[#D996A6]/20 blur-3xl pointer-events-none"></div>

    <div class="relative z-10 max-w-lg mx-auto text-center space-y-3">
        <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/10 text-[#D996A6] text-[11px] font-semibold uppercase tracking-wider">
            <span>Correspondencia Semanal</span>
        </div>

        <h3 class="font-serif text-xl sm:text-2xl font-bold text-white leading-tight">
            Ensayos de Liderazgo & Pensamiento en tu correo
        </h3>

        <p class="text-xs text-white/80 leading-relaxed max-w-md mx-auto">
            Cada domingo por la mañana, reflexiones sobre serenidad operativa, dirección estratégica y criterio personal por Marilú Díaz.
        </p>

        <!-- Formulario -->
        <form @submit.prevent="submit()" x-show="!subscribed" class="pt-2 flex flex-col sm:flex-row gap-2 max-w-md mx-auto">
            <input type="email"
                   x-model="email"
                   placeholder="Ingresa tu correo electrónico..."
                   class="flex-1 bg-white/10 border border-white/20 focus:bg-white focus:text-[#0B2038] placeholder-white/60 focus:placeholder-gray-400 text-xs rounded-full px-4 py-3 focus:outline-none transition-all"
                   required>
            <button type="submit"
                    :disabled="isSubmitting"
                    class="px-5 py-3 bg-[#D996A6] hover:bg-[#e0a6b4] text-[#063B73] font-bold text-xs rounded-full transition-colors whitespace-nowrap shadow-md">
                <span x-text="isSubmitting ? 'Registrando...' : 'Suscribirme'"></span>
            </button>
        </form>

        <!-- Mensaje de éxito -->
        <div x-show="subscribed" x-cloak class="p-3 bg-[#A8B58A]/30 border border-[#A8B58A] rounded-2xl text-xs text-white font-medium">
            ✓ ¡Gracias por unirte! Recibirás los próximos ensayos dominicales directamente en tu bandeja.
        </div>

        <!-- Mensaje de error -->
        <div x-show="errorMessage" x-cloak class="text-xs text-red-200 mt-1" x-text="errorMessage"></div>

        <p class="text-[10px] text-white/50 pt-1">
            Sin spam. Respetamos tu privacidad. Puedes cancelar en cualquier momento.
        </p>
    </div>
</div>
