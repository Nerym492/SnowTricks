<?php

namespace App\Security;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;

/**
 * Manages email dispatch and verification
 */
class EmailVerifier
{
    /**
     * @param VerifyEmailHelperInterface $verifyEmailHelper
     * @param MailerInterface $mailer
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        private VerifyEmailHelperInterface $verifyEmailHelper,
        private MailerInterface $mailer,
        private EntityManagerInterface $entityManager,
    ) {
    }

    /**
     * Generates an email, sends it to the user, and inserts the generated token in the user table
     *
     * @param string $verifyEmailRouteName
     * @param UserInterface $user
     * @param TemplatedEmail $email
     * @return void
     * @throws TransportExceptionInterface
     */
    public function sendEmailConfirmation(string $verifyEmailRouteName, UserInterface $user, TemplatedEmail $email): void
    {
        $signatureComponents = $this->verifyEmailHelper->generateSignature(
            $verifyEmailRouteName,
            $user->getId(),
            $user->getMail()
        );

        $context = $email->getContext();
        $context['signedUrl'] = $signatureComponents->getSignedUrl();
        $context['expiresAtMessageKey'] = $signatureComponents->getExpirationMessageKey();
        $context['expiresAtMessageData'] = $signatureComponents->getExpirationMessageData();

        $parsedUrl = parse_url($context['signedUrl']);

        // Store generated token in the user
        if (isset($parsedUrl['query'])) {
            parse_str($parsedUrl['query'], $urlParameters);
            if (isset($urlParameters['token'])) {
                $urlToken = $urlParameters['token'];
                $user->setMailConfirmToken($urlToken);
                $this->entityManager->persist($user);
                $this->entityManager->flush();
            }
        }

        $email->context($context);

        $this->mailer->send($email);
    }

    /**
     * @throws VerifyEmailExceptionInterface
     */
    public function handleEmailConfirmation(Request $request, UserInterface $user): void
    {
        $this->verifyEmailHelper->validateEmailConfirmation($request->getUri(), $user->getId(), $user->getMail());

        $user->setIsVerified(true);
        $user->setMailConfirmToken(null);

        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }
}
