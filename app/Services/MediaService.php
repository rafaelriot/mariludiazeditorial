<?php

declare(strict_types=1);

namespace App\Services;

use App\Entities\Media;
use App\Models\MediaModel;
use App\Models\MediaVariantModel;
use CodeIgniter\HTTP\Files\UploadedFile;
use Config\Media as MediaConfig;
use RuntimeException;

class MediaService
{
    protected MediaConfig $config;
    protected MediaModel $mediaModel;
    protected MediaVariantModel $variantModel;

    public function __construct(?MediaConfig $config = null)
    {
        $this->config = $config ?? config(MediaConfig::class);
        $this->mediaModel = model(MediaModel::class);
        $this->variantModel = model(MediaVariantModel::class);
    }

    /**
     * Procesa una imagen subida: auto-orienta EXIF, convierte a WebP, genera miniaturas y guarda registros
     */
    public function processUpload(UploadedFile $file, ?string $altText = null, ?int $uploadedBy = null): Media
    {
        if (! $file->isValid()) {
            throw new RuntimeException($file->getErrorString());
        }

        $realMime = $file->getMimeType();
        if (! in_array($realMime, $this->config->allowedMimes, true)) {
            throw new RuntimeException("El tipo de archivo '{$realMime}' no está permitido.");
        }

        if ($file->getSizeByUnit('kb') > $this->config->maxSizeKb) {
            throw new RuntimeException("El archivo excede el tamaño máximo permitido de {$this->config->maxSizeKb} KB.");
        }

        $tempPath = $file->getTempName();

        // Cargar imagen en memoria según su tipo
        $image = match ($realMime) {
            'image/jpeg' => @imagecreatefromjpeg($tempPath),
            'image/png'  => @imagecreatefrompng($tempPath),
            'image/webp' => @imagecreatefromwebp($tempPath),
            'image/gif'  => @imagecreatefromgif($tempPath),
            default      => null,
        };

        if (! $image) {
            throw new RuntimeException('No se pudo procesar la imagen cargada.');
        }

        // Auto-orientación EXIF para fotos tomadas con móvil (cámaras iPhone/Android)
        if ($realMime === 'image/jpeg' && function_exists('exif_read_data')) {
            $exif = @exif_read_data($tempPath);
            if (! empty($exif['Orientation'])) {
                $image = match ((int) $exif['Orientation']) {
                    3 => imagerotate($image, 180, 0),
                    6 => imagerotate($image, -90, 0),
                    8 => imagerotate($image, 90, 0),
                    default => $image,
                };
            }
        }

        $origW = imagesx($image);
        $origH = imagesy($image);

        // Color dominante promedio
        $dominantColor = $this->calculateDominantColor($image, $origW, $origH);

        // Directorio de almacenamiento por Año y Mes: public/uploads/YYYY/MM/
        $yearMonth = date('Y/m');
        $uploadDir = FCPATH . $this->config->publicDir . '/' . $yearMonth;
        if (! is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $baseName = bin2hex(random_bytes(10));
        $originalName = $file->getClientName();

        // Crear registro en tabla media
        $mediaData = [
            'original_name'  => $originalName,
            'path'           => "{$this->config->publicDir}/{$yearMonth}/{$baseName}-lg.webp",
            'mime'           => 'image/webp',
            'width'          => $origW,
            'height'         => $origH,
            'bytes'          => $file->getSize(),
            'alt_text'       => $altText ?: pathinfo($originalName, PATHINFO_FILENAME),
            'dominant_color' => $dominantColor,
            'uploaded_by'    => $uploadedBy,
        ];
        $mediaId = $this->mediaModel->insert($mediaData);

        // Generar variantes WebP
        $variants = [];
        foreach ($this->config->variants as $sizeName => $specs) {
            $variantPath = "{$this->config->publicDir}/{$yearMonth}/{$baseName}-{$sizeName}.webp";
            $destFile = FCPATH . $variantPath;

            $varInfo = $this->generateVariant($image, $origW, $origH, $specs, $destFile);
            $variants[$sizeName] = [
                'media_id' => $mediaId,
                'size'     => $sizeName,
                'path'     => $variantPath,
                'width'    => $varInfo['width'],
                'height'   => $varInfo['height'],
                'bytes'    => $varInfo['bytes'],
            ];
            $this->variantModel->insert($variants[$sizeName]);
        }

        imagedestroy($image);

        /** @var Media $media */
        $media = $this->mediaModel->find($mediaId);
        $media->variants = $variants;
        return $media;
    }

    /**
     * Genera una variante redimensionada y la guarda como WebP
     */
    protected function generateVariant(\GdImage $srcImg, int $srcW, int $srcH, array $specs, string $destPath): array
    {
        $targetW = $specs['width'];
        $isCrop  = $specs['crop'] ?? false;

        if ($isCrop && isset($specs['height'])) {
            // Recorte cuadrado centrado
            $targetH = $specs['height'];
            $srcAspect = $srcW / $srcH;
            $targetAspect = $targetW / $targetH;

            if ($srcAspect > $targetAspect) {
                $cropH = $srcH;
                $cropW = (int) round($srcH * $targetAspect);
                $cropX = (int) round(($srcW - $cropW) / 2);
                $cropY = 0;
            } else {
                $cropW = $srcW;
                $cropH = (int) round($srcW / $targetAspect);
                $cropX = 0;
                $cropY = (int) round(($srcH - $cropH) / 2);
            }

            $dstImg = imagecreatetruecolor($targetW, $targetH);
            imagecopyresampled($dstImg, $srcImg, 0, 0, $cropX, $cropY, $targetW, $targetH, $cropW, $cropH);
        } else {
            // Escalado proporcional sin recorte
            if ($srcW > $targetW) {
                $targetH = (int) round(($srcH * $targetW) / $srcW);
            } else {
                $targetW = $srcW;
                $targetH = $srcH;
            }

            $dstImg = imagecreatetruecolor($targetW, $targetH);
            imagecopyresampled($dstImg, $srcImg, 0, 0, 0, 0, $targetW, $targetH, $srcW, $srcH);
        }

        // Guardar como WebP optimizado
        imagewebp($dstImg, $destPath, $this->config->quality);
        $fileSize = filesize($destPath);
        imagedestroy($dstImg);

        return [
            'width'  => $targetW,
            'height' => $targetH,
            'bytes'  => $fileSize ?: 0,
        ];
    }

    /**
     * Calcula el color promedio de la imagen
     */
    protected function calculateDominantColor(\GdImage $img, int $w, int $h): string
    {
        $tiny = imagecreatetruecolor(1, 1);
        imagecopyresampled($tiny, $img, 0, 0, 0, 0, 1, 1, $w, $h);
        $rgb = imagecolorat($tiny, 0, 0);
        imagedestroy($tiny);

        $r = ($rgb >> 16) & 0xFF;
        $g = ($rgb >> 8) & 0xFF;
        $b = $rgb & 0xFF;

        return sprintf('#%02x%02x%02x', $r, $g, $b);
    }
}

