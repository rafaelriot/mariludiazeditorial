<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

class PostLikeModel extends Model
{
    protected $table         = 'post_likes';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = false;
    protected $allowedFields = ['post_id', 'user_id', 'visitor_hash', 'created_at'];

    /**
     * Alterna el like de un usuario o visitante y retorna el nuevo estado
     * @return array{liked: bool, count: int}
     */
    public function toggleLike(int $postId, ?int $userId, ?string $visitorHash): array
    {
        $builder = $this->builder();
        $builder->where('post_id', $postId);

        if ($userId) {
            $builder->where('user_id', $userId);
        } else {
            $builder->where('visitor_hash', $visitorHash);
        }

        $existing = $builder->get()->getRow();

        if ($existing) {
            // Quitar like
            $this->delete($existing->id);
            $liked = false;
        } else {
            // Agregar like
            $this->insert([
                'post_id'      => $postId,
                'user_id'      => $userId,
                'visitor_hash' => $visitorHash,
                'created_at'   => date('Y-m-d H:i:s'),
            ]);
            $liked = true;
        }

        // Recalcular contador en posts
        $count = $this->where('post_id', $postId)->countAllResults();
        $postModel = model(PostModel::class);
        $postModel->update($postId, ['likes_count' => $count]);

        return ['liked' => $liked, 'count' => $count];
    }

    /**
     * Revisa si un usuario o visitante ya dio like a un post
     */
    public function hasLiked(int $postId, ?int $userId, ?string $visitorHash): bool
    {
        $builder = $this->builder()->where('post_id', $postId);
        if ($userId) {
            $builder->where('user_id', $userId);
        } elseif ($visitorHash) {
            $builder->where('visitor_hash', $visitorHash);
        } else {
            return false;
        }
        return $builder->countAllResults() > 0;
    }
}
