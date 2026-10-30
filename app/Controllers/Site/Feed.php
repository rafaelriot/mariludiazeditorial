<?php

declare(strict_types=1);

namespace App\Controllers\Site;

use App\Controllers\BaseController;
use App\Models\CategoryModel;
use App\Models\PostLikeModel;
use App\Models\PostModel;

class Feed extends BaseController
{
    protected PostModel $postModel;
    protected CategoryModel $categoryModel;
    protected PostLikeModel $likeModel;

    public function __construct()
    {
        $this->postModel = model(PostModel::class);
        $this->categoryModel = model(CategoryModel::class);
        $this->likeModel = model(PostLikeModel::class);
    }

    /**
     * Página principal / Feed limpio tipo Instagram
     */
    public function index()
    {
        $page = max(1, (int) ($this->request->getGet('page') ?: 1));
        $search = trim((string) $this->request->getGet('q')) ?: null;
        $perPage = 6;
        $offset = ($page - 1) * $perPage;

        $posts = $this->postModel->getFeed($perPage, $offset, null, null, $search);
        $this->annotateLikedStatus($posts);

        $hasMore = count($posts) === $perPage;

        // Petición AJAX (cargar más / scroll infinito o buscador reactivo)
        if ($this->request->isAJAX()) {
            return view('site/partials/feed_items', [
                'posts'   => $posts,
                'hasMore' => $hasMore,
                'nextPage'=> $page + 1,
            ]);
        }

        $categories = $this->categoryModel->orderBy('sort_order', 'ASC')->findAll();
        $pinnedPosts = ! $search ? $this->postModel->getPinnedCarousel(5) : [];

        return view('site/feed', [
            'posts'          => $posts,
            'pinnedPosts'    => $pinnedPosts,
            'categories'     => $categories,
            'activeCategory' => null,
            'activeTag'      => null,
            'currentSearch'  => $search,
            'hasMore'        => $hasMore,
            'nextPage'       => 2,
            'pageTitle'      => 'Marilú Díaz — Historias y Liderazgo con Propósito',
        ]);
    }

    /**
     * Feed filtrado por categoría
     */
    public function category(string $slug)
    {
        $category = $this->categoryModel->where('slug', $slug)->first();
        if (! $category) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Categoría '{$slug}' no encontrada.");
        }

        $page = max(1, (int) ($this->request->getGet('page') ?: 1));
        $search = trim((string) $this->request->getGet('q')) ?: null;
        $perPage = 6;
        $offset = ($page - 1) * $perPage;

        $posts = $this->postModel->getFeed($perPage, $offset, $category->id, null, $search);
        $this->annotateLikedStatus($posts);

        $hasMore = count($posts) === $perPage;

        if ($this->request->isAJAX()) {
            return view('site/partials/feed_items', [
                'posts'   => $posts,
                'hasMore' => $hasMore,
                'nextPage'=> $page + 1,
            ]);
        }

        $categories = $this->categoryModel->orderBy('sort_order', 'ASC')->findAll();

        return view('site/feed', [
            'posts'          => $posts,
            'pinnedPosts'    => [],
            'categories'     => $categories,
            'activeCategory' => $category,
            'activeTag'      => null,
            'currentSearch'  => $search,
            'hasMore'        => $hasMore,
            'nextPage'       => 2,
            'pageTitle'      => "{$category->name} — Historias de Marilú Díaz",
        ]);
    }

    /**
     * Feed filtrado por etiqueta (Tag)
     */
    public function tag(string $slug)
    {
        $tagModel = model(\App\Models\TagModel::class);
        $tag = $tagModel->where('slug', $slug)->first();
        if (! $tag) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Etiqueta '{$slug}' no encontrada.");
        }

        $page = max(1, (int) ($this->request->getGet('page') ?: 1));
        $search = trim((string) $this->request->getGet('q')) ?: null;
        $perPage = 6;
        $offset = ($page - 1) * $perPage;

        $posts = $this->postModel->getFeed($perPage, $offset, null, $tag->id, $search);
        $this->annotateLikedStatus($posts);

        $hasMore = count($posts) === $perPage;

        if ($this->request->isAJAX()) {
            return view('site/partials/feed_items', [
                'posts'   => $posts,
                'hasMore' => $hasMore,
                'nextPage'=> $page + 1,
            ]);
        }

        $categories = $this->categoryModel->orderBy('sort_order', 'ASC')->findAll();

        return view('site/feed', [
            'posts'          => $posts,
            'pinnedPosts'    => [],
            'categories'     => $categories,
            'activeCategory' => null,
            'activeTag'      => $tag,
            'currentSearch'  => $search,
            'hasMore'        => $hasMore,
            'nextPage'       => 2,
            'pageTitle'      => "#{$tag->name} — Historias de Marilú Díaz",
        ]);
    }

    /**
     * Marca en cada post si el visitante/usuario actual ya le dio "Me gusta"
     * @param array<\App\Entities\Post> $posts
     */
    protected function annotateLikedStatus(array &$posts): void
    {
        if (empty($posts)) return;

        $userId = auth()->id();
        $visitorHash = $this->getVisitorHash();

        foreach ($posts as $p) {
            $p->has_liked = $this->likeModel->hasLiked($p->id, $userId, $visitorHash);
        }
    }

    protected function getVisitorHash(): string
    {
        $cookieName = 'md_v_id';
        $vId = $this->request->getCookie($cookieName);
        if (! $vId) {
            $vId = bin2hex(random_bytes(16));
            setcookie($cookieName, $vId, [
                'expires'  => time() + (86400 * 365),
                'path'     => '/',
                'httponly' => true,
                'samesite' => 'Lax',
            ]);
        }
        return hash('sha256', $vId . $this->request->getIPAddress());
    }
}

