<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\GroupTrick;
use App\Entity\ImagesTrick;
use App\Entity\Trick;
use App\Entity\VideosTrick;
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
        $groupeTrick = $this->manager->getRepository(GroupTrick::class)->findOneBy([
            'id' => $trick->getGroupTrick()->getId(),
        ]);
        $imagesTrickRepo = $this->manager->getRepository(ImagesTrick::class);
        $headerImage = $imagesTrickRepo->findOneBy(['trick' => $trickId, 'isInTheHeader' => 1]);
        // All trick images except the one already in the header.
        $trickImages = $imagesTrickRepo->findBy(['trick' => $trickId, 'isInTheHeader' => 0]);

        $trickVideos = $this->manager->getRepository(VideosTrick::class)->findAll();

        $comments = $this->manager->getRepository(Comment::class)->findAllOrdered(['creation_date' => 'DESC']);

        return $this->render('partials/trick.html.twig', [
            'trick' => $trick,
            'groupTrickName' => $groupeTrick->getName(),
            'headerImageExist' => true,
            'headerImage' => $headerImage,
            'trickImages' => $trickImages,
            'trickVideos' => $trickVideos,
            'comments' => $comments,
        ]);
    }

    #[Route('/trick/modify/{groupTrickName}/{trickId}/{imageName}')]
    public function showTrickForm(int $trickId, string $groupTrickName, string $imageName): Response
    {
        $headerImageExist = false;
        $trick = $this->manager->getRepository(Trick::class)->findOneBy(['id' => $trickId]);

        if ('' !== $imageName) {
            $headerImageExist = true;
        }

        return $this->render('partials/trick_form.html.twig', [
            'trick' => $trick,
            'groupTrickName' => $groupTrickName,
            'headerImageExist' => $headerImageExist,
            'headerImage' => $imageName,
        ]);
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
        $tricks = $trickRepository->findAllTricksBy(['name' => 'ASC'], $tricksReloaded);
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
