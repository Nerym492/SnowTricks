<?php

namespace App\Service;

use App\Entity\ImagesTrick;
use App\Entity\Trick;
use App\Entity\VideosTrick;
use App\Utils\PathUtils;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class MediaService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ParameterBagInterface $parameterBag,
        private RequestStack $requestStack,
    ) {
    }

    public function getAllTrickMedias(int $trickId): array
    {
        $imagesTrickRepo = $this->entityManager->getRepository(ImagesTrick::class);
        $headerImage = $imagesTrickRepo->findOneBy(['trick' => $trickId, 'isInTheHeader' => 1]);
        // All trick images except the one already in the header.
        $trickImages = $imagesTrickRepo->findBy(['trick' => $trickId, 'isInTheHeader' => 0]);

        $trickVideos = $this->entityManager->getRepository(VideosTrick::class)->findBy(['trick' => $trickId]);

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

    public function uploadTrickImage(UploadedFile $file, Trick $trick): string
    {
        $trickPath = PathUtils::buildTrickPath($this->parameterBag, $trick);
        $trickPathExists = file_exists($trickPath);
        $newFileName = uniqid().'-'.$trick->getName().'.'.$file->guessExtension();

        if (!$trickPathExists) {
            $trickPathExists = mkdir($trickPath, 0777, true);
        }

        if ($trickPathExists) {
            try {
                $file->move($trickPath, $newFileName);
            } catch (FileException) {
                // Clear last flash message
                $flashBag = $this->requestStack->getSession()->getFlashBag()->clear();
                // Add new flash message
                $flashBag->add('error', 'Unable to add file '.$file->getClientOriginalName());
                $newFileName = '';
            }
        }

        return $newFileName;
    }

    public function deleteTrickImage(Trick $trick, string $fileName): bool
    {
        $filePath = PathUtils::buildTrickPath($this->parameterBag, $trick).'/'.$fileName;

        return $this->deleteFile($filePath, $fileName);
    }

    public function deleteTrickFolder(Trick $trick): bool
    {
        $folderPath = PathUtils::buildTrickPath($this->parameterBag, $trick);

        return $this->deleteFile($folderPath);
    }

    private function deleteFile(string $filePath, string $fileName = ''): bool
    {
        $fileSystem = new Filesystem();
        if ($fileSystem->exists($filePath)) {
            try {
                $fileSystem->remove($filePath);
            } catch (IOException) {
                $flashBag = $this->requestStack->getSession()->getFlashBag()->clear();
                if ('' !== $fileName) {
                    $flashBag->add('error', 'Unable to remove file '.$fileName);
                } else {
                    $flashBag->add('error', 'Unable to remove all trick files');
                }

                return false;
            }
        }

        return true;
    }
}
