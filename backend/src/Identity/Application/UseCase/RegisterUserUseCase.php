<?php

namespace App\Identity\Application\UseCase;

use App\Identity\Domain\Entity\User;
use App\Identity\Domain\Repository\UserRepositoryInterface;
use App\Identity\Domain\Service\PasswordHasherInterface;
use App\Identity\Application\DTO\RegisterUserDTO;
use App\Identity\Application\Exception\EmailAlreadyExistsException;
use App\Identity\Application\Exception\UsernameAlreadyExistsException;
class RegisterUserUseCase
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private PasswordHasherInterface $passwordHasher
    ) {
    }

    public function execute(RegisterUserDTO $dto): void
    {
        if ($this->userRepository->existsByEmail($dto->getEmail())) {
            throw new EmailAlreadyExistsException();
        }

        if ($this->userRepository->existsByUsername($dto->getUsername())) {
            throw new UsernameAlreadyExistsException();
        }

        $hashedPassword = $this->passwordHasher->hash($dto->getPassword());
        $user = new User(
            $dto->getEmail(),
            $hashedPassword,
            $dto->getFirstName(),
            $dto->getLastName(),
            $dto->getUsername()
        );

        $this->userRepository->save($user);
    }
}