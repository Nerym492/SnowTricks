<?php

namespace App\Utils;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ImageUtils
{
    public function serveProtectedImage(string $imagePath): Response
    {
        $allowedTypes = ['jpg', 'jpeg', 'webp', 'png', 'gif'];

        $fileExtension = pathinfo($imagePath, PATHINFO_EXTENSION);

        // Vérifie si le fichier existe et si l'extension est autorisée.
        if (!in_array($fileExtension, $allowedTypes)) {
            throw new FileException('Invalid file type');
        }

        if (file_exists($imagePath)) {
            $mimeType = finfo_file(finfo_open(FILEINFO_MIME_TYPE), $imagePath);

            $response = new Response();
            $response->headers->set('Content-Type', $mimeType);
            $response->setContent(file_get_contents($imagePath));

            return $response;
        }

        throw new NotFoundHttpException('Image not found');
    }
}
