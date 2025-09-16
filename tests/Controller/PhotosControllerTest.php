<?php

namespace App\Tests\Controller;

use App\Entity\Photo;
use App\Entity\Gallery;
use App\Entity\User;
use App\Repository\PhotosRepository;
use App\Repository\UserRepository;
use App\Service\PhotosService;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class PhotosControllerTest extends WebTestCase
{
    private KernelBrowser $httpClient;

    public const TEST_ROUTE = '/Photos';

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

    // ---------- Create Form ----------
    public function testCreatePhotoForm(): void
    {
        $user = $this->createUser(['ROLE_ADMIN']);
        $this->httpClient->loginUser($user);

        $this->httpClient->request('GET', self::TEST_ROUTE.'/create');
        $status = $this->httpClient->getResponse()->getStatusCode();

        $this->assertEquals(200, $status);
    }

    // ---------- Create Submit ----------
    public function testCreatePhotoSubmit(): void
    {
        $user = $this->createUser(['ROLE_ADMIN']);
        $this->httpClient->loginUser($user);

        $gallery = $this->createGallery();

        // Prepare a fake uploaded file
        $file = new UploadedFile(
            __DIR__.'/../Fixtures/test.jpg', // make sure this file exists in your tests/Fixtures folder
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
            ]
        ]);

        $status = $this->httpClient->getResponse()->getStatusCode();
        $this->assertEquals(302, $status); // redirect after save
    }

    // ---------- Edit ----------
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
            ]
        ]);

        $status = $this->httpClient->getResponse()->getStatusCode();
        $this->assertEquals(302, $status);
    }

    // ---------- Delete ----------
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

    // ---------- Helpers ----------
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
