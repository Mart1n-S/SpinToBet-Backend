<?php

namespace App\Security;

use App\Entity\User;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\LockedException;

class CustomUserChecker implements UserCheckerInterface
{
    /**
     * Vérifie l'état du compte de l'utilisateur
     */
    public function checkPreAuth(UserInterface $user): void
    {
        // Vérifier si l'utilisateur est une instance de User
        if (!$user instanceof User) {
            return;
        }

        // Vérifier si l'utilisateur est bloqué (deletedAt n'est pas null)
        if ($user->getDeletedAt() !== null) {
            // Lancer une exception si l'utilisateur est bloqué
            throw new LockedException('Votre compte a été bloqué.');
        }
    }

    public function checkPostAuth(UserInterface $user): void
    {
        // Ajouter d'autres vérifications si nécessaire
    }
}
