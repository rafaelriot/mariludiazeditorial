<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\MediaModel;
use App\Services\MediaService;

class Media extends BaseController
{
    protected MediaService $mediaService;
    protected MediaModel $mediaModel;

    public function __construct()
    {
        $this->mediaService = new MediaService();
        $this->mediaModel = model(MediaModel::class);
    }

    /**
     * Subida general de imágenes para la galería de historias
     */
    public function upload()
    {
        $file = $this->request->getFile('file');
        if (! $file || ! $file->isValid()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Archivo inválido o ausente.'])->setStatusCode(400);
        }

        try {
            $altText = (string) $this->request->getPost('alt_text');
            $userId = auth()->id();

            $media = $this->mediaService->processUpload($file, $altText, $userId);

            return $this->response->setJSON([
                'success' => true,
                'media'   => [
                    'id'            => $media->id,
                    'thumb_url'     => $media->getUrl('thumb'),
                    'url'           => $media->getUrl('md'),
                    'original_name' => $media->original_name,
                    'alt_text'      => $media->alt_text,
                ],
                'csrf_hash' => csrf_hash(),
            ]);
        } catch (\Throwable $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => $e->getMessage(),
                'csrf_hash' => csrf_hash(),
            ])->setStatusCode(422);
        }
    }

    /**
     * Adaptador para CKEditor 5
     */
    public function editorUpload()
    {
        $file = $this->request->getFile('upload');
        if (! $file || ! $file->isValid()) {
            return $this->response->setJSON(['error' => ['message' => 'No se pudo subir la imagen.']])->setStatusCode(400);
        }

        try {
            $media = $this->mediaService->processUpload($file, null, auth()->id());
            return $this->response->setJSON([
                'url' => $media->getUrl('md'),
            ]);
        } catch (\Throwable $e) {
            return $this->response->setJSON([
                'error' => ['message' => $e->getMessage()],
            ])->setStatusCode(422);
        }
    }

    /**
     * Actualiza metadatos de accesibilidad (alt_text, caption)
     */
    public function update(int $id)
    {
        $media = $this->mediaModel->find($id);
        if (! $media) {
            return $this->response->setJSON(['success' => false, 'message' => 'Media no encontrado.'])->setStatusCode(404);
        }

        $this->mediaModel->update($id, [
            'alt_text' => $this->request->getPost('alt_text'),
            'caption'  => $this->request->getPost('caption'),
        ]);

        return $this->response->setJSON(['success' => true, 'csrf_hash' => csrf_hash()]);
    }

    /**
     * Elimina un archivo multimedia
     */
    public function delete(int $id)
    {
        $media = $this->mediaModel->find($id);
        if (! $media) {
            return $this->response->setJSON(['success' => false, 'message' => 'Media no encontrado.'])->setStatusCode(404);
        }

        $this->mediaModel->delete($id);
        return $this->response->setJSON(['success' => true, 'csrf_hash' => csrf_hash()]);
    }
}

