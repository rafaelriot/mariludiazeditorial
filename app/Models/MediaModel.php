<?php

declare(strict_types=1);

namespace App\Models;

use App\Entities\Media;
use CodeIgniter\Model;

class MediaModel extends Model
{
    protected $table         = 'media';
    protected $primaryKey    = 'id';
    protected $returnType    = Media::class;
    protected $useTimestamps = true;
    protected $allowedFields = [
        'original_name', 'path', 'mime', 'width', 'height', 'bytes',
        'alt_text', 'caption', 'dominant_color', 'uploaded_by'
    ];

    /**
     * Carga las variantes para uno o varios objetos Media
     * @param Media|array<Media> $media
     */
    public function loadVariants(Media|array &$media): void
    {
        $isArray = is_array($media);
        $items = $isArray ? $media : [$media];
        if (empty($items)) return;

        $ids = array_map(fn($m) => $m->id, $items);
        $variantModel = model(MediaVariantModel::class);
        $variants = $variantModel->whereIn('media_id', $ids)->findAll();

        $map = [];
        foreach ($variants as $v) {
            $map[$v['media_id']][$v['size']] = [
                'path'   => $v['path'],
                'width'  => (int) $v['width'],
                'height' => (int) $v['height'],
                'bytes'  => (int) $v['bytes'],
            ];
        }

        foreach ($items as $m) {
            $m->variants = $map[$m->id] ?? [];
        }
    }
}

