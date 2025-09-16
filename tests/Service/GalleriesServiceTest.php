<?php

namespace App\Tests\Service;

use App\Entity\Gallery;
use App\Repository\GalleriesRepository;
use App\Repository\PhotosRepository;
use App\Service\GalleriesService;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use Doctrine\ORM\QueryBuilder;
use PHPUnit\Framework\TestCase;

class GalleriesServiceTest extends TestCase
{
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
            ->with(
                $qbMock,
                $page,
                GalleriesRepository::PAGINATOR_ITEMS_PER_PAGE
            )
            ->willReturn($paginationMock);

        $service = new GalleriesService($repoMock, $this->createMock(PhotosRepository::class), $paginatorMock);

        $result = $service->createPaginatedList($page);

        $this->assertSame($paginationMock, $result);
    }

    public function testSave(): void
    {
        $gallery = new Gallery();

        $repoMock = $this->createMock(GalleriesRepository::class);
        $repoMock->expects($this->once())
            ->method('save')
            ->with($gallery);

        $service = new GalleriesService($repoMock, $this->createMock(PhotosRepository::class), $this->createMock(PaginatorInterface::class));
        $service->save($gallery);
    }

    public function testDelete(): void
    {
        $gallery = new Gallery();

        $repoMock = $this->createMock(GalleriesRepository::class);
        $repoMock->expects($this->once())
            ->method('delete')
            ->with($gallery);

        $service = new GalleriesService($repoMock, $this->createMock(PhotosRepository::class), $this->createMock(PaginatorInterface::class));
        $service->delete($gallery);
    }

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

        $service = new GalleriesService($galleriesRepoMock, $photosRepoMock, $this->createMock(PaginatorInterface::class));

        $result = $service->getOneWithPhotos(1);

        $this->assertSame(['gallery' => $gallery, 'photos' => $photos], $result);
    }

    public function testGetOneWithPhotosNotFound(): void
    {
        $galleriesRepoMock = $this->createMock(GalleriesRepository::class);
        $galleriesRepoMock->expects($this->once())
            ->method('find')
            ->with(1)
            ->willReturn(null);

        $service = new GalleriesService($galleriesRepoMock, $this->createMock(PhotosRepository::class), $this->createMock(PaginatorInterface::class));

        $this->assertNull($service->getOneWithPhotos(1));
    }
}
