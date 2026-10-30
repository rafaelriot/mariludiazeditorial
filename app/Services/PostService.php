<?php

declare(strict_types=1);

namespace App\Services;

use App\Entities\Post;
use App\Models\PostModel;
use HTMLPurifier;
use HTMLPurifier_Config;

class PostService
{
    protected PostModel $postModel;
    protected HTMLPurifier $purifier;

    public function __construct(?PostModel $postModel = null)
    {
        $this->postModel = $postModel ?? model(PostModel::class);

        $config = HTMLPurifier_Config::createDefault();
        $config->set('HTML.Allowed', 'p,b,strong,i,em,u,a[href|title|target|rel],ul,ol,li,blockquote,h2,h3,h4,hr,br,img[src|alt|title|width|height]');
        $config->set('HTML.TargetBlank', true);
        $config->set('HTML.Nofollow', true);
        $config->set('URI.AllowedSchemes', ['http' => true, 'https' => true, 'mailto' => true]);
        $config->set('Cache.SerializerPath', WRITEPATH . 'cache');

        $this->purifier = new HTMLPurifier($config);
    }

    /**
     * Purifica y guarda una publicación
     * @param array<string, mixed> $data
     * @param list<int> $galleryMediaIds
     * @param list<string> $tagNames
     */
    public function savePost(array $data, array $galleryMediaIds = [], array $tagNames = [], ?int $id = null): Post
    {
        // 1. Purificar HTML
        if (! empty($data['content'])) {
            $data['content'] = $this->purifier->purify($data['content']);
        }

        // 2. Generar excerpt si viene vacío
        if (empty($data['excerpt']) && ! empty($data['content'])) {
            $cleanText = strip_tags($data['content']);
            $data['excerpt'] = mb_strimwidth($cleanText, 0, 240, '...');
        }

        // 3. Estimar minutos de lectura (200 palabras por minuto)
        if (! empty($data['content'])) {
            $words = str_word_count(strip_tags($data['content']));
            $data['reading_minutes'] = max(1, (int) ceil($words / 200));
        }

        // 4. Asegurar Slug único
        if (empty($data['slug'])) {
            $baseText = ! empty($data['title']) ? $data['title'] : ($data['excerpt'] ?? 'historia-' . date('Ymd-His'));
            $data['slug'] = mb_url_title($baseText, '-', true);
        }
        $data['slug'] = $this->ensureUniqueSlug($data['slug'], $id);

        // 5. Fechas según estado
        if (($data['status'] ?? 'draft') === 'published' && empty($data['published_at'])) {
            $data['published_at'] = date('Y-m-d H:i:s');
        }

        if ($id) {
            $this->postModel->update($id, $data);
            $postId = $id;
        } else {
            $postId = (int) $this->postModel->insert($data);
        }

        // 6. Sincronizar galería
        $this->postModel->syncGallery($postId, $galleryMediaIds);

        // 7. Sincronizar etiquetas
        if (! empty($tagNames)) {
            $tagModel = model(\App\Models\TagModel::class);
            $tagIds = $tagModel->findOrCreateMany($tagNames);
            $this->postModel->syncTags($postId, $tagIds);
        } else {
            $this->postModel->syncTags($postId, []);
        }

        /** @var Post $post */
        $post = $this->postModel->find($postId);
        $this->postModel->eagerLoadRelations([$post]);
        return $post;
    }

    /**
     * Garantiza que el slug sea único
     */
    protected function ensureUniqueSlug(string $slug, ?int $ignoreId = null): string
    {
        $originalSlug = $slug;
        $counter = 1;

        while (true) {
            $builder = $this->postModel->where('slug', $slug);
            if ($ignoreId) {
                $builder->where('id !=', $ignoreId);
            }
            if ($builder->countAllResults() === 0) {
                break;
            }
            $slug = "{$originalSlug}-{$counter}";
            $counter++;
        }

        return $slug;
    }
}
