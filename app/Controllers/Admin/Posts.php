<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\CategoryModel;
use App\Models\PostModel;
use App\Services\PostService;

class Posts extends BaseController
{
    protected PostModel $postModel;
    protected CategoryModel $categoryModel;
    protected PostService $postService;

    public function __construct()
    {
        $this->postModel = model(PostModel::class);
        $this->categoryModel = model(CategoryModel::class);
        $this->postService = new PostService($this->postModel);
    }

    /**
     * Listado de historias en el panel
     */
    public function index()
    {
        $status = $this->request->getGet('status');
        $search = $this->request->getGet('q');

        $query = $this->postModel->orderBy('created_at', 'DESC');

        if ($status && in_array($status, ['draft', 'scheduled', 'published'], true)) {
            $query->where('status', $status);
        }
        if ($search) {
            $query->groupStart()
                ->like('title', $search)
                ->orLike('content', $search)
                ->orLike('slug', $search)
            ->groupEnd();
        }

        $posts = $query->paginate(15);
        $this->postModel->eagerLoadRelations($posts);

        return view('admin/posts/index', [
            'posts'          => $posts,
            'pager'          => $this->postModel->pager,
            'currentStatus'  => $status,
            'search'         => $search,
        ]);
    }

    /**
     * Formulario para crear una nueva historia
     */
    public function new()
    {
        $categories = $this->categoryModel->orderBy('sort_order', 'ASC')->findAll();

        return view('admin/posts/form', [
            'post'       => null,
            'categories' => $categories,
            'gallery'    => [],
            'isEdit'     => false,
        ]);
    }

    /**
     * Guarda una nueva historia
     */
    public function create()
    {
        $data = $this->extractFormData();
        $galleryIds = $this->extractGalleryIds();
        $tagNames = $this->extractTagNames();

        try {
            $data['author_id'] = auth()->id();
            $post = $this->postService->savePost($data, $galleryIds, $tagNames);

            return redirect()->to(admin_url('historias'))
                ->with('message', "Historia '{$post->slug}' guardada correctamente.");
        } catch (\Throwable $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al guardar la historia: ' . $e->getMessage());
        }
    }

    /**
     * Formulario para editar una historia existente
     */
    public function edit(int $id)
    {
        $post = $this->postModel->find($id);
        if (! $post) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Historia {$id} no encontrada.");
        }

        $this->postModel->eagerLoadRelations([$post]);
        $categories = $this->categoryModel->orderBy('sort_order', 'ASC')->findAll();

        return view('admin/posts/form', [
            'post'       => $post,
            'categories' => $categories,
            'gallery'    => $post->gallery,
            'isEdit'     => true,
        ]);
    }

    /**
     * Actualiza una historia existente
     */
    public function update(int $id)
    {
        $post = $this->postModel->find($id);
        if (! $post) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Historia {$id} no encontrada.");
        }

        $data = $this->extractFormData();
        $galleryIds = $this->extractGalleryIds();
        $tagNames = $this->extractTagNames();

        try {
            $this->postService->savePost($data, $galleryIds, $tagNames, $id);

            return redirect()->to(admin_url('historias'))
                ->with('message', 'Historia actualizada correctamente.');
        } catch (\Throwable $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al actualizar: ' . $e->getMessage());
        }
    }

    /**
     * Elimina una historia
     */
    public function delete(int $id)
    {
        $post = $this->postModel->find($id);
        if ($post) {
            $this->postModel->delete($id);
        }

        return redirect()->to(admin_url('historias'))
            ->with('message', 'Historia eliminada correctamente.');
    }

    protected function extractFormData(): array
    {
        $title = trim((string) $this->request->getPost('title'));
        $slug = trim((string) $this->request->getPost('slug'));
        $content = (string) $this->request->getPost('content');
        $excerpt = trim((string) $this->request->getPost('excerpt'));
        $status = (string) $this->request->getPost('status') ?: 'draft';
        $categoryId = (int) $this->request->getPost('category_id') ?: null;
        $isPinned = (bool) $this->request->getPost('is_pinned');

        $publishedAt = null;
        $pubDateInput = $this->request->getPost('published_at');
        if (! empty($pubDateInput)) {
            $publishedAt = date('Y-m-d H:i:s', strtotime((string) $pubDateInput));
        }

        return [
            'title'            => $title ?: null,
            'slug'             => $slug ?: null,
            'content'          => $content,
            'excerpt'          => $excerpt ?: null,
            'status'           => $status,
            'category_id'      => $categoryId,
            'is_pinned'        => $isPinned ? 1 : 0,
            'published_at'     => $publishedAt,
            'meta_title'       => trim((string) $this->request->getPost('meta_title')) ?: null,
            'meta_description' => trim((string) $this->request->getPost('meta_description')) ?: null,
        ];
    }

    protected function extractGalleryIds(): array
    {
        $rawIds = $this->request->getPost('gallery_ids');
        if (! $rawIds) return [];

        if (is_string($rawIds)) {
            $rawIds = explode(',', $rawIds);
        }
        return array_filter(array_map('intval', (array) $rawIds));
    }

    protected function extractTagNames(): array
    {
        $rawTags = (string) $this->request->getPost('tags');
        if (empty(trim($rawTags))) return [];

        $parts = explode(',', $rawTags);
        return array_filter(array_map('trim', $parts));
    }
}

