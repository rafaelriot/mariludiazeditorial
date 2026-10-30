<?php

declare(strict_types=1);

namespace App\Controllers\Site;

use App\Controllers\BaseController;
use App\Models\PostLikeModel;
use App\Models\PostModel;

class Likes extends BaseController
{
    /**
     * Alterna "Me gusta" (con usuario autenticado o visitante)
     */
    public function toggle(int $id)
    {
        $postModel = model(PostModel::class);
        $post = $postModel->find($id);

        if (! $post) {
            return $this->response->setJSON(['success' => false, 'message' => 'Historia no encontrada.'])->setStatusCode(404);
        }

        $userId = auth()->id();
        $cookieName = 'md_v_id';
        $vId = $this->request->getCookie($cookieName);
        if (! $vId) {
            $vId = bin2hex(random_bytes(16));
            setcookie($cookieName, $vId, [
                'expires'  => time() + (86400 * 365),
                'path'     => '/',
                'httponly' => true,
                'samesite' => 'Lax',
            ]);
        }
        $visitorHash = hash('sha256', $vId . $this->request->getIPAddress());

        $likeModel = model(PostLikeModel::class);
        $result = $likeModel->toggleLike($id, $userId, $visitorHash);

        return $this->response->setJSON([
            'success'     => true,
            'liked'       => $result['liked'],
            'likes_count' => $result['count'],
            'csrf_hash'   => csrf_hash(),
        ]);
    }
}

