<?php

namespace App\State\User;

use App\Entity\User;
use App\Dto\User\UserReadDTO;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use ApiPlatform\Doctrine\Orm\State\ItemProvider;

final class UserReadProvider implements ProviderInterface
{
    public function __construct(
        private ItemProvider $itemProvider,
    ) {}

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): ?UserReadDTO
    {
        /** @var User|null $user */
        $user = $this->itemProvider->provide($operation, $uriVariables, $context);

        if (!$user) {
            return null;
        }

        $dto = new UserReadDTO();
        $dto->id = $user->getId();
        $dto->email = $user->getEmail();
        $dto->pseudo = $user->getPseudo();
        $dto->balance = $user->getBalance();
        $dto->createdAt = $user->getCreatedAt();
        $dto->updatedAt = $user->getUpdatedAt();
        $dto->lastLaunch = $user->getLastLaunch();

        return $dto;
    }
}
