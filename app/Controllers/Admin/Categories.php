<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\CategoryModel;

class Categories extends BaseController
{
    protected CategoryModel $categoryModel;

    public function __construct()
    {
        $this->categoryModel = model(CategoryModel::class);
    }

    public function index()
    {
        $categories = $this->categoryModel->orderBy('sort_order', 'ASC')->findAll();

        return view('admin/categories/index', [
            'categories' => $categories,
        ]);
    }

    public function create()
    {
        $name = trim((string) $this->request->getPost('name'));
        $slug = trim((string) $this->request->getPost('slug')) ?: mb_url_title($name, '-', true);
        $tone = (string) $this->request->getPost('tone') ?: 'navy';

        $data = [
            'name'        => $name,
            'slug'        => $slug,
            'description' => trim((string) $this->request->getPost('description')) ?: null,
            'tone'        => $tone,
            'sort_order'  => (int) $this->request->getPost('sort_order'),
        ];

        if (! $this->categoryModel->save($data)) {
            return redirect()->back()->withInput()->with('error', implode(' ', $this->categoryModel->errors()));
        }

        return redirect()->to(admin_url('categorias'))->with('message', 'Categoría creada correctamente.');
    }

    public function update(int $id)
    {
        $category = $this->categoryModel->find($id);
        if (! $category) {
            return redirect()->back()->with('error', 'Categoría no encontrada.');
        }

        $name = trim((string) $this->request->getPost('name'));
        $slug = trim((string) $this->request->getPost('slug')) ?: mb_url_title($name, '-', true);
        $tone = (string) $this->request->getPost('tone') ?: 'navy';

        $data = [
            'id'          => $id,
            'name'        => $name,
            'slug'        => $slug,
            'description' => trim((string) $this->request->getPost('description')) ?: null,
            'tone'        => $tone,
            'sort_order'  => (int) $this->request->getPost('sort_order'),
        ];

        if (! $this->categoryModel->save($data)) {
            return redirect()->back()->withInput()->with('error', implode(' ', $this->categoryModel->errors()));
        }

        return redirect()->to(admin_url('categorias'))->with('message', 'Categoría actualizada.');
    }

    public function delete(int $id)
    {
        $this->categoryModel->delete($id);
        return redirect()->to(admin_url('categorias'))->with('message', 'Categoría eliminada.');
    }
}

