<?php

namespace App\Security;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

class ApiAuthenticator extends AbstractAuthenticator
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function supports(Request $request): ?bool
    {
        return $request->getMethod() === 'POST' && $request->getPathInfo() === '/api/login';
    }

    public function authenticate(Request $request): Passport
    {
        // Si la requête est un POST sur /api/login, on récupère les identifiants email et mot de passe.

        $data = json_decode($request->getContent(), true);

        if (!isset($data['email']) || !isset($data['password'])) {
            throw new AuthenticationException('L\'email et le mot de passe sont requis.');
        }

        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $data['email']]);

        if (!$user) {
            throw new AuthenticationException('Les identifiants sont incorrects.');
        }

        // Vérification du mot de passe
        if (!password_verify($data['password'], $user->getPassword())) {
            throw new AuthenticationException('Les identifiants sont incorrects.');
        }

        // Vérification si l'utilisateur est supprimé ou bloqué
        if ($user->getDeletedAt() !== null) {
            throw new AuthenticationException('Votre compte a été supprimé ou bloqué.');
        }

        // Vérification si l'utilisateur à vérifier son email
        if ($user->isVerified() === false) {
            throw new AuthenticationException('Votre compte n\'a pas été vérifié.');
        }

        // Retourne un Passport pour un utilisateur valide
        return new SelfValidatingPassport(
            new UserBadge($user->getEmail())
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        return new JsonResponse([
            'message' => $exception->getMessage()
        ], Response::HTTP_UNAUTHORIZED);
    }

    //    public function start(Request $request, AuthenticationException $authException = null): Response
    //    {
    //        /*
    //         * If you would like this class to control what happens when an anonymous user accesses a
    //         * protected page (e.g. redirect to /login), uncomment this method and make this class
    //         * implement Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface.
    //         *
    //         * For more details, see https://symfony.com/doc/current/security/experimental_authenticators.html#configuring-the-authentication-entry-point
    //         */
    //    }
}
