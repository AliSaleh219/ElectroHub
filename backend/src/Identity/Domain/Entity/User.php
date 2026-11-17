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

    #[ORM\Column(length: 100)]
    private string $firstName;

    #[ORM\Column(length: 100)]
    private string $lastName;

    #[ORM\Column(length: 100, unique: true)]
    private string $username;
    public function __construct(string $email, string $hashedPassword, string $firstName , string $lastName , string $username )
    {
        $trimEmail=trim($email);
        $trimFirstName=trim($firstName);
        $trimLastName=trim($lastName);
        $trimUsername=trim(strtolower($username));
        if($trimEmail === '') {
            throw new \InvalidArgumentException('Email cannot be empty.');
        }
        if(trim($hashedPassword) === '') {
            throw new \InvalidArgumentException('Password cannot be empty.');
        }
        if($trimFirstName === '') {
            throw new \InvalidArgumentException('First name cannot be empty.');
        }
        if($trimLastName === '') {
            throw new \InvalidArgumentException('Last name cannot be empty.');
        }
        if($trimUsername === '') {
            throw new \InvalidArgumentException('Username cannot be empty.');
        }
        $this->id = Uuid::v7();
        $this->email = strtolower($trimEmail);
        $this->password = $hashedPassword;
        $this->firstName = $trimFirstName;
        $this->lastName = $trimLastName;
        $this->username = $trimUsername;
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

    public function changePassword(string $hashedPassword): self
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
    public function getFirstName(): string
    {
        return $this->firstName;
    }
    public function changeFirstName(string $firstName): self
    {
        $trimFirstName=trim($firstName);
        if($trimFirstName === '') {
            throw new \InvalidArgumentException('First name cannot be empty.');
        }
        $this->firstName = $trimFirstName;
        return $this;
    }
    public function getLastName(): string
    {
        return $this->lastName;
    }
    public function changeLastName(string $lastName): self
    {
        $trimLastName=trim($lastName);
        if($trimLastName === '') {
            throw new \InvalidArgumentException('Last name cannot be empty.');
        }
        $this->lastName = $trimLastName;
        return $this;
    }
    public function getUsername(): string
    {
        return $this->username;
    }
    public function changeUsername(string $username): self
    {
        $trimmedUsername = trim(strtolower($username));
        if ($trimmedUsername === '') {
            throw new \InvalidArgumentException('Username cannot be empty.');
        }
        if ($this->username === $trimmedUsername) {
            return $this;
        }
        $this->username = $trimmedUsername;
        return $this;
    }
}
