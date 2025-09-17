<?php

/**
 * This file is part of the TODO App project.
 *
 * (c) Hlib Ivanov.
 *
 * For license information, see the LICENSE file.
 */

namespace App\Tests\Controller;

use App\Entity\Comment;
use App\Entity\Photo;
use App\Entity\Gallery;
use App\Entity\User;
use App\Repository\CommentsRepository;
use App\Repository\PhotosRepository;
use App\Repository\GalleriesRepository;
use App\Repository\UserRepository;
use App\Service\CommentsService;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class CommentsControllerTest.
 *
 * Functional tests for the CommentsController.
 */
class CommentsControllerTest extends WebTestCase
{
    private KernelBrowser $httpClient;
    private User $adminUser;

    public const TEST_ROUTE = '/Comments';

    /*** Public Test Methods ***/

    /**
     * Tests the index route of CommentsController.
     *
     * @return void Returns nothing. Asserts that the index route responds successfully.
     */
    public function testIndexRoute(): void
    {
        $this->httpClient->request('GET', self::TEST_ROUTE.'/');
        $this->assertResponseIsSuccessful();
    }

    /**
     * Tests showing a comment using a mock CommentsService.
     *
     * @return void Returns nothing. Asserts that the GET request to the comment's page returns HTTP 200.
     */
    public function testShowCommentWithMock(): void
    {
        $photo = $this->createPhoto();
        $comment = new Comment();
        $comment->setEmail('test@example.com');
        $comment->setNick('Tester');
        $comment->setText('Test comment');
        $comment->setPhoto($photo);

        $mockService = $this->createMock(CommentsService::class);
        static::getContainer()->set(CommentsService::class, $mockService);

        $this->httpClient->request('GET', self::TEST_ROUTE.'/'.$comment->getId());
        $this->assertEquals(200, $this->httpClient->getResponse()->getStatusCode());
    }

    /**
     * Tests rendering the comment creation form.
     *
     * @return void Returns nothing. Asserts the creation form page renders successfully.
     */
    public function testCreateCommentForm(): void
    {
        $photo = $this->createPhoto();
        $this->httpClient->request('GET', self::TEST_ROUTE.'/create/'.$photo->getId().'/photo');
        $this->assertResponseIsSuccessful();
    }

    /**
     * Tests submitting a new comment.
     *
     * @return void Returns nothing. Asserts redirection occurs and comment text appears after redirect.
     */
    public function testCreateCommentSubmit(): void
    {
        $photo = $this->createPhoto();

        $crawler = $this->httpClient->request('GET', self::TEST_ROUTE.'/create/'.$photo->getId().'/photo');
        $this->assertResponseIsSuccessful();

        $form = $crawler->selectButton('Dodaj')->form();
        $form['comments[nick]'] = 'TestUser';
        $form['comments[email]'] = 'test@example.com';
        $form['comments[text]'] = 'This is a test comment';

        $this->httpClient->submit($form);

        $this->assertResponseRedirects(self::TEST_ROUTE.'/');
        $this->httpClient->followRedirect();

        $this->assertSelectorTextContains('body', 'This is a test comment');
    }

    /**
     * Tests deleting a comment.
     *
     * @return void Returns nothing. Asserts redirection occurs and comment is removed after deletion.
     */
    public function testDeleteComment(): void
    {
        $photo = $this->createPhoto();
        $comment = $this->createComment($photo, $this->adminUser);

        $crawler = $this->httpClient->request('GET', self::TEST_ROUTE.'/'.$comment->getId().'/delete');
        $this->assertResponseIsSuccessful();

        $form = $crawler->selectButton('action_delete')->form();
        $this->httpClient->submit($form);

        $this->assertResponseRedirects(self::TEST_ROUTE.'/');
        $this->httpClient->followRedirect();

        $this->assertSelectorTextNotContains('body', $comment->getText());
    }

    /*** Protected Methods ***/

    /**
     * Sets up the test client and logs in an admin user before each test.
     *
     * @return void Returns nothing. Initializes client and admin user.
     */
    protected function setUp(): void
    {
        $this->httpClient = static::createClient();
        $this->adminUser = $this->createUser(['ROLE_ADMIN']);
        $this->httpClient->loginUser($this->adminUser);
    }

    /*** Private Helper Methods ***/

    /**
     * Creates a gallery for testing purposes.
     *
     * @return Gallery returns the created Gallery entity
     */
    private function createGallery(): Gallery
    {
        $gallery = new Gallery();
        $gallery->setTitle('Test Gallery');

        $repo = static::getContainer()->get(GalleriesRepository::class);
        $repo->save($gallery, true);

        return $gallery;
    }

    /**
     * Creates a photo linked to a gallery.
     *
     * @return Photo returns the created Photo entity
     */
    private function createPhoto(): Photo
    {
        $gallery = $this->createGallery();

        $photo = new Photo();
        $photo->setTitle('Test Photo');
        $photo->setText('Some description');
        $photo->setFilename('test.jpg');
        $photo->setCreatedAt(new \DateTimeImmutable());
        $photo->setUpdatedAt(new \DateTimeImmutable());
        $photo->setGallery($gallery);

        $repo = static::getContainer()->get(PhotosRepository::class);
        $repo->save($photo, true);

        return $photo;
    }

    /**
     * Creates a comment linked to a photo and user.
     *
     * @param Photo $photo Photo to attach comment to
     * @param User  $user  User creating the comment
     *
     * @return Comment returns the created Comment entity
     */
    private function createComment(Photo $photo, User $user): Comment
    {
        $comment = new Comment();
        $comment->setNick('Tester');
        $comment->setEmail('test@example.com');
        $comment->setText('Comment text');
        $comment->setPhoto($photo);

        $repo = static::getContainer()->get(CommentsRepository::class);
        $repo->save($comment, true);

        return $comment;
    }

    /**
     * Creates a user with given roles.
     *
     * @param array $roles Roles for the user
     *
     * @return User returns the created User entity
     */
    private function createUser(array $roles): User
    {
        $passwordHasher = static::getContainer()->get('security.password_hasher');

        $user = new User();
        $user->setEmail('user@example.com');
        $user->setRoles($roles);
        $user->setPassword($passwordHasher->hashPassword($user, 'p@55w0rd'));

        $repo = static::getContainer()->get(UserRepository::class);
        $repo->save($user, true);

        return $user;
    }
}
