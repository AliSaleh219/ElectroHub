<?php

namespace App\Tests\Identity\Domain\Entity;

use PHPUnit\Framework\TestCase;
use App\Identity\Domain\Entity\User;
class UserTest extends TestCase
{
    public function testNewUserIsNotVerified(): void
    {
        $user = new User('test@example.com', 'hashed_password', 'John', 'Doe', 'johndoe');
        self::assertFalse($user->isVerified());
    }
    public function testNewUserHasCustomerRole(): void
    {
        $user = new User('test@example.com', 'hashed_password', 'John', 'Doe', 'johndoe');
        self::assertSame(['ROLE_CUSTOMER'], $user->getRoles());
    }
    public function testCannotCreateUserWithEmptyEmail(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Email cannot be empty.');
        new User('', 'hashed_password', 'John', 'Doe', 'johndoe');
    }
    public function testCannotCreateUserWithEmptyPassword(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Password cannot be empty.');
        new User('test@example.com', '', 'John', 'Doe', 'johndoe');
    }
    public function testUserCanVerifyEmail(): void
    {
        $user = new User('test@example.com', 'hashed_password', 'John', 'Doe', 'johndoe');
        $user->verifyEmail();
        self::assertTrue($user->isVerified());
    }
    public function testVerifyingEmailTwiceDoesNotChangeVerificationDate(): void
    {
        $user = new User('test@example.com', 'hashed_password', 'John', 'Doe', 'johndoe');
        $user->verifyEmail();
        $verificationDate = $user->getEmailVerifiedAt();
        $user->verifyEmail();
        self::assertSame($verificationDate, $user->getEmailVerifiedAt());
    }
    public function testCanUpdatePassword(): void
    {
        $user = new User('test@example.com', 'hashed_password', 'John', 'Doe', 'johndoe');
        $user->changePassword('new_hashed_password');
        self::assertSame('new_hashed_password', $user->getPassword());
    }
    public function testCannotChangePasswordToEmptyValue(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Password cannot be empty.');
        $user = new User('test@example.com', 'hashed_password', 'John', 'Doe', 'johndoe');
        $user->changePassword('');
    }
    public function testMakeEmailLowerCase(): void
    {
        $user = new User('    TEST@EXAMPLE.COM   ', 'hashed_password', 'John', 'Doe', 'johndoe');
        self::assertSame('test@example.com', $user->getEmail());
    }
    public function testMakeUsernameLowerCase(): void
    {
        $user = new User('test@example.com', 'hashed_password', 'John', 'Doe', 'JOHNDOE');
        self::assertSame('johndoe', $user->getUsername());
    }
    public function testChangeUsername(): void
    {
        $user = new User('test@example.com', 'hashed_password', 'John', 'Doe', 'johndoe');
        $user->changeUsername('janedoe');
        self::assertSame('janedoe', $user->getUsername());
    }
    public function testCannotChangeUsernameToEmptyValue(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Username cannot be empty.');
        $user = new User('test@example.com', 'hashed_password', 'John', 'Doe', 'johndoe');
        $user->changeUsername('');
    }
    public function testChangeUsernameToSameUsername(): void
    {
        $user = new User('test@example.com', 'hashed_password', 'John', 'Doe', 'janedoe');
        $user->changeUsername('JANEDOE');
        self::assertSame('janedoe', $user->getUsername());
    }
    public function testCannotCreateUserWithEmptyFirstName(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('First name cannot be empty.');
        new User('test@example.com', 'hashed_password', '', 'Doe', 'johndoe');
    }
    public function testCannotCreateUserWithEmptyLastName(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Last name cannot be empty.');
        new User('test@example.com', 'hashed_password', 'John', '', 'johndoe');
    }
    public function testCannotCreateUserWithEmptyUsername(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Username cannot be empty.');
        new User('test@example.com', 'hashed_password', 'John', 'Doe', '');
    }
    public function testTrimmedValuesAreUsed(): void
    {
        $user = new User('    test@example.com    ', 'hashed_password', '    John    ', '    Doe    ', '    johndoe    ');
        self::assertSame('test@example.com', $user->getEmail());
        self::assertSame('John', $user->getFirstName());
        self::assertSame('Doe', $user->getLastName());
        self::assertSame('johndoe', $user->getUsername());
    }
    public function testChangeFirstName(): void
    {
        $user = new User('test@example.com', 'hashed_password', 'John', 'Doe', 'johndoe');
        $user->changeFirstName('Jane');
        self::assertSame('Jane', $user->getFirstName());
    }
    public function testChangeLastName(): void
    {
        $user = new User('test@example.com', 'hashed_password', 'John', 'Doe', 'johndoe');
        $user->changeLastName('Smith');
        self::assertSame('Smith', $user->getLastName());
    }
    public function testCannotChangeFirstNameToEmptyValue(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('First name cannot be empty.');
        $user = new User('test@example.com', 'hashed_password', 'John', 'Doe', 'johndoe');
        $user->changeFirstName('');
    }
    public function testCannotChangeLastNameToEmptyValue(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Last name cannot be empty.');
        $user = new User('test@example.com', 'hashed_password', 'John', 'Doe', 'johndoe');
        $user->changeLastName('');
    }
    public function testTrimmedFirstAndLastNamesAreUsed(): void
    {
        $user = new User('test@example.com', 'hashed_password', '    John    ', '    Doe    ', 'johndoe');
        self::assertSame('John', $user->getFirstName());
        self::assertSame('Doe', $user->getLastName());
    }
}