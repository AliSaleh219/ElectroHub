<?php

declare(strict_types=1);

namespace App\Identity\Infrastructure\Persistence;

use App\Identity\Domain\Entity\User;
use App\Identity\Domain\Repository\UserRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

final class DoctrineUserRepository implements UserRepositoryInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
    }

    public function existsByEmail(string $email): bool
    {
        return $this->entityManager
            ->getRepository(User::class)
            ->count(['email' => $email]) > 0;
    }

    public function existsByUsername(string $username): bool
    {
        return $this->entityManager
            ->getRepository(User::class)
            ->count(['username' => $username]) > 0;
    }

    public function save(User $user): void
    {
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }
}