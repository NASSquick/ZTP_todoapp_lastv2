<?php

/**
 * This file is part of the TODO App project.
 *
 * (c) Hlib Ivanov <email@example.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace App\Tests\Controller;

use App\Entity\Gallery;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\GalleriesService;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Test class for GalleriesController.
 *
 * Provides functional tests for galleries-related routes,
 * verifying creation, editing, showing, and deletion.
 */
class GalleriesControllerTest extends WebTestCase
{
    /**
     * HTTP client used for simulating browser requests.
     */
    private KernelBrowser $httpClient;

    /**
     * Base route for GalleriesController.
     *
     * @var string
     */
    public const TEST_ROUTE = '/Galleries';

    /**
     * Setup test client before each test.
     */
    protected function setUp(): void
    {
        $this->httpClient = static::createClient();
    }

    /**
     * Test index route returns valid HTTP status.
     */
    public function testIndexRoute(): void
    {
        $this->httpClient->request('GET', self::TEST_ROUTE.'/');
        $status = $this->httpClient->getResponse()->getStatusCode();

        $this->assertTrue(in_array($status, [200, 301, 302]));
    }

    /**
     * Test that a gallery can be displayed successfully.
     */
    public function testShowGallery(): void
    {
        $expectedStatus = 200;

        $gallery = $this->createGallery();

        $user = $this->createUser(['ROLE_ADMIN']);
        $this->httpClient->loginUser($user);

        $this->httpClient->request('GET', self::TEST_ROUTE.'/'.$gallery->getId());
        $status = $this->httpClient->getResponse()->getStatusCode();

        $this->assertEquals($expectedStatus, $status);
    }

    /**
     * Test that create gallery form page loads successfully.
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
     * Test submitting create gallery form redirects after save.
     */
    public function testCreateGallerySubmit(): void
    {
        $user = $this->createUser(['ROLE_ADMIN']);
        $this->httpClient->loginUser($user);

        $this->httpClient->request('GET', self::TEST_ROUTE.'/create');
        $this->httpClient->submitForm('Zapisz', [
            'galleries' => [
                'title' => 'New Gallery',
            ],
        ]);

        $status = $this->httpClient->getResponse()->getStatusCode();
        $this->assertEquals(302, $status); // redirect after save
    }

    /**
     * Test editing a gallery redirects after edit.
     */
    public function testEditGallerySubmit(): void
    {
        $user = $this->createUser(['ROLE_ADMIN']);
        $this->httpClient->loginUser($user);

        $gallery = $this->createGallery();

        $this->httpClient->request('GET', self::TEST_ROUTE.'/'.$gallery->getId().'/edit');
        $this->httpClient->submitForm('Zapisz', [
            'galleries' => [
                'title' => 'Edited Gallery',
            ],
        ]);

        $status = $this->httpClient->getResponse()->getStatusCode();
        $this->assertEquals(302, $status); // redirect after edit
    }

    /**
     * Test deleting a gallery redirects after delete.
     */
    public function testDeleteGallery(): void
    {
        $user = $this->createUser(['ROLE_ADMIN']);
        $this->httpClient->loginUser($user);

        $gallery = $this->createGallery();

        $this->httpClient->request('GET', self::TEST_ROUTE.'/'.$gallery->getId().'/delete');
        $this->httpClient->submitForm('Usuń'); // delete button in Twig

        $status = $this->httpClient->getResponse()->getStatusCode();
        $this->assertEquals(302, $status); // redirect after delete
    }

    /**
     * Create and persist a Gallery entity for tests.
     *
     * @return Gallery Created gallery entity
     */
    private function createGallery(): Gallery
    {
        $gallery = new Gallery();
        $gallery->setTitle('Test Gallery');
        $gallery->setCreatedAt(new \DateTimeImmutable());
        $gallery->setUpdatedAt(new \DateTimeImmutable());

        /** @var GalleriesService $repo */
        $repo = static::getContainer()->get(GalleriesService::class);
        $repo->save($gallery);

        return $gallery;
    }

    /**
     * Create and persist a User entity for tests.
     *
     * @param array $roles Roles assigned to the user
     *
     * @return User Created user entity
     */
    private function createUser(array $roles): User
    {
        $passwordHasher = static::getContainer()->get('security.password_hasher');

        $user = new User();
        $user->setEmail('user@example.com');
        $user->setRoles($roles);
        $user->setPassword($passwordHasher->hashPassword($user, 'p@55w0rd'));

        /** @var UserRepository $repo */
        $repo = static::getContainer()->get(UserRepository::class);
        $repo->save($user);

        return $user;
    }
}
