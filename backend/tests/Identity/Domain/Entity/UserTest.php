<?php

namespace App\Tests\Identity\Domain\Entity;

use PHPUnit\Framework\TestCase;
use App\Identity\Domain\Entity\User;
class UserTest extends TestCase
{
    public function testNewUserIsNotVerified(): void
    {
        $user = new User('test@example.com', 'hashed_password');
        self::assertFalse($user->isVerified());
    }
    public function testNewUserHasCustomerRole(): void
    {
        $user = new User('test@example.com', 'hashed_password');
        self::assertSame(['ROLE_CUSTOMER'], $user->getRoles());
    }
    public function testCannotCreateUserWithEmptyEmail(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Email cannot be empty.');
        new User('', 'hashed_password');
    }
    public function testCannotCreateUserWithEmptyPassword(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Password cannot be empty.');
        new User('test@example.com', '');
    }
    public function testUserCanVerifyEmail(): void
    {
        $user = new User('test@example.com', 'hashed_password');
        $user->verifyEmail();
        self::assertTrue($user->isVerified());
    }
    public function testVerifyingEmailTwiceDoesNotChangeVerificationDate(): void
    {
        $user = new User('test@example.com', 'hashed_password');
        $user->verifyEmail();
        $verificationDate = $user->getEmailVerifiedAt();
        $user->verifyEmail();
        self::assertSame($verificationDate, $user->getEmailVerifiedAt());
    }
    public function testCanUpdatePassword(): void
    {
        $user = new User('test@example.com', 'hashed_password');
        $user->setPassword('new_hashed_password');
        self::assertSame('new_hashed_password', $user->getPassword());
    }
    public function testCannotSetEmptyPassword(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Password cannot be empty.');
        $user = new User('test@example.com', 'hashed_password');
        $user->setPassword('');
    }
    public function testMakeEmailLowerCase(): void
    {
        $user = new User('    TEST@EXAMPLE.COM   ', 'hashed_password');
        self::assertSame('test@example.com', $user->getEmail());
    }
}