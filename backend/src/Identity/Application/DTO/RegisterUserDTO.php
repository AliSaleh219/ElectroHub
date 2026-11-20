<?php
namespace App\Identity\Application\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class RegisterUserDTO
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Email]
        #[Assert\Length(max: 180)]
        private string $email,
        #[Assert\NotBlank]
        #[Assert\Length(min: 8, max: 4096)]
        #[Assert\Regex(
            pattern: '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^a-zA-Z\d]).+$/',
            message: 'Password must contain at least one lowercase letter, one uppercase letter, one number, and one special character.'
        )]
        private string $password,
        #[Assert\NotBlank]
        #[Assert\Length(min: 2, max: 100)]
        private string $firstName,

        #[Assert\NotBlank]
        #[Assert\Length(min: 2, max: 100)]
        private string $lastName,
        #[Assert\Length(min: 3, max: 100)]
        #[Assert\Regex(
            pattern: '/^[a-zA-Z0-9_.]+$/',
            message: 'Username can only contain letters, numbers, underscores, and dots.'
        )]
        private string $username
    ) {}

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }
    public function getFirstName(): string
    {
        return $this->firstName;
    }
    public function getLastName(): string
    {
        return $this->lastName;
    }
    public function getUsername(): string
    {
        return $this->username;
    }
}