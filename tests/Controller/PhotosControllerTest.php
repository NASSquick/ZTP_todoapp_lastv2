<?php

/**
 * This file is part of the MyProject package.
 *
 * (c) Your Name <your.email@example.com>
 *
 * Functional tests for the PhotosController.
 * These tests verify CRUD operations for Photo entities,
 * including access control, form submissions, and redirects.
 */

namespace App\Tests\Controller;

use App\Entity\Photo;
use App\Entity\Gallery;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\PhotosService;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class PhotosControllerTest.
 *
 * Provides functional tests for the PhotosController routes and forms.
 */
class PhotosControllerTest extends WebTestCase
{
    private KernelBrowser $httpClient;

    public const TEST_ROUTE = '/Photos';

    /**
     * Sets up the test client before each test.
     *
     * @return void No value is returned
     */
    protected function setUp(): void
    {
        $this->httpClient = static::createClient();
    }

    /**
     * Test the index route of PhotosController.
     *
     * @return void No value is returned, assertions validate response status
     */
    public function testIndexRoute(): void
    {
        $this->httpClient->request('GET', self::TEST_ROUTE.'/');
        $status = $this->httpClient->getResponse()->getStatusCode();

        $this->assertTrue(in_array($status, [200, 301, 302]));
    }

    /**
     * Test showing a single photo page.
     *
     * @return void No value is returned, assertions validate response status
     */
    public function testShowPhoto(): void
    {
        $expectedStatus = 200;

        $photo = $this->createPhoto();

        $user = $this->createUser(['ROLE_ADMIN']);
        $this->httpClient->loginUser($user);

        $this->httpClient->request('GET', self::TEST_ROUTE.'/'.$photo->getId());
        $status = $this->httpClient->getResponse()->getStatusCode();

        $this->assertEquals($expectedStatus, $status);
    }

    /**
     * Test that the photo creation form loads correctly.
     *
     * @return void No value is returned, assertions validate response status
     */
    public function testCreatePhotoForm(): void
    {
        $user = $this->createUser(['ROLE_ADMIN']);
        $this->httpClient->loginUser($user);

        $this->httpClient->request('GET', self::TEST_ROUTE.'/create');
        $status = $this->httpClient->getResponse()->getStatusCode();

        $this->assertEquals(200, $status);
    }

    /**
     * Test submitting the create photo form with valid data.
     *
     * @return void No value is returned, assertions validate redirect status
     */
    public function testCreatePhotoSubmit(): void
    {
        $user = $this->createUser(['ROLE_ADMIN']);
        $this->httpClient->loginUser($user);

        $gallery = $this->createGallery();

        // Prepare a fake uploaded file
        $file = new UploadedFile(
            __DIR__.'/../Fixtures/test.jpg', // must exist in tests/Fixtures
            'test.jpg',
            'image/jpeg',
            null,
            true
        );

        $this->httpClient->request('GET', self::TEST_ROUTE.'/create');
        $this->httpClient->submitForm('Zapisz', [
            'photos' => [
                'title'   => 'Test Photo',
                'text'    => 'Photo description',
                'gallery' => $gallery->getId(),
                'file'    => $file,
            ],
        ]);

        $status = $this->httpClient->getResponse()->getStatusCode();
        $this->assertEquals(302, $status); // redirect after save
    }

    /**
     * Test editing an existing photo.
     *
     * @return void No value is returned, assertions validate redirect status
     */
    public function testEditPhotoSubmit(): void
    {
        $user = $this->createUser(['ROLE_ADMIN']);
        $this->httpClient->loginUser($user);

        $photo = $this->createPhoto();

        $this->httpClient->request('GET', self::TEST_ROUTE.'/'.$photo->getId().'/edit');
        $this->httpClient->submitForm('Zapisz', [
            'photos' => [
                'title' => 'Edited Photo',
                'text'  => 'Edited description',
            ],
        ]);

        $status = $this->httpClient->getResponse()->getStatusCode();
        $this->assertEquals(302, $status);
    }

    /**
     * Test deleting a photo.
     *
     * @return void No value is returned, assertions validate redirect status
     */
    public function testDeletePhoto(): void
    {
        $user = $this->createUser(['ROLE_ADMIN']);
        $this->httpClient->loginUser($user);

        $photo = $this->createPhoto();

        $this->httpClient->request('GET', self::TEST_ROUTE.'/'.$photo->getId().'/delete');
        $this->httpClient->submitForm('Usuń');

        $status = $this->httpClient->getResponse()->getStatusCode();
        $this->assertEquals(302, $status);
    }

    /**
     * Helper method to create a test Photo entity.
     *
     * @return Photo The created Photo entity used for testing
     */
    private function createPhoto(): Photo
    {
        $gallery = $this->createGallery();

        $photo = new Photo();
        $photo->setTitle('Test Photo');
        $photo->setText('Some description');
        $photo->setGallery($gallery);
        $photo->setFilename('test.jpg');
        $photo->setCreatedAt(new \DateTimeImmutable());
        $photo->setUpdatedAt(new \DateTimeImmutable());

        /** @var PhotosService $repo */
        $repo = static::getContainer()->get(PhotosService::class);
        $repo->save($photo);

        return $photo;
    }

    /**
     * Helper method to create a test Gallery entity.
     *
     * @return Gallery The created Gallery entity used for testing
     */
    private function createGallery(): Gallery
    {
        $gallery = new Gallery();
        $gallery->setTitle('Test Gallery');
        $gallery->setCreatedAt(new \DateTimeImmutable());
        $gallery->setUpdatedAt(new \DateTimeImmutable());

        $repo = static::getContainer()->get('App\Service\GalleriesService');
        $repo->save($gallery);

        return $gallery;
    }

    /**
     * Helper method to create a test User entity.
     *
     * @param array $roles Roles assigned to the created user
     *
     * @return User The created User entity used for authentication in tests
     */
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
