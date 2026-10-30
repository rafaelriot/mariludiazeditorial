<?php

declare(strict_types=1);

namespace App\Models;

use App\Entities\Tag;
use CodeIgniter\Model;

class TagModel extends Model
{
    protected $table         = 'tags';
    protected $primaryKey    = 'id';
    protected $returnType    = Tag::class;
    protected $useTimestamps = true;
    protected $allowedFields = ['name', 'slug'];

    protected $validationRules = [
        'name' => 'required|min_length[2]|max_length[80]',
        'slug' => 'required|min_length[2]|max_length[100]|is_unique[tags.slug,id,{id}]',
    ];

    /**
     * Busca o crea etiquetas a partir de un arreglo de nombres
     * @param list<string> $names
     * @return list<int> IDs de las etiquetas
     */
    public function findOrCreateMany(array $names): array
    {
        $ids = [];
        foreach ($names as $name) {
            $cleanName = trim($name);
            if (mb_strlen($cleanName) < 2) continue;

            $slug = mb_url_title($cleanName, '-', true);
            $existing = $this->where('slug', $slug)->first();

            if ($existing) {
                $ids[] = $existing->id;
            } else {
                $newId = (int) $this->insert([
                    'name' => $cleanName,
                    'slug' => $slug,
                ]);
                $ids[] = $newId;
            }
        }
        return array_unique($ids);
    }
}
