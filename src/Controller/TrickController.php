<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\GroupTrick;
use App\Entity\Trick;
use App\Entity\User;
use App\Form\CommentFormType;
use App\Form\TrickFormType;
use App\Service\MediaService;
use App\Utils\PathUtils;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
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
    public function getTrickDetails(Request $request, Security $security, int $trickId): Response
    {
        $trick = $this->manager->getRepository(Trick::class)->findOneBy(['id' => $trickId]);
        $groupeTrick = $this->manager->getRepository(GroupTrick::class)->findOneBy([
            'id' => $trick->getGroupTrick()->getId(),
        ]);
        $trickMedias = $this->mediaService->getAllTrickMedias($trickId);
        $comments = $this->manager->getRepository(Comment::class)->findAllOrdered(['creation_date' => 'DESC']);
        $connectedUser = $security->getUser();

        if (null !== $connectedUser) {
            $user = $this->manager->getRepository(User::class)->findOneBy([
                'mail' => $connectedUser->getUserIdentifier(),
            ]);
            $newComment = new Comment();
            $newComment->setUser($user);

            $commentForm = $this->createForm(CommentFormType::class, $newComment);
            $commentForm->handleRequest($request);

            $commentForm = $commentForm->createView();
        } else {
            $commentForm = null;
        }

        return $this->render('trick/trick.html.twig', [
            'trick' => $trick,
            'groupTrickName' => $groupeTrick->getName(),
            'headerImageExist' => true,
            'headerImage' => $trickMedias['headerImage'],
            'trickImages' => $trickMedias['images'],
            'trickVideos' => $trickMedias['videos'],
            'comments' => $comments,
            'commentForm' => $commentForm,
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
            $this->processFormImages($request, $trick, $form);
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

        return $this->render('trick/trick_form.html.twig', [
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
        $trick = $this->manager->getRepository(Trick::class)->findOneBy(['name' => $trickName]);
        $imagePath = PathUtils::buildTrickPath($parameterBag, $trick).'/'.$imageName;

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

        return $this->render('trick/tricks_list.html.twig', [
            'tricks' => $tricks,
            'hiddeLoadButton' => $hiddeLoadButton,
        ]);
    }

    #[Route('/tricks/delete/{trickName}/loaded/{tricksLoaded}', name: 'delete_trick')]
    public function deleteTrick(string $trickName, int $tricksLoaded): Response
    {
        $hiddeLoadButton = false;
        $trickRepository = $this->manager->getRepository(Trick::class);
        $trickToDelete = $trickRepository->findOneBy(['name' => $trickName]);

        if ($trickToDelete) {
            $folderDeleted = $this->mediaService->deleteTrickFolder($trickToDelete);

            if ($folderDeleted) {
                $this->manager->remove($trickToDelete);
                $this->manager->flush();
                $this->addFlash('success', 'The trick has been successfully deleted !');
            }
        }

        $tricks = $trickRepository->findAllTricksBy(['name' => 'ASC'], $tricksLoaded, false);
        $nbTricks = $trickRepository->countTricks();

        if ($nbTricks === count($tricks)) {
            $hiddeLoadButton = true;
        }

        return $this->render('trick/tricks_list.html.twig', [
            'tricks' => $tricks,
            'hiddeLoadButton' => $hiddeLoadButton,
        ]);
    }

    #[Route('/tricks/create', name: 'create_trick')]
    public function createTrick(Security $security, Request $request): Response
    {
        $trick = new Trick();
        $form = $this->createForm(TrickFormType::class, $trick);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->processFormImages($request, $trick, $form);
            $trick->setCreationDate(new \DateTime());
            $trick->setUser($security->getUser());

            // Persist the Trick
            $this->manager->persist($form->getData());
            $this->manager->flush();

            $this->addFlash('success', 'The trick has been successfully created !');

            return $this->redirectToRoute('app_home', ['_fragment' => 'trick-list']);
        }

        return $this->render('trick/trick_form.html.twig', [
            'trick' => $trick,
            'headerImageExist' => false,
            'headerImage' => null,
            'trickForm' => $form->createView(),
        ]);
    }

    private function processFormImages(Request $request, Trick $trick, $form)
    {
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
                        $trick
                    );
                }

                // The new image has been uploaded successfully
                if ('' !== $newFileName) {
                    $imagesData[$newImageFileKey]->setFileName($newFileName);
                }
            }
        }
    }
}
