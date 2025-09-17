<?php

/**
 * This file is part of the TODO App project.
 *
 * (c) Hlib Ivanov
 *
 * Unit tests for the GalleriesService class.
 * Ensures correct behavior for paginated list creation, saving, deleting galleries,
 * and retrieving a gallery with its photos.
 */

namespace App\Tests\Service;

use App\Entity\Gallery;
use App\Repository\GalleriesRepository;
use App\Repository\PhotosRepository;
use App\Service\GalleriesService;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use Doctrine\ORM\QueryBuilder;
use PHPUnit\Framework\TestCase;

/**
 * Class GalleriesServiceTest.
 *
 * Tests GalleriesService functionality:
 * - createPaginatedList()
 * - save()
 * - delete()
 * - getOneWithPhotos()
 */
class GalleriesServiceTest extends TestCase
{
    /**
     * Test creation of a paginated list of galleries.
     *
     * Verifies that the service returns the correct PaginationInterface object
     * from the paginator.
     *
     * @return void this test performs assertions and does not return a value
     */
    public function testCreatePaginatedList(): void
    {
        $page = 1;

        $qbMock = $this->createMock(QueryBuilder::class);

        $repoMock = $this->createMock(GalleriesRepository::class);
        $repoMock->expects($this->once())
            ->method('queryAll')
            ->willReturn($qbMock);

        $paginationMock = $this->createMock(PaginationInterface::class);

        $paginatorMock = $this->createMock(PaginatorInterface::class);
        $paginatorMock->expects($this->once())
            ->method('paginate')
            ->with($qbMock, $page, GalleriesRepository::PAGINATOR_ITEMS_PER_PAGE)
            ->willReturn($paginationMock);

        $service = new GalleriesService(
            $repoMock,
            $this->createMock(PhotosRepository::class),
            $paginatorMock
        );

        $result = $service->createPaginatedList($page);

        $this->assertSame($paginationMock, $result);
    }

    /**
     * Test saving a gallery.
     *
     * Ensures that the repository's save() method is called with the gallery entity.
     *
     * @return void this test performs assertions and does not return a value
     */
    public function testSave(): void
    {
        $gallery = new Gallery();

        $repoMock = $this->createMock(GalleriesRepository::class);
        $repoMock->expects($this->once())
            ->method('save')
            ->with($gallery);

        $service = new GalleriesService(
            $repoMock,
            $this->createMock(PhotosRepository::class),
            $this->createMock(PaginatorInterface::class)
        );
        $service->save($gallery);
    }

    /**
     * Test deleting a gallery.
     *
     * Ensures that the repository's delete() method is called with the gallery entity.
     *
     * @return void this test performs assertions and does not return a value
     */
    public function testDelete(): void
    {
        $gallery = new Gallery();

        $repoMock = $this->createMock(GalleriesRepository::class);
        $repoMock->expects($this->once())
            ->method('delete')
            ->with($gallery);

        $service = new GalleriesService(
            $repoMock,
            $this->createMock(PhotosRepository::class),
            $this->createMock(PaginatorInterface::class)
        );
        $service->delete($gallery);
    }

    /**
     * Test retrieving a gallery with its associated photos.
     *
     * Ensures that the service correctly returns an array with keys 'gallery' and 'photos'.
     *
     * @return void this test performs assertions and does not return a value
     */
    public function testGetOneWithPhotos(): void
    {
        $gallery = new Gallery();
        $photos = ['photo1', 'photo2'];

        $galleriesRepoMock = $this->createMock(GalleriesRepository::class);
        $galleriesRepoMock->expects($this->once())
            ->method('find')
            ->with(1)
            ->willReturn($gallery);

        $photosRepoMock = $this->createMock(PhotosRepository::class);
        $photosRepoMock->expects($this->once())
            ->method('findBy')
            ->with(['gallery' => $gallery])
            ->willReturn($photos);

        $service = new GalleriesService(
            $galleriesRepoMock,
            $photosRepoMock,
            $this->createMock(PaginatorInterface::class)
        );

        $result = $service->getOneWithPhotos(1);

        $this->assertSame(['gallery' => $gallery, 'photos' => $photos], $result);
    }

    /**
     * Test retrieving a gallery that does not exist.
     *
     * Ensures that the service returns null when the gallery is not found.
     *
     * @return void this test performs assertions and does not return a value
     */
    public function testGetOneWithPhotosNotFound(): void
    {
        $galleriesRepoMock = $this->createMock(GalleriesRepository::class);
        $galleriesRepoMock->expects($this->once())
            ->method('find')
            ->with(1)
            ->willReturn(null);

        $service = new GalleriesService(
            $galleriesRepoMock,
            $this->createMock(PhotosRepository::class),
            $this->createMock(PaginatorInterface::class)
        );

        $this->assertNull($service->getOneWithPhotos(1));
    }
}
