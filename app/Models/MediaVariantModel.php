<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

class MediaVariantModel extends Model
{
    protected $table         = 'media_variants';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = false;
    protected $allowedFields = ['media_id', 'size', 'path', 'width', 'height', 'bytes'];
}
