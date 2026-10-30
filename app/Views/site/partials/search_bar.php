<div class="mb-5"
     x-data="{
         query: '<?= esc($currentSearch ?? '') ?>',
         isSearching: false,
         timer: null,
         onInput() {
             clearTimeout(this.timer);
             this.timer = setTimeout(() => {
                 this.doSearch();
             }, 350);
         },
         doSearch() {
             const url = new URL(window.location.href);
             if (this.query.trim().length > 0) {
                 url.searchParams.set('q', this.query.trim());
             } else {
                 url.searchParams.delete('q');
             }
             url.searchParams.delete('page');
             this.isSearching = true;

             fetch(url.toString(), {
                 headers: { 'X-Requested-With': 'XMLHttpRequest' }
             })
             .then(res => res.text())
             .then(html => {
                 this.isSearching = false;
                 const container = document.getElementById('feed-container');
                 if (container) {
                     container.innerHTML = html;
                 }
                 window.history.replaceState({}, '', url.toString());
             })
             .catch(() => this.isSearching = false);
         },
         clearSearch() {
             this.query = '';
             this.doSearch();
         }
     }">
    <div class="relative flex items-center">
        <!-- Icono Lupa -->
        <span class="absolute left-3.5 text-gray-400 pointer-events-none">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
        </span>

        <input type="text"
               x-model="query"
               @input="onInput()"
               @keydown.enter.prevent="doSearch()"
               placeholder="Buscar por temas, reflexiones o palabras clave..."
               class="w-full bg-white border border-[#063B73]/10 focus:border-[#063B73] rounded-2xl pl-10 pr-10 py-2.5 text-xs text-[#0B2038] shadow-sm focus:outline-none transition-all">

        <!-- Botón Limpiar o Spinner -->
        <button type="button"
                x-show="query.length > 0"
                @click="clearSearch()"
                class="absolute right-3 text-gray-400 hover:text-gray-600 text-xs w-5 h-5 flex items-center justify-center rounded-full bg-gray-100"
                title="Limpiar búsqueda">
            ✕
        </button>
    </div>
</div>
