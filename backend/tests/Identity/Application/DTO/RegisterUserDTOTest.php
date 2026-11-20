<?php

declare(strict_types=1);

namespace App\Tests\Identity\Application\DTO;

use PHPUnit\Framework\TestCase;
use App\Identity\Application\DTO\RegisterUserDTO;
use Symfony\Component\Validator\Validation;
use PHPUnit\Framework\Attributes\DataProvider;

class RegisterUserDTOTest extends TestCase
{
    private $validator;

    protected function setUp(): void
    {
        $this->validator = Validation::createValidatorBuilder()
            ->enableAttributeMapping()
            ->getValidator();
    }
    public static function emptyFieldsProvider(): array
    {
        return [
            'empty email' => ['', 'Password123!', 'Ali', 'Saleh', 'Ali_123', 'email'],
            'empty password' => ['test@example.com', '', 'Ali', 'Saleh', 'Ali_123', 'password'],
            'empty first name' => ['test@example.com', 'Password123!', '', 'Saleh', 'Ali_123', 'firstName'],
            'empty last name' => ['test@example.com', 'Password123!', 'Ali', '', 'Ali_123', 'lastName'],
            'empty username' => ['test@example.com', 'Password123!', 'Ali', 'Saleh', '', 'username'],
        ];
    }
    public static function lengthFieldsProvider(): array
    {
        return [
            'password too short' => [
                'test@example.com',
                'Pas23!',
                'Ali',
                'Saleh',
                'Ali_123',
                'password'
            ],
            'username too short' => [
                'test@example.com',
                'Password123!',
                'Ali',
                'Saleh',
                'A',
                'username'
            ],
            'username too long' => [
                'test@example.com',
                'Password123!',
                'Ali',
                'Saleh',
                'A'.str_repeat('x', 150),
                'username'
            ],
            'first name too short' => [
                'test@example.com',
                'Password123!',
                'A',
                'Saleh',
                'Ali_123',
                'firstName'
            ],
            'last name too short' => [
                'test@example.com',
                'Password123!',
                'Ali',
                'S',
                'Ali_123',
                'lastName'
            ],
            'email too long' => [
                str_repeat('a', 170).'@example.com',
                'Password123!',
                'Ali',
                'Saleh',
                'Ali_123',
                'email'
            ],
        ];
    }
    public function testValidDataPassesValidation(): void
    {
        $dto = new RegisterUserDTO(
            'test@example.com',
            'Password123!',
            'Ali',
            'Saleh',
            'Ali_123'
        );
        
        $errors = $this->validator->validate($dto);
        self::assertCount(0, $errors);
    }
    public function testInvalidPasswordFailsValidation(): void
    {
        $dto = new RegisterUserDTO(
            'test@example.com',
            'password123',
            'Ali',
            'Saleh',
            'Ali_123'
        );

        $errors = $this->validator->validate($dto);
        self::assertNotEmpty($errors);
        self::assertSame('password', $errors[0]->getPropertyPath());
    }
    public function testInvalidEmailFailsValidation(): void
    {
        $dto = new RegisterUserDTO(
            'ali-not-an-email',
            'Password123!',
            'Ali',
            'Saleh',
            'Ali_123'
        );

        $errors = $this->validator->validate($dto);
        self::assertNotEmpty($errors);
        self::assertSame('email', $errors[0]->getPropertyPath());
    }
    public function testInvalidUsernameFailsValidation(): void
    {
        $dto = new RegisterUserDTO(
            'test@example.com',
            'Password123!',
            'Ali',
            'Saleh',
            'Ali@123'
        );

        $errors = $this->validator->validate($dto);
        self::assertNotEmpty($errors);
        self::assertSame('username', $errors[0]->getPropertyPath());
    }
    #[DataProvider('emptyFieldsProvider')]
    public function testEmptyFieldsFailValidation(string $email, string $password, string $firstName, string $lastName, string $username, string $expectedPropertyPath): void
    {
        $dto = new RegisterUserDTO(
            $email,
            $password,
            $firstName,
            $lastName,
            $username
        );

        $errors = $this->validator->validate($dto);
        self::assertNotEmpty($errors);
        self::assertSame($expectedPropertyPath, $errors[0]->getPropertyPath());
    }
    #[DataProvider('lengthFieldsProvider')]
    public function testLengthConstraintsFailValidation(string $email, string $password, string $firstName, string $lastName, string $username, string $expectedPropertyPath): void
    {
        $dto = new RegisterUserDTO(
            $email,
            $password,
            $firstName,
            $lastName,
            $username
        );
        $errors = $this->validator->validate($dto);
        self::assertNotEmpty($errors);
        self::assertSame($expectedPropertyPath, $errors[0]->getPropertyPath());
    }
}