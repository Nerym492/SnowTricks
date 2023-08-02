<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\GroupTrick;
use App\Entity\Trick;
use App\Entity\User;
use App\Form\CommentFormType;
use App\Form\TrickFormType;
use App\Repository\CommentRepository;
use App\Repository\TrickRepository;
use App\Service\MediaService;
use App\Utils\PathUtils;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Trick routes
 */
class TrickController extends AbstractController
{
    /**
     * @param EntityManagerInterface $manager
     * @param MediaService           $mediaService
     */
    public function __construct(
        private EntityManagerInterface $manager,
        private TrickRepository $trickRepository,
        private CommentRepository $commentRepository,
        private MediaService $mediaService,
        private SluggerInterface $slugger,
        private ValidatorInterface $validator,
    ) {
    }

    /**
     * Displays the details of a trick
     *
     * @param Trick $trick
     * @param Request $request
     * @param Security $security
     * @return Response Trick details page
     */
    #[Route('/tricks/details/{slug}', name: 'trick_details')]
    public function getTrickDetails(Trick $trick, Request $request, Security $security): Response
    {
        $hiddeLoadButton = false;
        $trickMedias = $this->mediaService->getAllTrickMedias($trick->getId());

        $nbTotalComments = $this->commentRepository->count(['trick' => $trick->getId()]);
        $comments = $this->commentRepository->findAllByTrick($trick, ['creationDate' => 'DESC']);
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
            'groupTrickName' => $trick->getGroupTrick()->getName(),
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
     * @param Trick $trick
     *
     * @return Response Trick form
     */
    #[Route('/trick/modify/{slug}', name: 'trick_modification')]
    public function showTrickForm(Request $request, Trick $trick): Response
    {
        $headerImageExist = false;

        $trickMedias = $this->mediaService->getAllTrickMedias($trick->getId());
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
            if (!$this->slugIsValid($trick, $form)) {
                return $this->renderTrickForm($trick, $form, $trickMedias, $headerImageExist);
            }

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

            return $this->redirectToRoute('app_home', ['_fragment' => 'trick-list']);
        }

        return $this->renderTrickForm($trick, $form, $trickMedias, $headerImageExist);
    }

    /**
     * Retrieves the image of a trick based on its name and the name of the image.
     *
     * @param ParameterBagInterface $parameterBag
     * @param Trick $trick
     * @param $imageName
     *
     * @return Response Trick image
     */
    #[Route('/trickImage/{slug}/{imageName}', name: 'get_trick_image')]
    public function getTrickImage(ParameterBagInterface $parameterBag, Trick $trick, $imageName): Response
    {
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
        $hiddeLoadButton = false;
        $tricks = $this->trickRepository->findAllTricksBy(['name' => 'ASC'], $tricksReloaded);
        $nbTricks = $this->trickRepository->countTricks();

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
     * @param Trick $trickToDelete
     * @param int $tricksLoaded Number of tricks currently loaded
     *
     * @return Response Trick list
     */
    #[Route('/tricks/delete/{slug}/loaded/{tricksLoaded}', name: 'delete_trick')]
    public function deleteTrick(Trick $trickToDelete, int $tricksLoaded): Response
    {
        $hiddeLoadButton = false;
        $folderDeleted = $this->mediaService->deleteTrickFolder($trickToDelete);

        if ($folderDeleted) {
            $this->manager->remove($trickToDelete);
            $this->manager->flush();
            $this->addFlash('success', 'The trick has been successfully deleted !');
        }

        $tricks = $this->trickRepository->findAllTricksBy(['name' => 'ASC'], $tricksLoaded, false);
        $nbTricks = $this->trickRepository->countTricks();

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
            if (!$this->slugIsValid($trick, $form)) {
                return $this->renderTrickForm($trick, $form);
            }

            $this->processFormImages($request, $trick, $form);
            $trick->setCreationDate(new \DateTime());
            $trick->setUser($security->getUser());

            // Persist the Trick
            $this->manager->persist($form->getData());
            $this->manager->flush();

            $this->addFlash('success', 'The trick has been successfully created !');

            return $this->redirectToRoute('app_home', ['_fragment' => 'trick-list']);
        }

        return $this->renderTrickForm($trick, $form);
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

    /**
     * Displays the trick form page
     *
     * @param Trick $trick
     * @param FormInterface $form
     * @param array|null $trickMedias
     * @param bool $headerImageExist
     * @return Response
     */
    private function renderTrickForm(
        Trick $trick,
        FormInterface $form,
        array $trickMedias = null,
        bool $headerImageExist = false,
    ): Response {
        if (!$trickMedias) {
            $trickMedias['headerImage'] = null;
            $trickMedias['images'] = null;
            $trickMedias['videos'] = null;
        }

        $groupTrickName = '';
        if ($trick->getGroupTrick()) {
            $groupTrickName = $trick->getGroupTrick()->getName();
        }

        return $this->render('trick/trick_form.html.twig', [
            'trick' => $trick,
            'trickName' => $trick->getName(),
            'groupTrickName' => $groupTrickName,
            'headerImageExist' => $headerImageExist,
            'headerImage' => $trickMedias['headerImage'],
            'trickImages' => $trickMedias['images'],
            'trickVideos' => $trickMedias['videos'],
            'trickForm' => $form->createView(),
        ]);
    }

    /**
     * Verify if the slug already exists
     *
     * @param Trick $trick
     * @param FormInterface $form
     * @return bool
     */
    private function slugIsValid(Trick $trick, FormInterface $form): bool
    {
        $oldSlug = $trick->getSlug();
        $trick->setSlug($this->slugger->slug($trick->getName(), '_'));
        $violations = $this->validator->validate($trick);

        if (count($violations) > 0) {
            foreach ($violations as $violation) {
                $formError = new FormError($violation->getMessage());
                $form->get('name')->addError($formError);
            }
            // No existing slug when creating a trick
            if ($oldSlug) {
                $trick->setSlug($oldSlug);
            }

            return false;
        }

        return true;
    }
}
