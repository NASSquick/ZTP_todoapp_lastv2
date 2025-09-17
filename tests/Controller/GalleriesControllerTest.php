<?php

/**
 * This file is part of the TODO App project.
 *
 * (c) Hlib Ivanov.
 *
 * For license information, see the LICENSE file.
 */

namespace App\Tests\Controller;

use App\Entity\Gallery;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\GalleriesService;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class GalleriesControllerTest.
 *
 * Functional tests for the GalleriesController.
 */
class GalleriesControllerTest extends WebTestCase
{
    private KernelBrowser $httpClient;
    private GalleriesService $galleryService;

    public const TEST_ROUTE = '/Galleries';

    /*** Public Test Methods ***/

    /**
     * Tests the index route of GalleriesController.
     *
     * @return void Returns nothing. Asserts the index route responds with a valid HTTP status.
     */
    public function testIndexRoute(): void
    {
        $this->httpClient->request('GET', self::TEST_ROUTE.'/');
        $status = $this->httpClient->getResponse()->getStatusCode();
        $this->assertTrue(in_array($status, [200, 301, 302]));
    }

    /**
     * Tests displaying a single gallery.
     *
     * @return void Returns nothing. Asserts that GET request returns HTTP 200.
     */
    public function testShowGallery(): void
    {
        $gallery = $this->createGallery();
        $user = $this->createUser(['ROLE_ADMIN']);
        $this->httpClient->loginUser($user);

        $this->httpClient->request('GET', self::TEST_ROUTE.'/'.$gallery->getId());
        $status = $this->httpClient->getResponse()->getStatusCode();
        $this->assertEquals(200, $status);
    }

    /**
     * Tests rendering the gallery creation form.
     *
     * @return void Returns nothing. Asserts that the creation form page is rendered successfully.
     */
    public function testCreateGalleryForm(): void
    {
        $user = $this->createUser(['ROLE_ADMIN']);
        $this->httpClient->loginUser($user);

        $this->httpClient->request('GET', self::TEST_ROUTE.'/create');
        $status = $this->httpClient->getResponse()->getStatusCode();
        $this->assertEquals(200, $status);
    }

    /**
     * Tests submitting a new gallery creation form.
     *
     * @return void Returns nothing. Asserts a 302 redirect occurs after successful creation.
     */
    public function testCreateGallerySubmit(): void
    {
        $user = $this->createUser(['ROLE_ADMIN']);
        $this->httpClient->loginUser($user);

        $crawler = $this->httpClient->request('GET', self::TEST_ROUTE.'/create');
        $form = $crawler->selectButton('Zapisz')->form([
            'galleries[title]' => 'New Gallery Test',
        ]);

        $this->httpClient->submit($form);
        $status = $this->httpClient->getResponse()->getStatusCode();
        $this->assertEquals(302, $status);
    }

    /**
     * Tests submitting the gallery edit form.
     *
     * @return void Returns nothing. Asserts a 302 redirect occurs after editing.
     */
    public function testEditGallerySubmit(): void
    {
        $gallery = $this->createGallery();
        $user = $this->createUser(['ROLE_ADMIN']);
        $this->httpClient->loginUser($user);

        $crawler = $this->httpClient->request('GET', self::TEST_ROUTE.'/'.$gallery->getId().'/edit');
        $form = $crawler->selectButton('Zapisz')->form([
            'galleries[title]' => 'Edited Gallery Test',
        ]);

        $this->httpClient->submit($form);
        $status = $this->httpClient->getResponse()->getStatusCode();
        $this->assertEquals(302, $status);
    }

    /**
     * Tests deleting a gallery.
     *
     * @return void Returns nothing. Asserts a 302 redirect occurs after deleting a gallery.
     */
    public function testDeleteGallery(): void
    {
        $gallery = $this->createGallery();
        $user = $this->createUser(['ROLE_ADMIN']);
        $this->httpClient->loginUser($user);

        $crawler = $this->httpClient->request('GET', self::TEST_ROUTE.'/'.$gallery->getId().'/delete');
        $form = $crawler->selectButton('Usuń')->form();
        $this->httpClient->submit($form);

        $status = $this->httpClient->getResponse()->getStatusCode();
        $this->assertEquals(302, $status);
    }

    /*** Protected Methods ***/

    /**
     * Sets up the test client and service before each test.
     *
     * @return void Returns nothing. Initializes the client and service dependencies.
     */
    protected function setUp(): void
    {
        $this->httpClient = static::createClient();
        $this->galleryService = static::getContainer()->get(GalleriesService::class);
    }

    /*** Private Helper Methods ***/

    /**
     * Creates a gallery for testing purposes.
     *
     * @return Gallery returns the created Gallery entity ready to be used in tests
     */
    private function createGallery(): Gallery
    {
        $gallery = new Gallery();
        $gallery->setTitle('Test Gallery '.uniqid());
        $gallery->setCreatedAt(new \DateTimeImmutable());
        $gallery->setUpdatedAt(new \DateTimeImmutable());

        $this->galleryService->save($gallery);

        return $gallery;
    }

    /**
     * Creates a user with given roles.
     *
     * @param array $roles Roles for the new user
     *
     * @return User Returns the created User entity with the specified roles
     */
    private function createUser(array $roles): User
    {
        $passwordHasher = static::getContainer()->get('security.password_hasher');

        $user = new User();
        $user->setEmail('user'.uniqid().'@example.com');
        $user->setRoles($roles);
        $user->setPassword($passwordHasher->hashPassword($user, 'p@55w0rd'));

        /** @var UserRepository $repo */
        $repo = static::getContainer()->get(UserRepository::class);
        $repo->save($user);

        return $user;
    }
}
