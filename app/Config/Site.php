<?php

declare(strict_types=1);

namespace Config;

use CodeIgniter\Config\BaseConfig;

/**
 * Valores por defecto del sitio. Los editables desde el panel se guardan con
 * `setting('Site.xxx', $valor)` (tabla `settings` de codeigniter4/settings),
 * por lo que estas propiedades actúan como valores iniciales.
 */
class Site extends BaseConfig
{
    public string $name        = 'Marilú Díaz';
    public string $tagline     = 'Historias, reflexiones y liderazgo con propósito';
    public string $description = 'Blog personal de Marilú Díaz: historias, ensayos y reflexiones sobre liderazgo consciente, crecimiento personal y propósito.';
    public string $bio         = 'Mentora, escritora y conferencista. Comparto historias y reflexiones para liderar con serenidad, propósito y humanidad.';
    public string $location    = 'México';

    /**
     * Texto largo de la página "Sobre mí" (HTML permitido, se purifica al guardar).
     */
    public string $about = '<p>Soy Marilú Díaz. Durante más de una década he acompañado a líderes y equipos en procesos de transformación. Este espacio es mi cuaderno abierto: aquí comparto historias, aprendizajes y reflexiones.</p>';

    /**
     * Redes sociales (vacías = no se muestran).
     *
     * @var array<string, string>
     */
    public array $social = [
        'instagram' => '',
        'facebook'  => '',
        'linkedin'  => '',
        'youtube'   => '',
        'x'         => '',
    ];

    /**
     * Historias por página en el feed.
     */
    public int $perPage = 8;
}
