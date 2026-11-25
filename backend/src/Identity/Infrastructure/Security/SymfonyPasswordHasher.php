<?php

declare(strict_types=1);

namespace App\Identity\Infrastructure\Security;

use App\Identity\Domain\Service\PasswordHasherInterface;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;
use App\Identity\Domain\Entity\User;

final class SymfonyPasswordHasher implements PasswordHasherInterface
{
    public function __construct(
        private PasswordHasherFactoryInterface $passwordHasherFactory
    ) {
    }
    public function hash(string $plainPassword): string
    {
        $passwordHasher = $this->passwordHasherFactory->getPasswordHasher(User::class);

        return $passwordHasher->hash($plainPassword);
    }
}