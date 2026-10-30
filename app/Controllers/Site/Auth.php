<?php

declare(strict_types=1);

namespace App\Controllers\Site;

use App\Controllers\BaseController;
use App\Services\GoogleAuthService;

class Auth extends BaseController
{
    protected GoogleAuthService $googleAuth;

    public function __construct()
    {
        $this->googleAuth = new GoogleAuthService();
    }

    /**
     * Redirige al flujo de login con Google
     */
    public function google()
    {
        $returnTo = $this->request->getGet('return') ?: (previous_url() ?: '/');
        $authUrl = $this->googleAuth->getAuthUrl($returnTo);
        return redirect()->to($authUrl);
    }

    /**
     * Callback de retorno de Google
     */
    public function googleCallback()
    {
        $code = $this->request->getGet('code');
        if (! $code) {
            return redirect()->to('/')->with('error', 'No se recibió autorización de Google.');
        }

        $user = $this->googleAuth->handleCallback((string) $code);
        if (! $user) {
            return redirect()->to('/')->with('error', 'Error al verificar tu cuenta de Google.');
        }

        $returnTo = session()->get('google_auth_return') ?: '/';
        session()->remove('google_auth_return');
        return redirect()->to($returnTo)->with('message', '¡Bienvenida/o! Has iniciado sesión con Google.');
    }

    /**
     * Simulador local para desarrollo cuando no hay credenciales de Google en .env
     */
    public function googleMock()
    {
        if ($this->request->is('post')) {
            $email = (string) $this->request->getPost('email');
            $name = (string) $this->request->getPost('name');

            if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return redirect()->back()->with('error', 'Ingresa un correo de Gmail válido.');
            }

            $user = $this->googleAuth->findOrCreateUser($email, $name);
            $returnTo = session()->get('google_auth_return') ?: '/';
            session()->remove('google_auth_return');
            return redirect()->to($returnTo)->with('message', "Sesión iniciada como {$name}.");
        }

        return view('auth/google_mock', [
            'returnTo' => session()->get('google_auth_return') ?: '/',
        ]);
    }

    /**
     * Cierre de sesión de lectores
     */
    public function logout()
    {
        session()->remove('reader_profile');
        auth()->logout();
        return redirect()->to(previous_url() ?: '/')->with('message', 'Sesión cerrada correctamente.');
    }
}
