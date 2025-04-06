<?php

namespace App\Dto\User;

use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

final class UserPatchDTO
{
    // #[Assert\Regex(
    //     pattern: '/^[a-zA-Z0-9_]{2,20}$/',
    //     message: 'Le pseudonyme doit comporter entre 2 et 20 caractères et ne peut contenir que des lettres, des chiffres et des underscores (_).'
    // )]
    #[Groups(['patch:user'])]
    public ?string $pseudo = null;

    #[Groups(['patch:user'])]
    public ?string $currentPassword = null;

    // #[Assert\Regex(
    //     pattern: '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{16,100}$/',
    //     message: 'Le mot de passe doit comporter au moins 16 caractères et contenir au moins une minuscule, une majuscule, un chiffre et un caractère spécial.'
    // )]
    #[Groups(['patch:user'])]
    public ?string $newPassword = null;
}
