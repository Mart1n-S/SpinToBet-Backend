<?php

namespace App\EventListener;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Gesdinet\JWTRefreshTokenBundle\Entity\RefreshToken;
use Symfony\Component\Security\Core\Event\AuthenticationEvent;

class AuthenticationSuccessListener
{
    private $entityManager;
    private $refreshTokenRepository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->refreshTokenRepository = $entityManager->getRepository(RefreshToken::class);
    }

    /**
     * Méthode appelée lorsqu'un utilisateur se connecte avec succès.
     */
    public function onAuthenticationSuccess(AuthenticationEvent $event)
    {
        // Récupérer l'utilisateur authentifié
        $user = $event->getAuthenticationToken()->getUser();

        // Vérifier si l'utilisateur existe et est valide
        if ($user instanceof User) {
            // Chercher et supprimer l'ancien refresh token de cet utilisateur
            $oldToken = $this->refreshTokenRepository->findOneBy(['username' => $user->getEmail()]);

            if ($oldToken) {
                // Supprimer l'ancien refresh token
                $this->entityManager->remove($oldToken);
                $this->entityManager->flush();
            }
        }
    }
}
