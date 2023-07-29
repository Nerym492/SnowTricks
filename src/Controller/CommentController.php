<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Trick;
use App\Entity\User;
use App\Form\CommentFormType;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Comments management
 */
class CommentController extends AbstractController
{
    /**
     * @param EntityManagerInterface $manager
     */
    public function __construct(private EntityManagerInterface $manager)
    {
    }

    /**
     * Comment form management.
     *
     * @param Request $request
     * @param Security $security
     * @param ParameterBagInterface $parameterBag
     * @param string $trickName
     * @return Response Comment section
     *
     * @throws Exception
     */
    #[Route('/comment/submitForm/{trickName}', name: 'submit_comment_form')]
    public function processCommentForm(
        Request $request,
        Security $security,
        ParameterBagInterface $parameterBag,
        string $trickName
    ): Response {
        $commentRepository = $this->manager->getRepository(Comment::class);
        $commentForm = $this->createForm(CommentFormType::class);
        $commentForm->handleRequest($request);
        $hiddeLoadButton = false;

        if ($commentForm->isSubmitted() && $commentForm->isValid()) {
            $userMail = $security->getUser()->getUserIdentifier();
            $user = $this->manager->getRepository(User::class)->findOneBy(['mail' => $userMail]);
            $trick = $this->manager->getRepository(Trick::class)->findOneBy(['name' => $trickName]);

            $newComment = $commentForm->getData();
            $newComment->setUser($user);
            $newComment->setCreationDate(new \DateTime('now', new \DateTimeZone($parameterBag->get('timezone'))));
            $newComment->setTrick($trick);

            $this->manager->persist($newComment);
            $this->manager->flush();

            $this->addFlash('success', 'Your comment has been successfully added !');
        }

        $trick = $this->manager->getRepository(Trick::class)->findOneBy(['name' => $trickName]);
        $comments = $commentRepository->findAllByTrick($trick, ['creationDate' => 'DESC']);

        if (count($comments) <= $parameterBag->get('comments_list_limit')) {
            $hiddeLoadButton = true;
        }

        return $this->render(
            'comment/comment_section.html.twig',
            [
                'comments' => $comments,
                'commentForm' => $commentForm->createView(),
                'hiddeLoadButton' => $hiddeLoadButton,
            ]
        );
    }

    /**
     * Load more comment base on the current number of displayed comments.
     *
     * @param int $commentsLoaded Number of comments currently loaded
     * @param string $trickName
     * @return Response Comment list
     */
    #[Route('/comments/loaded/{commentsLoaded}/loadMore/{trickName}', name: 'load_more_comments')]
    public function loadMoreComments(int $commentsLoaded, string $trickName): Response
    {
        $hiddeLoadButton = false;

        $commentRepository = $this->manager->getRepository(Comment::class);
        $trick = $this->manager->getRepository(Trick::class)->findOneBy(['name' => $trickName]);
        $comments = $commentRepository->findAllByTrick(
            $trick,
            ['creationDate' => 'DESC'],
            $commentsLoaded
        );

        $nbTotalComments = $commentRepository->count(['trick' => $trick->getId()]);

        if (count($comments) === $nbTotalComments) {
            $hiddeLoadButton = true;
        }

        return $this->render(
            'comment/comments_list.html.twig',
            [
                'comments' => $comments,
                'hiddeLoadButton' => $hiddeLoadButton,
            ]
        );
    }// end loadMoreComments()
}// end class
