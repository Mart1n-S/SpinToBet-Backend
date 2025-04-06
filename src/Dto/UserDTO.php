<?php

namespace App\Dto;

use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Uid\Uuid;
use App\Validator\UniqueField;
use App\Entity\User;

final class UserDTO
{

    #[Groups(['read:user'])]
    public ?Uuid $id = null;

    #[Groups(['read:user'])]
    #[Assert\NotBlank(message: 'L\'adresse email est obligatoire.')]
    #[Assert\Email(message: 'L\'adresse email n\'est pas valide.')]
    #[Assert\Length(
        max: 180,
        maxMessage: 'L\'adresse email ne peut pas dépasser {{ limit }} caractères.'
    )]
    #[UniqueField(
        entityClass: User::class,
        field: 'email',
        message: 'Il existe déjà un compte avec cet email.'
    )]
    public ?string $email = null;

    public bool $isVerified = false;

    #[Groups(['read:user'])]
    public array $roles = [];

    #[Groups(['patch:user'])]
    #[Assert\Regex(
        pattern: '/^[^<>]*$/',
        message: 'Le format est incorrect.'
    )]
    public ?string $currentPassword = null;

    #[Groups(['patch:user'])]
    #[Assert\NotBlank(message: 'Le mot de passe est obligatoire.')]
    #[Assert\Regex(
        pattern: '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{16,100}$/',
        message: 'Le mot de passe doit comporter au moins 16 caractères et contenir au moins une minuscule, une majuscule, un chiffre et un caractère spécial.'
    )]
    #[Assert\NotCompromisedPassword(message: 'Ce mot de passe a été compromis dans une violation de données. Veuillez en choisir un autre.')]
    public ?string $password = null;

    #[Groups(['read:user', 'patch:user'])]
    #[Assert\NotBlank(message: 'Le pseudo est obligatoire.')]
    #[Assert\Regex(
        pattern: '/^[a-zA-Z0-9_]{2,20}$/',
        message: 'Le pseudonyme doit comporter entre 2 et 20 caractères et ne peut contenir que des lettres, des chiffres et des underscores (_).'
    )]
    #[UniqueField(
        entityClass: User::class,
        field: 'pseudo',
        message: 'Ce pseudo est déjà utilisé.'
    )]
    public ?string $pseudo = null;

    #[Groups(['read:user'])]
    #[Assert\Range(
        min: 0,
        max: 1000000000,
        notInRangeMessage: 'Le solde doit être compris entre {{ min }} et {{ max }}.'
    )]
    public ?float $balance = null;

    #[Groups(['read:user'])]
    #[Assert\Length(
        max: 32,
        maxMessage: 'Le code parrain ne peut pas dépasser {{ limit }} caractères.'
    )]
    public ?string $referralCode = null;

    #[Groups(['read:user'])]
    public ?\DateTimeInterface $createdAt = null;

    #[Groups(['read:user'])]
    public ?\DateTimeInterface $updatedAt = null;

    #[Groups(['read:user'])]
    public ?\DateTimeInterface $lastLaunch = null;

    #[Groups(['read:user'])]
    public ?\DateTimeInterface $deletedAt = null;
}
