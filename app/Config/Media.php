<?php

declare(strict_types=1);

namespace Config;

use CodeIgniter\Config\BaseConfig;

/**
 * Configuración de la gestión multimedia (conversión WebP + variantes).
 */
class Media extends BaseConfig
{
    /**
     * Carpeta pública (relativa a FCPATH) donde se guardan las imágenes procesadas.
     */
    public string $publicDir = 'uploads';

    /**
     * Tamaño máximo de subida en KB (10 MB).
     */
    public int $maxSizeKb = 10240;

    /**
     * Máximo de imágenes por historia.
     */
    public int $maxPerPost = 10;

    /**
     * MIME aceptados (validados contra el contenido real del archivo).
     *
     * @var list<string>
     */
    public array $allowedMimes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];

    /**
     * Calidad WebP (0-100).
     */
    public int $quality = 80;

    /**
     * Variantes generadas. `crop` = recorte cuadrado centrado.
     *
     * @var array<string, array{width: int, height?: int, crop?: bool}>
     */
    public array $variants = [
        'thumb' => ['width' => 400, 'height' => 400, 'crop' => true],
        'sm'    => ['width' => 480],
        'md'    => ['width' => 960],
        'lg'    => ['width' => 1600],
    ];
}
