<?php

/**
 * This file is part of the TODO App project.
 *
 * (c) Hlib Ivanov.
 *
 * For license information, see the LICENSE file.
 */

namespace App\Tests\Controller;

use App\Entity\Photo;
use App\Entity\Gallery;
use App\Entity\User;
use App\Service\PhotosService;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Repository\UserRepository;
use App\Service\GalleriesService;

/**
 * Class PhotosControllerTest.
 *
 * Functional tests for the PhotosController.
 */
class PhotosControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private PhotosService $photosService;
    private GalleriesService $galleriesService;
    private UserRepository $userRepository;

    /*** Public Test Methods ***/

    /**
     * Tests the index route of PhotosController.
     *
     * @return void Returns nothing. Asserts that the index route responds successfully (200, 301, or 302).
     */
    public function testIndexRoute(): void
    {
        $this->client->request('GET', '/Photos/');
        $this->assertContains(
            $this->client->getResponse()->getStatusCode(),
            [200, 301, 302]
        );
    }

    /**
     * Tests displaying a single photo.
     *
     * @return void Returns nothing. Asserts that a GET request to the photo's page returns a 200 status code.
     */
    public function testShowPhoto(): void
    {
        $user = $this->createUser();
        $this->client->loginUser($user);

        $photo = $this->createPhoto();

        $this->client->request('GET', '/Photos/'.$photo->getId());
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    /**
     * Tests rendering the photo creation form.
     *
     * @return void Returns nothing. Asserts that the creation form page is successfully rendered.
     */
    public function testCreatePhotoForm(): void
    {
        $user = $this->createUser();
        $this->client->loginUser($user);

        $this->client->request('GET', '/Photos/create');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    /**
     * Tests submitting a new photo creation form.
     *
     * @return void Returns nothing. Submits the form and asserts a 302 redirect after successful creation.
     */
    public function testCreatePhotoSubmit(): void
    {
        $user = $this->createUser();
        $this->client->loginUser($user);

        $gallery = $this->createGallery();

        $file = new UploadedFile(
            __DIR__.'/../Fixtures/test.jpg', // ensure this file exists
            'test.jpg',
            'image/jpeg',
            null,
            true
        );

        $this->client->request('GET', '/Photos/create');
        $this->client->submitForm('Zapisz', [
            'photos' => [
                'title'   => 'New Photo',
                'text'    => 'Photo description',
                'gallery' => $gallery->getId(),
                'file'    => $file,
            ],
        ]);

        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
    }

    /**
     * Tests submitting the photo edit form.
     *
     * @return void Returns nothing. Submits the edit form and asserts that changes are persisted.
     */
    public function testEditPhotoSubmit(): void
    {
        $user = $this->createUser();
        $this->client->loginUser($user);

        $photo = $this->createPhoto();

        $this->client->request('GET', '/Photos/'.$photo->getId().'/edit');
        $this->client->submitForm('Zapisz', [
            'photos' => [
                'title' => 'Edited Photo',
                'text'  => 'Edited description',
            ],
        ]);

        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());

        $updatedPhoto = $this->photosService->getOne($photo->getId());
        $this->assertSame('Edited Photo', $updatedPhoto->getTitle());
        $this->assertSame('Edited description', $updatedPhoto->getText());
    }

    /**
     * Tests deleting a photo.
     *
     * @return void Returns nothing. Submits the delete form and asserts that the photo is removed from storage.
     */
    public function testDeletePhoto(): void
    {
        $user = $this->createUser();
        $this->client->loginUser($user);

        $photo = $this->createPhoto();

        $crawler = $this->client->request('GET', '/Photos/'.$photo->getId().'/delete');
        $form = $crawler->filter('form')->form();
        $this->client->submit($form);

        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
        $this->assertNull($this->photosService->getOne($photo->getId()));
    }

    /*** Protected Methods ***/

    /**
     * Sets up the test client and services before each test.
     *
     * @return void Returns nothing. Initializes the client and service dependencies for tests.
     */
    protected function setUp(): void
    {
        $this->client = static::createClient();
        $container = static::getContainer();

        $this->photosService = $container->get(PhotosService::class);
        $this->galleriesService = $container->get(GalleriesService::class);
        $this->userRepository = $container->get(UserRepository::class);
    }

    /*** Private Helper Methods ***/

    /**
     * Creates a user with the given roles.
     *
     * @param array $roles Roles for the new user
     *
     * @return User Returns the created User entity with the specified roles
     */
    private function createUser(array $roles = ['ROLE_ADMIN']): User
    {
        $passwordHasher = static::getContainer()->get('security.password_hasher');

        $user = new User();
        $user->setEmail('user@example.com');
        $user->setRoles($roles);
        $user->setPassword($passwordHasher->hashPassword($user, 'p@55w0rd'));

        $this->userRepository->save($user);

        return $user;
    }

    /**
     * Creates a gallery for testing purposes.
     *
     * @return Gallery Returns the created Gallery entity ready to be associated with photos
     */
    private function createGallery(): Gallery
    {
        $gallery = new Gallery();
        $gallery->setTitle('Test Gallery');
        $gallery->setCreatedAt(new \DateTimeImmutable());
        $gallery->setUpdatedAt(new \DateTimeImmutable());

        $this->galleriesService->save($gallery);

        return $gallery;
    }

    /**
     * Creates a photo optionally linked to a provided gallery.
     *
     * @param Gallery|null $gallery The gallery to associate the photo with, or null to create a new one
     *
     * @return Photo Returns the created Photo entity with a gallery assigned
     */
    private function createPhoto(?Gallery $gallery = null): Photo
    {
        $gallery ??= $this->createGallery();

        $photo = new Photo();
        $photo->setTitle('Test Photo');
        $photo->setText('Photo description');
        $photo->setGallery($gallery);
        $photo->setFilename('test.jpg');
        $photo->setCreatedAt(new \DateTimeImmutable());
        $photo->setUpdatedAt(new \DateTimeImmutable());

        $this->photosService->save($photo);

        return $photo;
    }
}
