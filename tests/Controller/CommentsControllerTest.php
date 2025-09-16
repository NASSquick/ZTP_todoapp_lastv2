<?php

namespace App\Tests\Controller;

use App\Entity\Comment;
use App\Entity\Photo;
use App\Entity\User;
use App\Repository\CommentsRepository;
use App\Repository\PhotosRepository;
use App\Repository\UserRepository;
use App\Service\CommentsService;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CommentsControllerTest extends WebTestCase
{
    private KernelBrowser $httpClient;

    public const TEST_ROUTE = '/Comments';

    protected function setUp(): void
    {
        $this->httpClient = static::createClient();
    }

    public function testIndexRoute(): void
    {
        $this->httpClient->request('GET', self::TEST_ROUTE.'/');
        $status = $this->httpClient->getResponse()->getStatusCode();
        $this->assertEquals(200, $status); // changed from redirect check
    }

    public function testShowCommentWithMock(): void
    {
        $expectedStatus = 200;

        $photo = $this->createPhoto();
        $comment = new Comment();
        $comment->setEmail('test@example.com');
        $comment->setNick('Tester');
        $comment->setText('Test comment');
        $comment->setPhoto($photo);

        // Remove save() expectation: GET does not call save
        $commentService = $this->createMock(CommentsService::class);
        static::getContainer()->set(CommentsService::class, $commentService);

        $user = $this->createUser(['ROLE_ADMIN']);
        $this->httpClient->loginUser($user);

        $this->httpClient->request('GET', self::TEST_ROUTE.'/'.$comment->getId());
        $status = $this->httpClient->getResponse()->getStatusCode();

        $this->assertEquals($expectedStatus, $status);
    }

    public function testCreateCommentForm(): void
    {
        $user = $this->createUser(['ROLE_ADMIN']);
        $this->httpClient->loginUser($user);

        $photo = $this->createPhoto();

        $this->httpClient->request('GET', self::TEST_ROUTE.'/create/'.$photo->getId().'/photo');
        $status = $this->httpClient->getResponse()->getStatusCode();

        $this->assertEquals(200, $status);
    }

    public function testCreateCommentSubmit(): void
    {
        $user = $this->createUser(['ROLE_ADMIN']);
        $this->httpClient->loginUser($user);

        $photo = $this->createPhoto();

        $this->httpClient->request('GET', self::TEST_ROUTE.'/create/'.$photo->getId().'/photo');
        $this->httpClient->submitForm('Dodaj', [
            'comments' => [
                'nick' => 'Tester',
                'email' => 'test@example.com',
                'text' => 'Comment content',
            ]
        ]);

        $status = $this->httpClient->getResponse()->getStatusCode();
        $this->assertEquals(302, $status); // redirect after save
    }

    public function testDeleteComment(): void
    {
        $user = $this->createUser(['ROLE_ADMIN']);
        $this->httpClient->loginUser($user);

        $photo = $this->createPhoto();
        $comment = $this->createComment($photo, $user);

        $this->httpClient->request('GET', self::TEST_ROUTE.'/'.$comment->getId().'/delete');
        $this->httpClient->submitForm('action_delete');

        $status = $this->httpClient->getResponse()->getStatusCode();
        $this->assertEquals(302, $status); // redirect after delete
    }

    private function createPhoto(): Photo
    {
        $photo = new Photo();
        $photo->setTitle('Test Photo');
        $photo->setText('Some description');
        $photo->setFilename('test.jpg');
        $photo->setCreatedAt(new \DateTimeImmutable());
        $photo->setUpdatedAt(new \DateTimeImmutable());

        $repo = static::getContainer()->get(PhotosRepository::class);
        $repo->save($photo);

        return $photo;
    }

    private function createComment(Photo $photo, User $user): Comment
    {
        $comment = new Comment();
        $comment->setNick('Tester');
        $comment->setEmail('test@example.com');
        $comment->setText('Comment text');
        $comment->setPhoto($photo);

        $repo = static::getContainer()->get(CommentsRepository::class);
        $repo->save($comment);

        return $comment;
    }

    private function createUser(array $roles): User
    {
        $passwordHasher = static::getContainer()->get('security.password_hasher');

        $user = new User();
        $user->setEmail('user@example.com');
        $user->setRoles($roles);
        $user->setPassword($passwordHasher->hashPassword($user, 'p@55w0rd'));

        $repo = static::getContainer()->get(UserRepository::class);
        $repo->save($user);

        return $user;
    }
}
