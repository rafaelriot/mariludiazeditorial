<?php

declare(strict_types=1);

namespace App\Models;

use App\Entities\Comment;
use CodeIgniter\Model;

class CommentModel extends Model
{
    protected $table         = 'post_comments';
    protected $primaryKey    = 'id';
    protected $returnType    = Comment::class;
    protected $useTimestamps = true;
    protected $useSoftDeletes= true;
    protected $allowedFields = [
        'post_id', 'user_id', 'author_name', 'author_email',
        'author_avatar', 'content', 'status'
    ];

    protected $validationRules = [
        'post_id'     => 'required|is_natural_no_zero',
        'author_name' => 'required|min_length[2]|max_length[120]',
        'content'     => 'required|min_length[2]|max_length[3000]',
    ];

    /**
     * Retorna los comentarios aprobados de una publicación
     */
    public function getApprovedForPost(int $postId, int $limit = 50): array
    {
        return $this->where('post_id', $postId)
            ->where('status', 'approved')
            ->orderBy('created_at', 'ASC')
            ->findAll($limit);
    }
}
