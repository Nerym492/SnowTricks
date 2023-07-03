<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\User;
use App\Form\CommentFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CommentController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $manager,
    ) {
    }

    #[Route('/comment/submitForm', name: 'submit_comment_form')]
    public function processCommentForm(Request $request, Security $security, ParameterBagInterface $parameterBag): Response
    {
        $commentRepository = $this->manager->getRepository(Comment::class);
        $commentForm = $this->createForm(CommentFormType::class);
        $commentForm->handleRequest($request);

        if ($commentForm->isSubmitted() && $commentForm->isValid()) {
            $userMail = $security->getUser()->getUserIdentifier();
            $user = $this->manager->getRepository(User::class)->findOneBy(['mail' => $userMail]);
            $newComment = $commentForm->getData();
            $newComment->setUser($user);
            $newComment->setCreationDate(new \DateTime('now', new \DateTimeZone($parameterBag->get('timezone'))));

            $this->manager->persist($newComment);
            $this->manager->flush();

            $this->addFlash('success', 'Your comment has been successfully added !');
        }

        $comments = $commentRepository->findAllOrdered(['creation_date' => 'DESC']);

        return $this->render('partials/comment_section.html.twig', [
            'comments' => $comments,
            'commentForm' => $commentForm->createView(),
            'hiddeLoadButton' => false,
        ]);
    }

    #[Route('/comments/loaded/{commentsLoaded}/loadMore/', name: 'load_more_comments')]
    public function loadMoreComments(int $commentsLoaded): Response
    {
        $hiddeLoadButton = false;

        $commentRepository = $this->manager->getRepository(Comment::class);
        $comments = $commentRepository->findAllOrdered(
            ['creation_date' => 'DESC'],
            $commentsLoaded
        );

        $nbTotalComments = $commentRepository->count([]);

        if ($nbTotalComments === count($comments)) {
            $hiddeLoadButton = true;
        }

        return $this->render('partials/comments_list.html.twig', [
            'comments' => $comments,
            'hiddeLoadButton' => $hiddeLoadButton,
        ]);
    }
}
