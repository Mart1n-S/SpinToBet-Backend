<?php

namespace App\Dto\User;

use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;

final class UserReadDTO
{
    #[Groups(['read:user'])]
    public ?Uuid $id = null;

    #[Groups(['read:user'])]
    public ?string $email = null;

    #[Groups(['read:user'])]
    public ?string $pseudo = null;

    #[Groups(['read:user'])]
    public ?float $balance = null;

    #[Groups(['read:user'])]
    public ?\DateTimeInterface $createdAt = null;

    #[Groups(['read:user'])]
    public ?\DateTimeInterface $updatedAt = null;

    #[Groups(['read:user'])]
    public ?\DateTimeInterface $lastLaunch = null;
}
