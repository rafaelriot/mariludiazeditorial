<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\CommentModel;
use App\Models\PostModel;

class Comments extends BaseController
{
    protected CommentModel $commentModel;

    public function __construct()
    {
        $this->commentModel = model(CommentModel::class);
    }

    /**
     * Listado y moderación de comentarios
     */
    public function index()
    {
        $status = $this->request->getGet('status');

        $query = $this->commentModel->orderBy('created_at', 'DESC');
        if ($status && in_array($status, ['approved', 'pending', 'spam'], true)) {
            $query->where('status', $status);
        }

        $comments = $query->paginate(20);

        // Cargar posts asociados
        if (! empty($comments)) {
            $postIds = array_unique(array_map(fn($c) => $c->post_id, $comments));
            $postModel = model(PostModel::class);
            $posts = $postModel->whereIn('id', $postIds)->findAll();
            $postMap = [];
            foreach ($posts as $p) {
                $postMap[$p->id] = $p;
            }
            foreach ($comments as $c) {
                $c->post = $postMap[$c->post_id] ?? null;
            }
        }

        $counts = [
            'all'      => $this->commentModel->countAllResults(false),
            'approved' => $this->commentModel->where('status', 'approved')->countAllResults(false),
            'pending'  => $this->commentModel->where('status', 'pending')->countAllResults(false),
            'spam'     => $this->commentModel->where('status', 'spam')->countAllResults(false),
        ];

        return view('admin/comments/index', [
            'comments'      => $comments,
            'pager'         => $this->commentModel->pager,
            'currentStatus' => $status,
            'counts'        => $counts,
            'pageTitle'     => 'Moderación de Comentarios',
        ]);
    }

    /**
     * Cambiar estado de un comentario (aprobar, spam, pendiente)
     */
    public function setStatus(int $id, string $status)
    {
        if (! in_array($status, ['approved', 'pending', 'spam'], true)) {
            return redirect()->back()->with('error', 'Estado inválido.');
        }

        $comment = $this->commentModel->find($id);
        if ($comment) {
            $this->commentModel->update($id, ['status' => $status]);

            // Actualizar contador en post
            $count = $this->commentModel->where('post_id', $comment->post_id)->where('status', 'approved')->countAllResults();
            model(PostModel::class)->update($comment->post_id, ['comments_count' => $count]);
        }

        return redirect()->back()->with('message', "Comentario actualizado a estado '{$status}'.");
    }

    /**
     * Eliminar comentario definitivamente
     */
    public function delete(int $id)
    {
        $comment = $this->commentModel->find($id);
        if ($comment) {
            $this->commentModel->delete($id, true);

            $count = $this->commentModel->where('post_id', $comment->post_id)->where('status', 'approved')->countAllResults();
            model(PostModel::class)->update($comment->post_id, ['comments_count' => $count]);
        }

        return redirect()->back()->with('message', 'Comentario eliminado.');
    }
}

