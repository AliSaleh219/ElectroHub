<?php

declare(strict_types=1);

namespace App\Tests\Identity\Infrastructure\Security;

use PHPUnit\Framework\TestCase;
use App\Identity\Domain\Entity\User;
use App\Identity\Infrastructure\Security\SymfonyPasswordHasher;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactory;

class SymfonyPasswordHasherTest extends TestCase
{
    public function testItHashesPassword(): void
    {
        $plainPassword = 'StrongPass123!';

        $factory = new PasswordHasherFactory([
            User::class => ['algorithm' => 'auto'],
        ]);
        $hasher = new SymfonyPasswordHasher($factory);
        $hashedPassword = $hasher->hash($plainPassword);
        $this->assertNotEmpty($hashedPassword);
        $this->assertNotEquals($plainPassword, $hashedPassword);
        $passwordHasher = $factory->getPasswordHasher(User::class);

        $this->assertTrue(
            $passwordHasher->verify($hashedPassword, $plainPassword)
        );
    }
}