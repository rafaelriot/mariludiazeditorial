<?php

declare(strict_types=1);

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class Category extends Entity
{
    protected $dates = ['created_at', 'updated_at'];
    protected $casts = [
        'id'         => 'integer',
        'sort_order' => 'integer',
    ];

    public function getToneClasses(): array
    {
        return match ($this->tone) {
            'rose' => [
                'bg'   => 'bg-[#D996A6]/20',
                'text' => 'text-[#8E4B5B]',
                'dot'  => 'bg-[#D996A6]',
            ],
            'sage' => [
                'bg'   => 'bg-[#A8B58A]/25',
                'text' => 'text-[#4F5C38]',
                'dot'  => 'bg-[#A8B58A]',
            ],
            default => [
                'bg'   => 'bg-[#063B73]/10',
                'text' => 'text-[#063B73]',
                'dot'  => 'bg-[#063B73]',
            ],
        };
    }
}
