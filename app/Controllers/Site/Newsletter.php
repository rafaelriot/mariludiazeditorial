<?php

declare(strict_types=1);

namespace App\Controllers\Site;

use App\Controllers\BaseController;
use App\Models\SubscriberModel;

class Newsletter extends BaseController
{
    /**
     * Suscripción AJAX al boletín semanal
     */
    public function subscribe()
    {
        $email = trim(strtolower((string) $this->request->getPost('email')));

        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->response->setJSON([
                'success'   => false,
                'message'   => 'Por favor ingresa un correo electrónico válido.',
                'csrf_hash' => csrf_hash(),
            ])->setStatusCode(422);
        }

        $subscriberModel = model(SubscriberModel::class);
        $existing = $subscriberModel->where('email', $email)->first();

        if ($existing) {
            if ($existing->status === 'unsubscribed') {
                $subscriberModel->update($existing->id, ['status' => 'active']);
            }
            return $this->response->setJSON([
                'success'   => true,
                'message'   => '¡Ya formas parte de la correspondencia dominical!',
                'csrf_hash' => csrf_hash(),
            ]);
        }

        $token = bin2hex(random_bytes(16));
        $subscriberModel->insert([
            'email'        => $email,
            'token'        => $token,
            'status'       => 'active',
            'source'       => 'web_feed',
            'confirmed_at' => date('Y-m-d H:i:s'),
        ]);

        return $this->response->setJSON([
            'success'   => true,
            'message'   => '¡Te has suscrito con éxito a la correspondencia semanal!',
            'csrf_hash' => csrf_hash(),
        ]);
    }

    /**
     * Cancelar suscripción
     */
    public function unsubscribe(string $token)
    {
        $subscriberModel = model(SubscriberModel::class);
        $subscriber = $subscriberModel->where('token', $token)->first();

        if ($subscriber) {
            $subscriberModel->update($subscriber->id, ['status' => 'unsubscribed']);
        }

        return view('site/newsletter_unsubscribed', [
            'pageTitle' => 'Suscripción Cancelada — Marilú Díaz',
        ]);
    }
}

