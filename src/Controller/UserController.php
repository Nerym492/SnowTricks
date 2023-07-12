<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\MediaService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
    }

    #[Route('/profilePicture/{userPseudo}', name: 'get_profile_picture', defaults: ['userPseudo' => ''])]
    public function getProfilePicture(
        Security $security,
        ParameterBagInterface $parameterBag,
        MediaService $mediaService,
        string $userPseudo,
    ): Response {
        // The default is to retrieve the logged-in user
        if ($security->getUser()) {
            $userMail = $security->getUser()->getUserIdentifier();
            $userCriteria = ['mail' => $userMail];
        }

        if ('' !== $userPseudo) {
            $userCriteria = ['pseudo' => $userPseudo];
        }

        // User connected or userPseudo defined in the route parameters
        if (isset($userCriteria)) {
            $user = $this->entityManager->getRepository(User::class)->findOneBy($userCriteria);
        }

        // User defined and profile photo not empty
        if (isset($user) && $user->getProfilePhoto()) {
            $profilePicturePath = $parameterBag->get('user_folder_path').'/'.$user->getProfilePhoto();

            return $mediaService->serveProtectedImage($profilePicturePath);
        }

        // Default profile photo
        $defaultImagePath = $this->getParameter('kernel.project_dir').
            '/public/build/images/default-user-avatar.png';
        $defaultImageFile = new File($defaultImagePath);

        return new Response(
            file_get_contents($defaultImageFile),
            Response::HTTP_OK,
            ['Content-Type' => 'image/jpeg']
        );
    }
}
