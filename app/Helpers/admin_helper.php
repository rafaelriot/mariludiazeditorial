<?php

declare(strict_types=1);

if (! function_exists('admin_path')) {
    /**
     * Segmento de URL del panel administrativo.
     * Se configura en .env con ADMIN_PATH (por defecto: "admin").
     * Cambiarlo dificulta que bots localicen el panel.
     */
    function admin_path(): string
    {
        $path = trim((string) env('ADMIN_PATH', 'admin'), '/ ');

        return preg_match('/^[a-z0-9\-_]+$/i', $path) === 1 ? $path : 'admin';
    }
}

if (! function_exists('admin_url')) {
    /**
     * URL absoluta dentro del panel administrativo.
     * Ej.: admin_url('historias/nueva') → https://dominio.com/{ADMIN_PATH}/historias/nueva
     */
    function admin_url(string $uri = ''): string
    {
        $uri = ltrim($uri, '/');

        return base_url(admin_path() . ($uri !== '' ? '/' . $uri : ''));
    }
}

