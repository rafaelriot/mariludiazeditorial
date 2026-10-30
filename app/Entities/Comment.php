<?php

declare(strict_types=1);

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class Comment extends Entity
{
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];
    protected $casts = [
        'id'      => 'integer',
        'post_id' => 'integer',
        'user_id' => '?integer',
    ];

    public function getRelativeTime(): string
    {
        if (! $this->created_at) {
            return '';
        }
        $diff = time() - $this->created_at->getTimestamp();
        if ($diff < 60) return 'hace un momento';
        if ($diff < 3600) return 'hace ' . (int) floor($diff / 60) . ' min';
        if ($diff < 86400) return 'hace ' . (int) floor($diff / 3600) . ' h';
        return 'hace ' . (int) floor($diff / 86400) . ' d';
    }
}

