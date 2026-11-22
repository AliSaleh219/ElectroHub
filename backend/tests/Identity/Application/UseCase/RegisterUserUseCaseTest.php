<?php

declare(strict_types=1);

namespace App\Tests\Identity\Application\UseCase;

use PHPUnit\Framework\TestCase;
use App\Identity\Application\UseCase\RegisterUserUseCase;
use App\Identity\Application\DTO\RegisterUserDTO;
use App\Identity\Domain\Repository\UserRepositoryInterface;
use App\Identity\Domain\Service\PasswordHasherInterface;
use App\Identity\Domain\Entity\User;
use App\Identity\Application\Exception\EmailAlreadyExistsException;
use App\Identity\Application\Exception\UsernameAlreadyExistsException;

class RegisterUserUseCaseTest extends TestCase
{
    public function testSuccessfulRegistration(): void
    {
        $userRepository = $this->createMock(UserRepositoryInterface::class);
        $passwordHasher = $this->createStub(PasswordHasherInterface::class);
        $passwordHasher
            ->method('hash')
            ->willReturn('hashed_password');
        $dto = new RegisterUserDTO(
            'ali@example.com',
            'StrongPass123!',
            'Ali',
            'Saleh',
            'ali.saleh'
        );
        $savedUser = null;
        $userRepository
            ->expects($this->once())
            ->method('save')
            ->willReturnCallback(function (User $user) use (&$savedUser): void 
            {
                $savedUser = $user;
            });
        $useCase = new RegisterUserUseCase(
            $userRepository,
            $passwordHasher
        );
        $userRepository
            ->method('existsByEmail')
            ->willReturn(false);

        $userRepository
            ->method('existsByUsername')
            ->willReturn(false);
        $useCase->execute($dto);
        $this->assertInstanceOf(User::class, $savedUser);
        $this->assertSame('ali@example.com', $savedUser->getEmail());
        $this->assertSame('Ali', $savedUser->getFirstName());
        $this->assertSame('Saleh', $savedUser->getLastName());
        $this->assertSame('ali.saleh', $savedUser->getUsername());
        $this->assertSame('hashed_password', $savedUser->getPassword());
    }
    public function testRegistrationFailsWhenEmailAlreadyExists(): void
    {
        $userRepository = $this->createMock(UserRepositoryInterface::class);
        $passwordHasher = $this->createMock(PasswordHasherInterface::class);
        $passwordHasher
            ->expects($this->never())
            ->method('hash');
        $dto = new RegisterUserDTO(
            'ali@example.com',
            'StrongPass123!',
            'Ali',
            'Saleh',
            'ali.saleh'
        );
        $userRepository
            ->method('existsByEmail')
            ->willReturn(true);

        $userRepository
            ->expects($this->never())
            ->method('save');
        $useCase = new RegisterUserUseCase(
            $userRepository,
            $passwordHasher
        );
        $this->expectException(EmailAlreadyExistsException::class);
        $useCase->execute($dto);
    }
    public function testRegistrationFailsWhenUsernameAlreadyExists(): void
    {
        $userRepository = $this->createMock(UserRepositoryInterface::class);
        $passwordHasher = $this->createMock(PasswordHasherInterface::class);
        $passwordHasher
            ->expects($this->never())
            ->method('hash');

        $dto = new RegisterUserDTO(
            'ali@example.com',
            'StrongPass123!',
            'Ali',
            'Saleh',
            'ali.saleh'
        );
        $userRepository
            ->method('existsByEmail')
            ->willReturn(false);

        $userRepository
            ->method('existsByUsername')
            ->willReturn(true);

        $userRepository
            ->expects($this->never())
            ->method('save');
        $useCase = new RegisterUserUseCase(
            $userRepository,
            $passwordHasher
        );
        $this->expectException(UsernameAlreadyExistsException::class);
        $useCase->execute($dto);
    }
}