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
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Trick routes
 */
class TrickController extends AbstractController
{
    /**
     * @param EntityManagerInterface $manager
     * @param MediaService           $mediaService
     */
    public function __construct(private EntityManagerInterface $manager, private MediaService $mediaService)
    {
    }

    /**
     * Displays the details of a trick according to the id passed in parameter.
     *
     * @param Request  $request
     * @param Security $security
     * @param int      $trickId
     *
     * @return Response Trick details page
     */
    #[Route('/tricks/details/{trickId}', name: 'trick_details')]
    public function getTrickDetails(Request $request, Security $security, int $trickId): Response
    {
        $hiddeLoadButton = false;

        $trick = $this->manager->getRepository(Trick::class)->findOneBy(['id' => $trickId]);
        $groupeTrick = $this->manager->getRepository(GroupTrick::class)->findOneBy([
            'id' => $trick->getGroupTrick()->getId(),
        ]);
        $trickMedias = $this->mediaService->getAllTrickMedias($trickId);

        $commentRepository = $this->manager->getRepository(Comment::class);
        $nbTotalComments = $commentRepository->count([]);
        $comments = $commentRepository->findAllOrdered(['creationDate' => 'DESC']);
        $connectedUser = $security->getUser();

        if (count($comments) === $nbTotalComments) {
            $hiddeLoadButton = true;
        }

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
            'hiddeLoadButton' => $hiddeLoadButton,
        ]);
    }

    /**
     * Displays the trick form.
     *
     * @param Request $request
     * @param int     $trickId
     *
     * @return Response Trick form
     */
    #[Route('/trick/modify/{trickId}', name: 'trick_modification')]
    public function showTrickForm(Request $request, int $trickId): Response
    {
        $headerImageExist = false;
        $trick = $this->manager->getRepository(Trick::class)->findOneBy(['id' => $trickId]);
        $trickName = $trick->getName();
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
                    dump($trick);
                    $imageDeleted = $this->mediaService->deleteTrickImage($trick, $image->getFileName());
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

            $this->addFlash('success', 'The trick has been successfully modified !');

            return $this->redirectToRoute('app_home', ['trickId' => $trickId, '_fragment' => 'trick-list']);
        }

        return $this->render('trick/trick_form.html.twig', [
            'trick' => $trick,
            'trickName' => $trickName,
            'groupTrickName' => $groupeTrick->getName(),
            'headerImageExist' => $headerImageExist,
            'headerImage' => $trickMedias['headerImage'],
            'trickImages' => $trickMedias['images'],
            'trickVideos' => $trickMedias['videos'],
            'trickForm' => $form->createView(),
        ]);
    }

    /**
     * Retrieves the image of a trick based on its name and the name of the image.
     *
     * @param ParameterBagInterface $parameterBag
     * @param $trickName
     * @param $imageName
     *
     * @return Response Trick image
     */
    #[Route('/trickImage/{trickName}/{imageName}', name: 'get_trick_image')]
    public function getTrickImage(ParameterBagInterface $parameterBag, $trickName, $imageName): Response
    {
        $trick = $this->manager->getRepository(Trick::class)->findOneBy(['name' => $trickName]);
        $imagePath = PathUtils::buildTrickPath($parameterBag, $trick).'/'.$imageName;

        return $this->mediaService->serveProtectedImage($imagePath);
    }

    /**
     * Load more tricks base on the current number of displayed tricks.
     *
     * @param int $tricksReloaded
     *
     * @return Response
     */
    #[Route('/tricks/loadMore/{tricksReloaded}', name: 'load_more_tricks')]
    public function loadMoreTricks(int $tricksReloaded): Response
    {
        $trickRepository = $this->manager->getRepository(Trick::class);
        $hiddeLoadButton = false;
        $tricks = $trickRepository->findAllTricksBy(['name' => 'ASC'], $tricksReloaded);
        $nbTricks = $trickRepository->countTricks();

        if (count($tricks) === $nbTricks) {
            $hiddeLoadButton = true;
        }

        return $this->render('trick/tricks_list.html.twig', [
            'tricks' => $tricks,
            'hiddeLoadButton' => $hiddeLoadButton,
        ]);
    }

    /**
     * Delete a trick using the trick name passed in parameters and reloads the list of tricks afterwards.
     *
     * @param string $trickName    Name of the trick to delete
     * @param int    $tricksLoaded Number of tricks currently loaded
     *
     * @return Response Trick list
     */
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

        if (count($tricks) === $nbTricks) {
            $hiddeLoadButton = true;
        }

        return $this->render('trick/tricks_list.html.twig', [
            'tricks' => $tricks,
            'hiddeLoadButton' => $hiddeLoadButton,
        ]);
    }

    /**
     * Displays and manages the trick creation form.
     *
     * @param Security $security
     * @param Request  $request
     *
     * @return Response Trick form
     */
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
            'trickName' => $trick->getName(),
            'headerImageExist' => false,
            'headerImage' => null,
            'trickForm' => $form->createView(),
        ]);
    }

    /**
     * Add new images if there are any.
     *
     * @param Request       $request
     * @param Trick         $trick
     * @param FormInterface $form
     *
     * @return void
     */
    private function processFormImages(Request $request, Trick $trick, FormInterface $form): void
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
