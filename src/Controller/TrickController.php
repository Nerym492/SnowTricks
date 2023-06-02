<?php

namespace App\Controller;

use App\Entity\Trick;
use App\Utils\ImageUtils;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TrickController extends AbstractController
{
    private EntityManagerInterface $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    #[Route('/tricks/details/{trickId}')]
    public function getTrickDetails(string $trickId): Response
    {
        $trick = $this->manager->getRepository(Trick::class)->findOneBy(['id' => $trickId]);

        return $this->render('trick.html.twig', ['trick' => $trick]);
    }

    #[Route('/trickImage/{groupName}/{trickName}/{imageName}', name: 'get_trick_image')]
    public function getTrickImage($groupName, $trickName, $imageName): Response
    {
        $imagePath = '../assets/uploads/Trick/'.$groupName.'/'.str_replace(' ', '_', $trickName).
            '/'.$imageName;

        $imageUtils = new ImageUtils();

        return $imageUtils->serveProtectedImage($imagePath);
    }

    #[Route('/tricks/loadMore/{tricksReloaded}', name: 'load_more_tricks')]
    public function loadMoreTricks(int $tricksReloaded): Response
    {
        $trickRepository = $this->manager->getRepository(Trick::class);
        $hiddeLoadButton = false;
        $tricks = $trickRepository->findAllTricksBy(['nom' => 'ASC'], $tricksReloaded);
        $nbTricks = $trickRepository->countTricks();

        if ($nbTricks === count($tricks)) {
            $hiddeLoadButton = true;
        }

        return $this->render('partials/tricks_list.html.twig', [
            'tricks' => $tricks,
            'hiddeLoadButton' => $hiddeLoadButton,
        ]);
    }
}
