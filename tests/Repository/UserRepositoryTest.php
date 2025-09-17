<?php

/**
 * This file is part of the TODO App project.
 *
 * (c) Hlib Ivanov
 *
 * This file contains functional tests for the UserRepository class,
 * ensuring correct persistence, retrieval, and password upgrade logic.
 */

namespace App\Tests\Repository;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Class UserRepositoryTest.
 *
 * Tests for the UserRepository service, verifying user persistence,
 * retrieval, and password upgrade functionality.
 */
class UserRepositoryTest extends KernelTestCase
{
    /**
     * The repository under test.
     */
    private UserRepository $userRepository;

    /**
     * Boots the Symfony kernel and retrieves the UserRepository instance
     * before each test.
     *
     * @return void this method does not return a value
     */
    protected function setUp(): void
    {
        self::bootKernel();
        $this->userRepository = static::getContainer()->get(UserRepository::class);
    }

    /**
     * Tests saving a user and retrieving it by email.
     *
     * Ensures that a newly persisted user can be found in the database
     * using its unique email.
     *
     * @return void this method does not return a value; it performs assertions
     */
    public function testSaveAndFindUser(): void
    {
        // Create a temporary user (use a unique email to avoid conflicts)
        $user = new User();
        $user->setEmail('test+'.uniqid().'@example.com');
        $user->setPassword('dummy'); // hashed or plain depends on your service logic

        $this->userRepository->save($user);

        $foundUser = $this->userRepository->findOneBy(['email' => $user->getEmail()]);

        $this->assertNotNull($foundUser);
        $this->assertSame($user->getEmail(), $foundUser->getEmail());
    }

    /**
     * Tests the upgradePassword functionality.
     *
     * Ensures that when a user's password is upgraded (rehashed),
     * the new hashed password is correctly persisted in the database.
     *
     * @return void this method does not return a value; it performs assertions
     */
    public function testUpgradePassword(): void
    {
        $user = new User();
        $user->setEmail('upgrade+'.uniqid().'@example.com');
        $user->setPassword('oldpassword');

        $this->userRepository->save($user);

        $this->userRepository->upgradePassword($user, 'newhashedpassword');

        $updatedUser = $this->userRepository->findOneBy(['email' => $user->getEmail()]);

        $this->assertSame('newhashedpassword', $updatedUser->getPassword());
    }
}
