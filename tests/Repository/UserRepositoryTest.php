<?php

namespace App\Tests\Repository;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UserRepositoryTest extends KernelTestCase
{
    private UserRepository $userRepository;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->userRepository = static::getContainer()->get(UserRepository::class);
    }

    public function testSaveAndFindUser(): void
    {
        // Create a temporary user (use a unique email to avoid conflicts)
        $user = new User();
        $user->setEmail('test+' . uniqid() . '@example.com');
        $user->setPassword('dummy'); // hashed or plain depends on your service logic

        $this->userRepository->save($user);

        $foundUser = $this->userRepository->findOneBy(['email' => $user->getEmail()]);

        $this->assertNotNull($foundUser);
        $this->assertSame($user->getEmail(), $foundUser->getEmail());
    }

    public function testUpgradePassword(): void
    {
        $user = new User();
        $user->setEmail('upgrade+' . uniqid() . '@example.com');
        $user->setPassword('oldpassword');

        $this->userRepository->save($user);

        $this->userRepository->upgradePassword($user, 'newhashedpassword');

        $updatedUser = $this->userRepository->findOneBy(['email' => $user->getEmail()]);

        $this->assertSame('newhashedpassword', $updatedUser->getPassword());
    }
}
