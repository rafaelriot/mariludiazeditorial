<?php

declare(strict_types=1);

namespace App\Models;

use App\Entities\Category;
use CodeIgniter\Model;

class CategoryModel extends Model
{
    protected $table            = 'categories';
    protected $primaryKey       = 'id';
    protected $returnType       = Category::class;
    protected $useTimestamps    = true;
    protected $allowedFields    = ['name', 'slug', 'description', 'tone', 'sort_order'];

    protected $validationRules = [
        'name' => 'required|min_length[2]|max_length[80]',
        'slug' => 'required|min_length[2]|max_length[100]|is_unique[categories.slug,id,{id}]',
        'tone' => 'in_list[navy,rose,sage]',
    ];
}
