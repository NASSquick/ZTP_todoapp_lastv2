<?php

namespace App\Tests\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\UserService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserServiceTest extends TestCase
{
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
