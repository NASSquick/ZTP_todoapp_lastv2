<?php

/**
 * This file is part of the TODO App project.
 *
 * (c) Hlib Ivanov
 *
 * Unit tests for the UserService class.
 * Ensures correct behavior for saving users with or without password updates.
 */

namespace App\Tests\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\UserService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Class UserServiceTest.
 *
 * Tests UserService functionality:
 * - Saving a user without changing the password
 * - Saving a user with password update and hashing
 */
class UserServiceTest extends TestCase
{
    /**
     * Test saving a user without providing a new password.
     * The existing password should remain unchanged.
     *
     * @return void this test performs assertions and does not return a value
     */
    public function testSaveWithoutPassword(): void
    {
        $user = new User();
        $user->setEmail('test@example.com');
        $user->setPassword('original_password');

        $repoMock = $this->createMock(UserRepository::class);
        $repoMock->expects($this->once())
            ->method('save')
            ->with($user);

        $hasherMock = $this->createMock(UserPasswordHasherInterface::class);
        // hashPassword should NOT be called
        $hasherMock->expects($this->never())
            ->method('hashPassword');

        $service = new UserService($repoMock, $hasherMock);
        $service->save($user, null);

        $this->assertEquals('original_password', $user->getPassword());
    }

    /**
     * Test saving a user with a new password provided.
     * The password should be hashed and updated correctly.
     *
     * @return void this test performs assertions and does not return a value
     */
    public function testSaveWithPassword(): void
    {
        $user = new User();
        $user->setEmail('test@example.com');
        $user->setPassword('original_password');

        $repoMock = $this->createMock(UserRepository::class);
        $repoMock->expects($this->once())
            ->method('save')
            ->with($user);

        $hasherMock = $this->createMock(UserPasswordHasherInterface::class);
        $hasherMock->expects($this->once())
            ->method('hashPassword')
            ->with($user, 'new_password')
            ->willReturn('hashed_password');

        $service = new UserService($repoMock, $hasherMock);
        $service->save($user, 'new_password');

        $this->assertEquals('hashed_password', $user->getPassword());
    }
}
