<?php

namespace App\Security;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class LoginFormAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'app_login';

    public function __construct(
        private UrlGeneratorInterface $urlGenerator,
        private EntityManagerInterface $entityManager,
        private RequestStack $requestStack,
    ) {
    }

    public function supports(Request $request): bool
    {
        $mail = $request->request->get('mail');
        $mailVerified = false;

        if ($mail) {
            $user = $this->entityManager->getRepository(User::class)->findOneBy(['mail' => $mail]);
            $mailVerified = $user->isVerified();
        }

        if (!$mailVerified && $mail) {
            $flashBag = $this->requestStack->getSession()->getFlashBag();
            $flashBag->clear();
            $flashBag->add('warning', 'Please validate your email address before logging in.');
        }

        return parent::supports($request) && $mailVerified;
    }

    public function authenticate(Request $request): Passport
    {
        $mail = $request->request->get('mail', '');

        $request->getSession()->set(Security::LAST_USERNAME, $mail);

        return new Passport(
            new UserBadge($mail),
            new PasswordCredentials($request->request->get('password', '')),
            [
                new CsrfTokenBadge('authenticate', $request->request->get('_csrf_token')),
            ]
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
            return new RedirectResponse($targetPath);
        }

        return new RedirectResponse($this->urlGenerator->generate('app_home'));
    }

    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }
}
