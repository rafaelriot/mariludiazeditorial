<?php

declare(strict_types=1);

namespace App\Models;

use App\Entities\Post;
use CodeIgniter\Model;

class PostModel extends Model
{
    protected $table         = 'posts';
    protected $primaryKey    = 'id';
    protected $returnType    = Post::class;
    protected $useTimestamps = true;
    protected $useSoftDeletes= true;
    protected $allowedFields = [
        'author_id', 'category_id', 'title', 'slug', 'excerpt', 'content',
        'status', 'published_at', 'is_pinned', 'reading_minutes',
        'likes_count', 'comments_count', 'views_count', 'meta_title', 'meta_description'
    ];

    protected $validationRules = [
        'slug'    => 'required|min_length[3]|max_length[200]|is_unique[posts.slug,id,{id}]',
        'status'  => 'in_list[draft,scheduled,published]',
    ];

    /**
     * Consulta base para el Feed público:
     * - Publicadas o programadas cuya fecha ya venció
     * - Ordenadas primero por fijadas (is_pinned DESC) y luego por fecha (published_at DESC)
     */
    public function getFeed(int $limit = 10, int $offset = 0, ?int $categoryId = null): array
    {
        $now = date('Y-m-d H:i:s');
        $this->groupStart()
            ->where('status', 'published')
            ->orGroupStart()
                ->where('status', 'scheduled')
                ->where('published_at <=', $now)
            ->groupEnd()
        ->groupEnd();

        if ($categoryId) {
            $this->where('category_id', $categoryId);
        }

        $posts = $this->orderBy('is_pinned', 'DESC')
            ->orderBy('published_at', 'DESC')
            ->findAll($limit, $offset);

        $this->eagerLoadRelations($posts);
        return $posts;
    }

    /**
     * Busca una publicación por su slug para vista de detalle
     */
    public function findBySlug(string $slug, bool $onlyPublished = true): ?Post
    {
        if ($onlyPublished) {
            $now = date('Y-m-d H:i:s');
            $this->groupStart()
                ->where('status', 'published')
                ->orGroupStart()
                    ->where('status', 'scheduled')
                    ->where('published_at <=', $now)
                ->groupEnd()
            ->groupEnd();
        }

        /** @var Post|null $post */
        $post = $this->where('slug', $slug)->first();
        if ($post) {
            $list = [$post];
            $this->eagerLoadRelations($list);
        }
        return $post;
    }

    /**
     * Asocia la galería de medios ordenados a una lista de posts
     * @param array<Post> $posts
     */
    public function eagerLoadRelations(array $posts): void
    {
        if (empty($posts)) return;

        $postIds = array_map(fn($p) => $p->id, $posts);
        $categoryIds = array_filter(array_map(fn($p) => $p->category_id, $posts));

        // 1. Cargar Categorías
        if (! empty($categoryIds)) {
            $catModel = model(CategoryModel::class);
            $cats = $catModel->whereIn('id', array_unique($categoryIds))->findAll();
            $catMap = [];
            foreach ($cats as $c) {
                $catMap[$c->id] = $c;
            }
            foreach ($posts as $p) {
                if ($p->category_id && isset($catMap[$p->category_id])) {
                    $p->category = $catMap[$p->category_id];
                }
            }
        }

        // 2. Cargar Galería de Medios
        $db = \Config\Database::connect();
        $mediaPivot = $db->table('post_media')
            ->whereIn('post_id', $postIds)
            ->orderBy('position', 'ASC')
            ->get()
            ->getResultArray();

        if (! empty($mediaPivot)) {
            $mediaIds = array_unique(array_column($mediaPivot, 'media_id'));
            $mediaModel = model(MediaModel::class);
            $mediaItems = $mediaModel->whereIn('id', $mediaIds)->findAll();
            $mediaModel->loadVariants($mediaItems);

            $mediaMap = [];
            foreach ($mediaItems as $m) {
                $mediaMap[$m->id] = $m;
            }

            $postMediaMap = [];
            foreach ($mediaPivot as $row) {
                if (isset($mediaMap[$row['media_id']])) {
                    $postMediaMap[$row['post_id']][] = $mediaMap[$row['media_id']];
                }
            }

            foreach ($posts as $p) {
                $p->gallery = $postMediaMap[$p->id] ?? [];
            }
        }

        // 3. Cargar Comentarios aprobados (recientes)
        $commentModel = model(CommentModel::class);
        $comments = $commentModel->whereIn('post_id', $postIds)
            ->where('status', 'approved')
            ->orderBy('created_at', 'ASC')
            ->findAll();

        $commentsMap = [];
        foreach ($comments as $c) {
            $commentsMap[$c->post_id][] = $c;
        }

        foreach ($posts as $p) {
            $p->comments = $commentsMap[$p->id] ?? [];
        }
    }

    /**
     * Guarda la lista ordenada de IDs de medios para un post
     * @param list<int> $mediaIds
     */
    public function syncGallery(int $postId, array $mediaIds): void
    {
        $db = \Config\Database::connect();
        $db->table('post_media')->where('post_id', $postId)->delete();

        if (empty($mediaIds)) return;

        $rows = [];
        $pos = 0;
        foreach ($mediaIds as $mId) {
            $mId = (int) $mId;
            if ($mId > 0) {
                $rows[] = [
                    'post_id'  => $postId,
                    'media_id' => $mId,
                    'position' => $pos++,
                ];
            }
        }

        if (! empty($rows)) {
            $db->table('post_media')->insertBatch($rows);
        }
    }
}
