import Alpine from 'alpinejs';
import intersect from '@alpinejs/intersect';

Alpine.plugin(intersect);

// Componente para historias tipo feed (Likes, Comentarios, Lightbox, Carrusel)
Alpine.data('feedPost', (config) => ({
  postId: config.postId,
  liked: config.isLiked || false,
  likesCount: config.likesCount || 0,
  commentsCount: config.commentsCount || 0,
  activeSlide: 0,
  totalSlides: config.totalSlides || 1,
  showComments: false,
  comments: config.comments || [],
  newCommentText: '',
  isSubmittingComment: false,
  heartPop: false,

  toggleLike() {
    this.liked = !this.liked;
    this.likesCount += this.liked ? 1 : -1;
    this.heartPop = true;
    setTimeout(() => this.heartPop = false, 500);

    const csrfName = document.querySelector('meta[name="csrf-token-name"]')?.content || 'csrf_test_name';
    const csrfHash = document.querySelector('meta[name="csrf-token"]')?.content;

    const formData = new FormData();
    if (csrfHash) formData.append(csrfName, csrfHash);

    fetch(`/historia/${this.postId}/like`, {
      method: 'POST',
      body: formData,
      headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(res => res.json())
    .then(data => {
      if (data.likes_count !== undefined) {
        this.likesCount = data.likes_count;
        this.liked = data.liked;
      }
      if (data.csrf_hash) {
        document.querySelectorAll('meta[name="csrf-token"]').forEach(m => m.content = data.csrf_hash);
      }
    })
    .catch(err => console.error('Error toggling like:', err));
  },

  nextSlide() {
    if (this.activeSlide < this.totalSlides - 1) this.activeSlide++;
    else this.activeSlide = 0;
  },

  prevSlide() {
    if (this.activeSlide > 0) this.activeSlide--;
    else this.activeSlide = this.totalSlides - 1;
  },

  toggleComments() {
    this.showComments = !this.showComments;
  },

  submitComment() {
    if (!this.newCommentText.trim() || this.isSubmittingComment) return;
    this.isSubmittingComment = true;

    const csrfName = document.querySelector('meta[name="csrf-token-name"]')?.content || 'csrf_test_name';
    const csrfHash = document.querySelector('meta[name="csrf-token"]')?.content;

    const formData = new FormData();
    formData.append('content', this.newCommentText);
    if (csrfHash) formData.append(csrfName, csrfHash);

    fetch(`/historia/${this.postId}/comentar`, {
      method: 'POST',
      body: formData,
      headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(res => res.json())
    .then(data => {
      this.isSubmittingComment = false;
      if (data.success && data.comment) {
        this.comments.push(data.comment);
        this.commentsCount++;
        this.newCommentText = '';
        this.showComments = true;
      } else if (data.redirect) {
        // Redirigir a login de Google si requiere autenticación
        window.location.href = data.redirect;
      } else {
        alert(data.message || 'No se pudo publicar el comentario.');
      }
      if (data.csrf_hash) {
        document.querySelectorAll('meta[name="csrf-token"]').forEach(m => m.content = data.csrf_hash);
      }
    })
    .catch(err => {
      this.isSubmittingComment = false;
      console.error('Error submitting comment:', err);
    });
  },

  sharePost(url, title) {
    if (navigator.share) {
      navigator.share({ title: title, url: url }).catch(() => {});
    } else {
      navigator.clipboard.writeText(url).then(() => {
        alert('¡Enlace de la historia copiado al portapapeles!');
      });
    }
  }
}));

// Componente para Lightbox de imágenes de pantalla completa
Alpine.data('lightbox', () => ({
  open: false,
  imageUrl: '',
  imageAlt: '',
  openModal(url, alt) {
    this.imageUrl = url;
    this.imageAlt = alt || '';
    this.open = true;
    document.body.style.overflow = 'hidden';
  },
  closeModal() {
    this.open = false;
    document.body.style.overflow = 'auto';
  }
}));

// Componente para Carrusel de Historias Destacadas
Alpine.data('featuredCarousel', (totalSlides) => ({
  currentSlide: 0,
  total: totalSlides || 1,
  timer: null,

  init() {
    if (this.total > 1) {
      this.startTimer();
    }
  },

  startTimer() {
    this.stopTimer();
    this.timer = setInterval(() => {
      this.next();
    }, 5500);
  },

  stopTimer() {
    if (this.timer) {
      clearInterval(this.timer);
      this.timer = null;
    }
  },

  next() {
    this.currentSlide = (this.currentSlide + 1) % this.total;
    this.scrollToSlide();
  },

  prev() {
    this.currentSlide = (this.currentSlide - 1 + this.total) % this.total;
    this.scrollToSlide();
  },

  goTo(idx) {
    this.currentSlide = idx;
    this.scrollToSlide();
  },

  scrollToSlide() {
    const container = this.$refs.carousel;
    if (!container) return;
    const card = container.children[this.currentSlide];
    if (card) {
      const targetLeft = card.offsetLeft - container.offsetLeft;
      container.scrollTo({ left: targetLeft, behavior: 'smooth' });
    }
  },

  onScroll() {
    const container = this.$refs.carousel;
    if (!container || !container.children.length) return;
    const scrollLeft = container.scrollLeft;
    const width = container.children[0].offsetWidth || 1;
    const newIdx = Math.round(scrollLeft / width);
    if (newIdx !== this.currentSlide && newIdx >= 0 && newIdx < this.total) {
      this.currentSlide = newIdx;
    }
  }
}));

// Componente para suscripción al Newsletter
Alpine.data('newsletterBox', (endpointUrl) => ({
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

    fetch(endpointUrl || '/newsletter/suscribir', {
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
}));

// Componente para buscador inteligente reactivo
Alpine.data('liveSearch', (initialQuery) => ({
  query: initialQuery || '',
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
    .catch(() => {
      this.isSearching = false;
    });
  },

  clearSearch() {
    this.query = '';
    this.doSearch();
  }
}));

window.Alpine = Alpine;
Alpine.start();

