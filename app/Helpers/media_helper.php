<?php

declare(strict_types=1);

use App\Entities\Media;

if (! function_exists('render_media_picture')) {
    /**
     * Genera una etiqueta <picture> o <img> responsiva con WebP, srcset y lazy-load
     *
     * @param Media|null $media
     * @param string $preferredVariant 'thumb', 'sm', 'md', 'lg'
     * @param string $class Clases CSS adicionales
     * @param bool $eager Si es true, usa loading="eager" y fetchpriority="high" (para la primera imagen del feed)
     */
    function render_media_picture(?Media $media, string $preferredVariant = 'md', string $class = 'w-full h-auto object-cover', bool $eager = false): string
    {
        if (! $media) {
            return '';
        }

        $src = $media->getUrl($preferredVariant);
        $alt = esc($media->alt_text ?: 'Historia de Marilú Díaz');
        $loading = $eager ? 'eager' : 'lazy';
        $priority = $eager ? 'fetchpriority="high"' : 'decoding="async"';

        // Generar srcset si existen variantes
        $srcset = [];
        if (! empty($media->variants)) {
            foreach (['sm' => 480, 'md' => 960, 'lg' => 1600] as $size => $w) {
                if (isset($media->variants[$size])) {
                    $srcset[] = base_url($media->variants[$size]['path']) . " {$w}w";
                }
            }
        }
        $srcsetAttribute = ! empty($srcset) ? 'srcset="' . implode(', ', $srcset) . '" sizes="(max-width: 640px) 100vw, 640px"' : '';

        $wAttr = $media->width ? "width=\"{$media->width}\"" : '';
        $hAttr = $media->height ? "height=\"{$media->height}\"" : '';

        $bgStyle = $media->dominant_color ? "style=\"background-color: {$media->dominant_color};\"" : '';

        return "<img src=\"{$src}\" {$srcsetAttribute} alt=\"{$alt}\" class=\"{$class}\" loading=\"{$loading}\" {$priority} {$wAttr} {$hAttr} {$bgStyle} />";
    }
}

