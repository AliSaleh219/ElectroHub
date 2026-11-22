<?php

declare(strict_types=1);

namespace App\Identity\Domain\Repository;

use App\Identity\Domain\Entity\User;

interface UserRepositoryInterface
{
    public function existsByEmail(string $email): bool;

    public function existsByUsername(string $username): bool;

    public function save(User $user): void;
}