<?php

namespace App\Service;

use App\Entity\ImagesTrick;
use App\Entity\Trick;
use App\Entity\User;
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

/**
 * Image management
 */
class MediaService
{
    /**
     * @param EntityManagerInterface $entityManager
     * @param ParameterBagInterface $parameterBag
     * @param RequestStack $requestStack
     */
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ParameterBagInterface $parameterBag,
        private RequestStack $requestStack,
    ) {
    }

    /**
     * Retrieve all ImagesTricks and VideosTricks objects of a Trick
     *
     * @param int $trickId
     * @return array
     */
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

    /**
     * Returns an image according to a given path
     *
     * @param string $imagePath
     * @return Response
     */
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

    /**
     * Upload a new Trick image
     *
     * @param UploadedFile $file
     * @param Trick $trick
     * @return string
     */
    public function uploadTrickImage(UploadedFile $file, Trick $trick): string
    {
        $trickPath = PathUtils::buildTrickPath($this->parameterBag, $trick);
        $trickPathExists = file_exists($trickPath);
        $newFileName = uniqid().'-'.$trick->getName().'.'.$file->guessExtension();

        if (!$trickPathExists) {
            $trickPathExists = mkdir($trickPath, 0777, true);
        }

        if ($trickPathExists) {
            $newFileName = $this->createFile($file, $trickPath, $newFileName);
        }

        return $newFileName;
    }

    /**
     * Upload a user profile picture
     *
     * @param UploadedFile $file
     * @param User $user
     * @return string
     */
    public function uploadProfilePicture(UploadedFile $file, User $user): string
    {
        $userPath = $this->parameterBag->get('user_folder_path');
        $newFileName = uniqid().'-'.$user->getPseudo().'.'.$file->guessExtension();

        if (file_exists($userPath)) {
            $newFileName = $this->createFile($file, $userPath, $newFileName);
        }

        return $newFileName;
    }

    /**
     * Delete a trick image
     *
     * @param Trick $trick
     * @param string $fileName
     * @return bool
     */
    public function deleteTrickImage(Trick $trick, string $fileName): bool
    {
        $filePath = PathUtils::buildTrickPath($this->parameterBag, $trick).'/'.$fileName;

        return $this->deleteFile($filePath, $fileName);
    }

    /**
     * Delete a trick folder
     *
     * @param Trick $trick
     * @return bool
     */
    public function deleteTrickFolder(Trick $trick): bool
    {
        $folderPath = PathUtils::buildTrickPath($this->parameterBag, $trick);

        return $this->deleteFile($folderPath);
    }

    /**
     * Create an image file on the server
     *
     * @param UploadedFile $file
     * @param string $path
     * @param string $fileName
     * @return string
     */
    private function createFile(UploadedFile $file, string $path, string $fileName): string
    {
        try {
            $file->move($path, $fileName);
        } catch (FileException) {
            // Clear last flash message
            $flashBag = $this->requestStack->getSession()->getFlashBag();
            $flashBag->clear();
            // Add new flash message
            $flashBag->add('danger', 'Unable to add file '.$file->getClientOriginalName());
            $fileName = '';
        }

        return $fileName;
    }

    /**
     * Delete an image file from the server
     *
     * @param string $filePath
     * @param string $fileName
     * @return bool
     */
    private function deleteFile(string $filePath, string $fileName = ''): bool
    {
        $fileSystem = new Filesystem();
        if ($fileSystem->exists($filePath)) {
            try {
                $fileSystem->remove($filePath);
            } catch (IOException) {
                $flashBag = $this->requestStack->getSession()->getFlashBag();
                $flashBag->clear();
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
