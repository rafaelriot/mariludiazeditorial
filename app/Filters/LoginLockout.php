<?php

declare(strict_types=1);

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Bloqueo de fuerza bruta en el inicio de sesión.
 *
 * Shield registra cada intento de login en la tabla `auth_logins`
 * (IP, identificador, fecha y si fue exitoso). Este filtro cuenta los
 * intentos FALLIDOS de la IP actual dentro de la ventana de tiempo y,
 * si alcanza el máximo permitido, bloquea nuevos intentos hasta que
 * la ventana expire.
 */
class LoginLockout implements FilterInterface
{
    /** Intentos fallidos permitidos antes del bloqueo */
    public const MAX_ATTEMPTS = 5;

    /** Duración de la ventana / bloqueo en minutos */
    public const LOCKOUT_MINUTES = 15;

    public function before(RequestInterface $request, $arguments = null)
    {
        if (strtolower($request->getMethod()) !== 'post') {
            return null;
        }

        $ip    = $request->getIPAddress();
        $since = date('Y-m-d H:i:s', time() - (self::LOCKOUT_MINUTES * 60));

        $failed = db_connect()->table('auth_logins')
            ->where('ip_address', $ip)
            ->where('success', 0)
            ->where('date >=', $since)
            ->countAllResults();

        if ($failed >= self::MAX_ATTEMPTS) {
            log_message('warning', "Login bloqueado para la IP {$ip}: {$failed} intentos fallidos en " . self::LOCKOUT_MINUTES . ' min.');

            return redirect()->to(url_to('login'))->with(
                'error',
                'Demasiados intentos fallidos. Por seguridad, el acceso desde tu conexión se bloqueó durante '
                . self::LOCKOUT_MINUTES . ' minutos.'
            );
        }

        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        return null;
    }
}

