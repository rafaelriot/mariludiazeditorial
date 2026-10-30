<?php

declare(strict_types=1);

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class Post extends Entity
{
    protected $datamap = [];
    protected $dates   = ['published_at', 'created_at', 'updated_at', 'deleted_at'];
    protected $casts   = [
        'id'              => 'integer',
        'author_id'       => '?integer',
        'category_id'     => '?integer',
        'is_pinned'       => 'boolean',
        'reading_minutes' => 'integer',
        'likes_count'     => 'integer',
        'comments_count'  => 'integer',
        'views_count'     => 'integer',
    ];

    /**
     * Galería de objetos Media asociados
     * @var array<Media>
     */
    public array $gallery = [];

    /**
     * Categoría asociada
     * @var Category|null
     */
    public ?Category $category = null;

    /**
     * Etiquetas asociadas
     * @var array<Tag>
     */
    public array $tags = [];

    /**
     * Comentarios asociados
     * @var array<Comment>
     */
    public array $comments = [];

    /**
     * Retorna la imagen principal de portada (la primera de la galería)
     */
    public function getCover(): ?Media
    {
        return $this->gallery[0] ?? null;
    }

    /**
     * Retorna la fecha formateada en lenguaje natural
     */
    public function getFormattedDate(): string
    {
        $date = $this->published_at ?? $this->created_at;
        if (! $date) {
            return '';
        }
        return $date->toLocalizedString('d MMMM, yyyy');
    }

    /**
     * Retorna el tiempo relativo (hace X horas / días)
     */
    public function getRelativeTime(): string
    {
        $date = $this->published_at ?? $this->created_at;
        if (! $date) {
            return '';
        }
        $diff = time() - $date->getTimestamp();
        if ($diff < 60) {
            return 'hace un momento';
        }
        if ($diff < 3600) {
            $mins = (int) floor($diff / 60);
            return "hace {$mins} min";
        }
        if ($diff < 86400) {
            $hrs = (int) floor($diff / 3600);
            return "hace {$hrs} h";
        }
        if ($diff < 604800) {
            $days = (int) floor($diff / 86400);
            return "hace {$days} d";
        }
        return $this->getFormattedDate();
    }
}

