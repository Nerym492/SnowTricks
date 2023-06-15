<?php

namespace App\Service;

use App\Entity\ImagesTrick;
use App\Entity\VideosTrick;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class MediaService
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getAllTrickMedias(int $trickId): array
    {
        $imagesTrickRepo = $this->entityManager->getRepository(ImagesTrick::class);
        $headerImage = $imagesTrickRepo->findOneBy(['trick' => $trickId, 'isInTheHeader' => 1]);
        // All trick images except the one already in the header.
        $trickImages = $imagesTrickRepo->findBy(['trick' => $trickId, 'isInTheHeader' => 0]);

        $trickVideos = $this->entityManager->getRepository(VideosTrick::class)->findAll();

        return [
            'headerImage' => $headerImage,
            'images' => $trickImages,
            'videos' => $trickVideos,
        ];
    }

    public function serveProtectedImage(string $imagePath): Response
    {
        $allowedTypes = ['jpg', 'jpeg', 'webp', 'png'];

        $fileExtension = pathinfo($imagePath, PATHINFO_EXTENSION);

        // Checks if the file exists and if the extension is authorized.
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
