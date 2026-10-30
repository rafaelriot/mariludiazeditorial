<?php

declare(strict_types=1);

namespace App\Services;

use CodeIgniter\Shield\Entities\User;
use CodeIgniter\Shield\Models\UserModel;

class GoogleAuthService
{
    protected string $clientId;
    protected string $clientSecret;
    protected string $redirectUri;

    public function __construct()
    {
        $this->clientId = env('google.clientId', '');
        $this->clientSecret = env('google.clientSecret', '');
        $this->redirectUri = base_url('auth/google/callback');
    }

    /**
     * Retorna si las credenciales de Google están configuradas
     */
    public function isConfigured(): bool
    {
        return ! empty($this->clientId) && ! empty($this->clientSecret);
    }

    /**
     * Genera la URL para iniciar sesión con Google
     */
    public function getAuthUrl(string $returnTo = '/'): string
    {
        session()->set('google_auth_return', $returnTo);

        if (! $this->isConfigured()) {
            // Modo simulador local si aún no han colocado las credenciales en .env
            return base_url('auth/google/mock');
        }

        $params = [
            'client_id'     => $this->clientId,
            'redirect_uri'  => $this->redirectUri,
            'response_type' => 'code',
            'scope'         => 'openid email profile',
            'access_type'   => 'online',
            'prompt'        => 'select_account',
            'state'         => csrf_hash(),
        ];

        return 'https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query($params);
    }

    /**
     * Procesa el código de retorno de Google y autentica al usuario en Shield
     * @return User|null
     */
    public function handleCallback(string $code): ?User
    {
        $tokenUrl = 'https://oauth2.googleapis.com/token';
        $postData = [
            'code'          => $code,
            'client_id'     => $this->clientId,
            'client_secret' => $this->clientSecret,
            'redirect_uri'  => $this->redirectUri,
            'grant_type'    => 'authorization_code',
        ];

        $client = \Config\Services::curlrequest();
        try {
            $response = $client->post($tokenUrl, ['form_params' => $postData]);
            $tokens = json_decode($response->getBody(), true);
            if (empty($tokens['access_token'])) {
                return null;
            }

            // Obtener perfil del usuario
            $userinfoRes = $client->get('https://www.googleapis.com/oauth2/v3/userinfo', [
                'headers' => ['Authorization' => 'Bearer ' . $tokens['access_token']]
            ]);
            $profile = json_decode($userinfoRes->getBody(), true);

            return $this->findOrCreateUser($profile['email'], $profile['name'] ?? '', $profile['picture'] ?? null);
        } catch (\Throwable $e) {
            log_message('error', 'Google OAuth Error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Busca o crea un usuario en Shield para el lector de Google
     */
    public function findOrCreateUser(string $email, string $name, ?string $avatar = null): User
    {
        /** @var UserModel $userModel */
        $userModel = model(UserModel::class);
        $user = $userModel->findByCredentials(['email' => $email]);

        if (! $user) {
            $username = strtolower(explode('@', $email)[0]) . '_' . substr(bin2hex(random_bytes(2)), 0, 4);
            $user = new User([
                'username' => $username,
                'email'    => $email,
                'password' => bin2hex(random_bytes(16)),
                'active'   => 1,
            ]);
            $userModel->save($user);
            $user = $userModel->findById($userModel->getInsertID());

            // Asignar al grupo por defecto (lector)
            $user->addGroup('editor'); // En Shield los grupos por defecto
        }

        // Guardar metadata en sesión para mostrar el nombre y avatar
        session()->set('reader_profile', [
            'name'   => $name ?: $user->username,
            'email'  => $email,
            'avatar' => $avatar,
        ]);

        auth()->login($user);
        return $user;
    }
}

