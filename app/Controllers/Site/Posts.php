<?php

declare(strict_types=1);

namespace App\Controllers\Site;

use App\Controllers\BaseController;
use App\Models\CommentModel;
use App\Models\PostLikeModel;
use App\Models\PostModel;

class Posts extends BaseController
{
    protected PostModel $postModel;
    protected CommentModel $commentModel;
    protected PostLikeModel $likeModel;

    public function __construct()
    {
        $this->postModel = model(PostModel::class);
        $this->commentModel = model(CommentModel::class);
        $this->likeModel = model(PostLikeModel::class);
    }

    /**
     * Vista de lectura individual de una historia
     */
    public function show(string $slug)
    {
        $post = $this->postModel->findBySlug($slug, true);
        if (! $post) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Historia '{$slug}' no encontrada.");
        }

        // Incrementar contador de vistas sin bloquear
        $this->postModel->update($post->id, ['views_count' => $post->views_count + 1]);

        $userId = auth()->id();
        $visitorHash = $this->getVisitorHash();
        $post->has_liked = $this->likeModel->hasLiked($post->id, $userId, $visitorHash);

        $comments = $this->commentModel->getApprovedForPost($post->id);

        return view('site/post_detail', [
            'post'      => $post,
            'comments'  => $comments,
            'pageTitle' => ($post->title ?: 'Historia') . ' — Marilú Díaz',
        ]);
    }

    /**
     * Enviar un comentario a una historia
     */
    public function comment(int $id)
    {
        $post = $this->postModel->find($id);
        if (! $post) {
            return $this->response->setJSON(['success' => false, 'message' => 'Historia no encontrada.'])->setStatusCode(404);
        }

        // Verificar si el usuario está autenticado con Google o Shield
        if (! auth()->loggedIn()) {
            $returnUrl = base_url("historia/{$post->slug}");
            $googleLoginUrl = base_url("auth/google?return=" . urlencode($returnUrl));

            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success'  => false,
                    'redirect' => $googleLoginUrl,
                    'message'  => 'Inicia sesión con tu cuenta de Google para comentar.',
                ]);
            }
            return redirect()->to($googleLoginUrl);
        }

        $content = trim((string) $this->request->getPost('content'));
        if (mb_strlen($content) < 2) {
            return $this->response->setJSON(['success' => false, 'message' => 'El comentario es muy corto.'])->setStatusCode(422);
        }

        $user = auth()->user();
        $profile = session()->get('reader_profile') ?: [];

        $authorName = $profile['name'] ?? ($user->username ?: 'Lector');
        $authorEmail = $profile['email'] ?? $user->email;
        $authorAvatar = $profile['avatar'] ?? null;

        $commentId = $this->commentModel->insert([
            'post_id'       => $id,
            'user_id'       => $user->id,
            'author_name'   => $authorName,
            'author_email'  => $authorEmail,
            'author_avatar' => $authorAvatar,
            'content'       => esc($content),
            'status'        => 'approved',
        ]);

        // Actualizar contador en post
        $newCount = $this->commentModel->where('post_id', $id)->where('status', 'approved')->countAllResults();
        $this->postModel->update($id, ['comments_count' => $newCount]);

        $comment = $this->commentModel->find($commentId);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'success'   => true,
                'comment'   => [
                    'id'          => $comment->id,
                    'author_name' => $comment->author_name,
                    'avatar'      => $comment->author_avatar,
                    'content'     => $comment->content,
                    'time'        => 'hace un momento',
                ],
                'csrf_hash' => csrf_hash(),
            ]);
        }

        return redirect()->to("historia/{$post->slug}#comentarios")->with('message', '¡Comentario publicado!');
    }

    protected function getVisitorHash(): string
    {
        $cookieName = 'md_v_id';
        $vId = $this->request->getCookie($cookieName) ?: bin2hex(random_bytes(16));
        return hash('sha256', $vId . $this->request->getIPAddress());
    }
}
