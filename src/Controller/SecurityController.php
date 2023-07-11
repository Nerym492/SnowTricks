<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ResetPasswordFormType;
use App\Form\ResetPasswordRequestFormType;
use App\Service\MediaService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private TokenGeneratorInterface $tokenGenerator
    ) {
    }

    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        if (isset($error)) {
            $this->addFlash('danger', $error->getMessageKey());
        }

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route(path: '/forgottenPassword', name: 'forgotten_password')]
    public function forgottenPassword(Request $request, MailerInterface $mailer): Response
    {
        $resetPasswordForm = $this->createForm(ResetPasswordRequestFormType::class);

        $resetPasswordForm->handleRequest($request);

        if ($resetPasswordForm->isSubmitted() && $resetPasswordForm->isSubmitted()) {
            $mailFormData = $resetPasswordForm->get('mail')->getData();
            $user = $this->entityManager->getRepository(User::class)->findOneBy(['mail' => $mailFormData]);
            if ($user) {
                // Generate a token
                $token = $this->tokenGenerator->generateToken();
                $user->setResetToken($token);
                $this->entityManager->persist($user);
                $this->entityManager->flush();

                // Generate a password reset link
                $url = $this->generateUrl('reset_password', ['token' => $token],
                    UrlGeneratorInterface::ABSOLUTE_URL);

                // Send the email with the reset link
                $email = (new TemplatedEmail())
                    ->from('florianpohu49@gmail.com')
                    ->to($user->getMail())
                    ->subject('Password reset')
                    ->htmlTemplate('emails/reset_password.html.twig')
                    ->context([
                        'user' => $user,
                        'url' => $url,
                    ]);

                $mailer->send($email);

                $this->addFlash('success', 'A password reset email has been sent to you.');

                return $this->redirectToRoute('app_login');
            }

            $this->addFlash('danger', 'A problem occurred.');

            return $this->redirectToRoute('app_login');
        }

        return $this->render('security/reset_password.html.twig', [
            'resetPasswordForm' => $resetPasswordForm->createView(),
        ]);
    }

    #[Route('resetPassword/{token}', name: 'reset_password')]
    public function resetPassword(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        string $token
    ): Response {
        // Check if the token exists in the database
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['resetToken' => $token]);

        if ($user) {
            $form = $this->createForm(ResetPasswordFormType::class);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $user->setResetToken('');
                $user->setPassword(
                    $passwordHasher->hashPassword(
                        $user,
                        $form->get('password')->getData()
                    )
                );

                $this->entityManager->persist($user);
                $this->entityManager->flush();

                $this->addFlash('success', 'Password successfully changed !');

                return $this->redirectToRoute('app_login');
            }

            return $this->render('security/reset_password.html.twig', [
                'resetPasswordForm' => $form->createView(),
            ]);
        }

        $this->addFlash('danger', 'Invalid token');

        return $this->redirectToRoute('app_login');
    }

    #[Route('/profilePicture', name: 'get_profile_picture')]
    public function getProfilePicture(
        Security $security,
        ParameterBagInterface $parameterBag,
        MediaService $mediaService
    ): Response {
        $userMail = $security->getUser()->getUserIdentifier();
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['mail' => $userMail]);

        if ($user->getProfilePhoto()) {
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
