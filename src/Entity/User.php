<?php

namespace App\Entity;

use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use Doctrine\DBAL\Types\Types;
use ApiPlatform\Metadata\Patch;
use Symfony\Component\Uid\Uuid;
use ApiPlatform\Metadata\Delete;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use ApiPlatform\Metadata\ApiResource;
use App\State\SoftDeleteUserProcessor;
use Gedmo\Mapping\Annotation as Gedmo;
use ApiPlatform\Metadata\GetCollection;
use App\State\UserPasswordHasherPocessor;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[UniqueEntity(fields: ['email'], message: 'Il existe déjà un compte avec cet email.')]
#[ApiResource(
    normalizationContext: ['groups' => ['read:user']],
    denormalizationContext: ['groups' => ['patch:user']],
    paginationEnabled: false,
    operations: [
        new Post(
            uriTemplate: '/user',
            name: 'userPost',
            security: "is_granted('PUBLIC_ACCESS')",
            processor: UserPasswordHasherPocessor::class
        ),
        new Get(
            uriTemplate: '/user/{id}',
            name: 'userGet',
            security: "is_granted('IS_AUTHENTICATED_FULLY') and object == user",  // L'utilisateur ne peut accéder qu'à ses propres données
        ),
        new Patch(
            uriTemplate: '/user/{id}',
            name: 'userPatch',
            security: "is_granted('IS_AUTHENTICATED_FULLY') and object == user",
            processor: UserPasswordHasherPocessor::class
        ),
        // <----- Partie Admin ----->
        new GetCollection(
            uriTemplate: '/admin/users',
            name: 'adminUsersGetCollection',
            security: "is_granted('ROLE_ADMIN')",
            paginationEnabled: true
        ),
        new Post(
            uriTemplate: '/admin/user',
            name: 'adminUserPost',
            security: "is_granted('ROLE_ADMIN')",
            processor: UserPasswordHasherPocessor::class

        ),
        new Patch(
            uriTemplate: '/admin/user/{id}',
            name: 'adminUserPatch',
            security: "is_granted('ROLE_ADMIN')",
            processor: UserPasswordHasherPocessor::class
        ),
        new Delete(
            uriTemplate: '/admin/user/{id}',
            name: 'adminUserDelete',
            security: "is_granted('ROLE_ADMIN')",
            processor: SoftDeleteUserProcessor::class
        ),
    ],
)]
#[Gedmo\SoftDeleteable(fieldName: "deletedAt", timeAware: false)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    use TimestampableEntity;

    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    #[Groups(['read:user'])]
    private ?Uuid $id = null;

    #[ORM\Column(length: 180)]
    #[
        Groups(['read:user', 'patch:user']),
    ]
    #[Assert\NotBlank(message: 'L\'adresse email est obligatoire.')]
    #[Assert\Email(message: 'L\'adresse email n\'est pas valide.')]
    #[Assert\Length(
        max: 180,
        maxMessage: 'L\'adresse email ne peut pas dépasser {{ limit }} caractères.'
    )]
    private ?string $email = null;

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $isVerified = false;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    #[Groups(['read:user'])]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    #[Groups(['patch:user'])]
    #[Assert\NotBlank(message: 'Le mot de passe est obligatoire.')]
    #[Assert\Regex(
        pattern: '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{16,100}$/',
        message: 'Le mot de passe doit comporter au moins 16 caractères et contenir au moins une minuscule, une majuscule, un chiffre et un caractère spécial.'
    )]
    #[Assert\NotCompromisedPassword(message: 'Ce mot de passe a été compromis dans une violation de données. Veuillez en choisir un autre.')]
    private ?string $password = null;

    #[ORM\Column(length: 20)]
    #[Groups(['read:user', 'patch:user'])]
    #[Assert\NotBlank(message: 'Le pseudo est obligatoire.')]
    #[Assert\Regex(
        pattern: '/^[a-zA-Z0-9_]{2,20}$/',
        message: 'Le pseudonyme doit comporter entre 2 et 20 caractères et ne peut contenir que des lettres, des chiffres et des underscores (_).'
    )]
    private ?string $pseudo = null;

    #[ORM\Column(type: Types::FLOAT, nullable: true)]
    #[Groups(['read:user'])]
    #[Assert\Range(
        min: 0,
        max: 1000000000,
        notInRangeMessage: 'Le solde doit être compris entre {{ min }} et {{ max }}.'
    )]
    private ?float $balance = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Groups(['read:user'])]
    private ?\DateTimeInterface $last_launch = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Groups(['read:user'])]
    private ?\DateTimeInterface $deletedAt = null;

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): static
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     *
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getPseudo(): ?string
    {
        return $this->pseudo;
    }

    public function setPseudo(string $pseudo): static
    {
        $this->pseudo = $pseudo;

        return $this;
    }

    public function getBalance(): ?float
    {
        return $this->balance;
    }

    public function setBalance(?float $balance): static
    {
        $this->balance = $balance;

        return $this;
    }

    public function getLastLaunch(): ?\DateTimeInterface
    {
        return $this->last_launch;
    }

    public function setLastLaunch(?\DateTimeInterface $last_launch): static
    {
        $this->last_launch = $last_launch;

        return $this;
    }

    #[Groups(['read:user'])]
    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }

    #[Groups(['read:user'])]
    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updatedAt;
    }

    public function getDeletedAt(): ?\DateTimeInterface
    {
        return $this->deletedAt;
    }

    public function setDeletedAt(?\DateTimeInterface $deletedAt): static
    {
        $this->deletedAt = $deletedAt;
        return $this;
    }

    public function isDeleted(): bool
    {
        return $this->deletedAt !== null;
    }
}
