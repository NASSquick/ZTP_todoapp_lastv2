<?php

namespace App\Tests\Controller;

use App\Entity\Gallery;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\GalleriesService;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class GalleriesControllerTest extends WebTestCase
{
    private KernelBrowser $httpClient;

    public const TEST_ROUTE = '/Galleries';

    protected function setUp(): void
    {
        $this->httpClient = static::createClient();
    }

    // ---------- Index ----------
    public function testIndexRoute(): void
    {
        $this->httpClient->request('GET', self::TEST_ROUTE.'/');
        $status = $this->httpClient->getResponse()->getStatusCode();

        $this->assertTrue(in_array($status, [200, 301, 302]));
    }

    // ---------- Show ----------
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

    // ---------- Create Form ----------
    public function testCreateGalleryForm(): void
    {
        $user = $this->createUser(['ROLE_ADMIN']);
        $this->httpClient->loginUser($user);

        $this->httpClient->request('GET', self::TEST_ROUTE.'/create');
        $status = $this->httpClient->getResponse()->getStatusCode();

        $this->assertEquals(200, $status);
    }

    // ---------- Create Submit ----------
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

    // ---------- Edit ----------
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

    // ---------- Delete ----------
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

    // ---------- Helpers ----------
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
