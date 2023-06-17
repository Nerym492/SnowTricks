<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\GroupTrick;
use App\Entity\Trick;
use App\Form\TrickFormType;
use App\Service\MediaService;
use App\Utils\PathUtils;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TrickController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $manager,
        private MediaService $mediaService,
    ) {
    }

    #[Route('/tricks/details/{trickId}', name: 'trick_details')]
    public function getTrickDetails(int $trickId): Response
    {
        $trick = $this->manager->getRepository(Trick::class)->findOneBy(['id' => $trickId]);
        $groupeTrick = $this->manager->getRepository(GroupTrick::class)->findOneBy([
            'id' => $trick->getGroupTrick()->getId(),
        ]);
        $trickMedias = $this->mediaService->getAllTrickMedias($trickId);

        $comments = $this->manager->getRepository(Comment::class)->findAllOrdered(['creation_date' => 'DESC']);

        return $this->render('partials/trick.html.twig', [
            'trick' => $trick,
            'groupTrickName' => $groupeTrick->getName(),
            'headerImageExist' => true,
            'headerImage' => $trickMedias['headerImage'],
            'trickImages' => $trickMedias['images'],
            'trickVideos' => $trickMedias['videos'],
            'comments' => $comments,
        ]);
    }

    #[Route('/trick/modify/{trickId}', name: 'trick_modification')]
    public function showTrickForm(Request $request, int $trickId): Response
    {
        $headerImageExist = false;
        $trick = $this->manager->getRepository(Trick::class)->findOneBy(['id' => $trickId]);
        $groupeTrick = $this->manager->getRepository(GroupTrick::class)->findOneBy([
            'id' => $trick->getGroupTrick()->getId(),
        ]);

        $trickMedias = $this->mediaService->getAllTrickMedias($trickId);
        // All images except the header
        $trickImages = $trickMedias['images'];

        if ('' !== $trickMedias['headerImage']) {
            $headerImageExist = true;
        }

        // Collection of ImagesTricks before form submission
        $imagesCollection = new ArrayCollection();
        if (isset($trickMedias['headerImage'])) {
            $imagesCollection->add($trickMedias['headerImage']);
        }

        foreach ($trickImages as $trickImage) {
            $imagesCollection->add($trickImage);
        }

        $form = $this->createForm(TrickFormType::class, $trick);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Collection of ImagesTricks after form submission
            $imagesData = $form->get('imagesTricks')->getData();

            $fileBag = $request->files;

            if (isset($fileBag->get('trick_form')['imagesTricks'])) {
                // UploadedFile collection
                $newImagesFiles = $fileBag->get('trick_form')['imagesTricks'];

                foreach ($newImagesFiles as $newImageFileKey => $newImageFile) {
                    $newFileName = '';
                    // New image added in the form
                    if (null !== $newImageFile['file']) {
                        $newFileName = $this->mediaService->uploadTrickImage(
                            $newImageFile['file'],
                            $trick->getName()
                        );
                    }

                    // The new image has been uploaded successfully
                    if ('' !== $newFileName) {
                        $imagesData[$newImageFileKey]->setFileName($newFileName);
                        $imagesData[$newImageFileKey]->setIsInTheHeader(false);
                    }
                }
            }
            dump($imagesCollection);
            // Deleting images files that no longer exist in the trick
            foreach ($imagesCollection as $image) {
                $imageDeleted = false;
                if (null === $image->getTrick()) {
                    $imageDeleted = $this->mediaService->deleteTrickImage($trick->getName(), $image->getFileName());
                }
                // Image file suppression failed
                if (false === $imageDeleted && null === $image->getTrick()) {
                    // Cancels deletion of image from database
                    $image->setTrick($trick);
                }
            }

            // Persist the Trick
            $this->manager->persist($form->getData());
            $this->manager->flush();

            return $this->redirectToRoute('trick_modification', ['trickId' => $trickId]);
        }

        return $this->render('partials/trick_form.html.twig', [
            'trick' => $trick,
            'groupTrickName' => $groupeTrick->getName(),
            'headerImageExist' => $headerImageExist,
            'headerImage' => $trickMedias['headerImage'],
            'trickImages' => $trickMedias['images'],
            'trickVideos' => $trickMedias['videos'],
            'trickForm' => $form->createView(),
        ]);
    }

    #[Route('/trickImage/{trickName}/{imageName}', name: 'get_trick_image')]
    public function getTrickImage(ParameterBagInterface $parameterBag, $trickName, $imageName): Response
    {
        $imagePath = PathUtils::buildTrickPath($parameterBag, $this->manager, $trickName).'/'.$imageName;

        return $this->mediaService->serveProtectedImage($imagePath);
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
