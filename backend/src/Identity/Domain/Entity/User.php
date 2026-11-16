<?php

namespace App\Identity\Domain\Entity;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;
use DateTimeImmutable;

#[ORM\Entity]
#[ORM\Table(name: 'users')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    private Uuid $id ;

    #[ORM\Column(length: 180, unique: true)]
    private string $email;

    #[ORM\Column]
    private string $password ;

    #[ORM\Column(type: 'json')]
    private array $roles = [];

    #[ORM\Column(nullable: true)]
    private ?DateTimeImmutable $emailVerifiedAt = null;

    public function __construct(string $email, string $hashedPassword)
    {
        $userEmail=trim($email);
        if($userEmail === '') {
            throw new \InvalidArgumentException('Email cannot be empty.');
        }
        if(trim($hashedPassword) === '') {
            throw new \InvalidArgumentException('Password cannot be empty.');
        }
        $this->id = Uuid::v7();
        $this->email = strtolower($userEmail);
        $this->password = $hashedPassword;
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    public function eraseCredentials(): void
    {
        // No temporary sensitive data to erase.
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $hashedPassword): self
    {
        if (trim($hashedPassword) === '') {
            throw new \InvalidArgumentException('Password cannot be empty.');
        }
        $this->password = $hashedPassword;

        return $this;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;

        $roles[] = 'ROLE_CUSTOMER';

        return array_unique($roles);
    }
    public function isVerified(): bool
    {
        return $this->emailVerifiedAt !== null;
    }
    public function verifyEmail(): void
    {
        if ($this->isVerified()) {
            return;
        }
        $this->emailVerifiedAt = new DateTimeImmutable();
    }
    public function getEmailVerifiedAt(): ?DateTimeImmutable
    {
        return $this->emailVerifiedAt;
    }
}
