<?php

declare(strict_types=1);

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class Subscriber extends Entity
{
    protected $dates = ['confirmed_at', 'created_at', 'updated_at'];
    protected $casts = [
        'id' => 'integer',
    ];
}
