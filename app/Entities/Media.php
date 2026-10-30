<?php

declare(strict_types=1);

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class Media extends Entity
{
    protected $dates = ['created_at', 'updated_at'];
    protected $casts = [
        'id'          => 'integer',
        'width'       => 'integer',
        'height'      => 'integer',
        'bytes'       => 'integer',
        'uploaded_by' => '?integer',
    ];

    /**
     * @var array<string, array{path: string, width: int, height: int, bytes: int}>
     */
    public array $variants = [];

    /**
     * Retorna la URL de una variante ('thumb', 'sm', 'md', 'lg')
     */
    public function getUrl(string $size = 'md'): string
    {
        if (isset($this->variants[$size])) {
            return base_url($this->variants[$size]['path']);
        }
        return base_url($this->path);
    }
}
