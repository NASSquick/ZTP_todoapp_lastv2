<?php

/**
 * This file is part of the TODO App project.
 *
 * (c) Hlib Ivanov
 *
 * Unit tests for the PhotosService class.
 * Ensures correct behavior for paginated list creation, retrieval, saving, and deleting photos.
 */

namespace App\Tests\Service;

use App\Entity\Photo;
use App\Repository\PhotosRepository;
use App\Repository\CommentsRepository;
use App\Service\PhotosService;
use App\Service\FileUploader;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Doctrine\ORM\QueryBuilder;

/**
 * Class PhotosServiceTest.
 *
 * Tests PhotosService functionality:
 * - createPaginatedList()
 * - getOne()
 * - getOneWithComments()
 * - save()
 * - delete()
 */
class PhotosServiceTest extends TestCase
{
    /**
     * Test creation of a paginated list of photos.
     *
     * @return void this test performs assertions and does not return a value
     */
    public function testCreatePaginatedList(): void
    {
        $page = 1;

        $qbMock = $this->createMock(QueryBuilder::class);

        $photosRepoMock = $this->createMock(PhotosRepository::class);
        $photosRepoMock->expects($this->once())
            ->method('queryAll')
            ->willReturn($qbMock);

        $paginationMock = $this->createMock(PaginationInterface::class);

        $paginatorMock = $this->createMock(PaginatorInterface::class);
        $paginatorMock->expects($this->once())
            ->method('paginate')
            ->with($qbMock, $page, PhotosService::PAGINATOR_ITEMS_PER_PAGE)
            ->willReturn($paginationMock);

        $service = new PhotosService(
            $photosRepoMock,
            $this->createMock(CommentsRepository::class),
            $paginatorMock,
            $this->createMock(FileUploader::class)
        );

        $result = $service->createPaginatedList($page);

        $this->assertSame($paginationMock, $result);
    }

    /**
     * Test retrieving a single photo by ID.
     *
     * @return void this test performs assertions and does not return a value
     */
    public function testGetOne(): void
    {
        $photo = new Photo();

        $photosRepoMock = $this->createMock(PhotosRepository::class);
        $photosRepoMock->expects($this->once())
            ->method('find')
            ->with(1)
            ->willReturn($photo);

        $service = new PhotosService(
            $photosRepoMock,
            $this->createMock(CommentsRepository::class),
            $this->createMock(PaginatorInterface::class),
            $this->createMock(FileUploader::class)
        );

        $this->assertSame($photo, $service->getOne(1));
    }

    /**
     * Test retrieving a photo along with its comments.
     *
     * @return void this test performs assertions and does not return a value
     */
    public function testGetOneWithComments(): void
    {
        $photo = new Photo();
        $comments = ['comment1', 'comment2'];

        $photosRepoMock = $this->createMock(PhotosRepository::class);
        $photosRepoMock->expects($this->once())
            ->method('find')
            ->with(1)
            ->willReturn($photo);

        $commentsRepoMock = $this->createMock(CommentsRepository::class);
        $commentsRepoMock->expects($this->once())
            ->method('findBy')
            ->with(['photo' => $photo])
            ->willReturn($comments);

        $service = new PhotosService(
            $photosRepoMock,
            $commentsRepoMock,
            $this->createMock(PaginatorInterface::class),
            $this->createMock(FileUploader::class)
        );

        $result = $service->getOneWithComments(1);

        $this->assertSame(['photo' => $photo, 'comments' => $comments], $result);
    }

    /**
     * Test retrieving a photo that does not exist.
     *
     * @return void this test performs assertions and does not return a value
     */
    public function testGetOneWithCommentsNotFound(): void
    {
        $photosRepoMock = $this->createMock(PhotosRepository::class);
        $photosRepoMock->expects($this->once())
            ->method('find')
            ->with(1)
            ->willReturn(null);

        $service = new PhotosService(
            $photosRepoMock,
            $this->createMock(CommentsRepository::class),
            $this->createMock(PaginatorInterface::class),
            $this->createMock(FileUploader::class)
        );

        $this->assertNull($service->getOneWithComments(1));
    }

    /**
     * Test saving a photo with an uploaded file.
     *
     * @return void this test performs assertions and does not return a value
     */
    public function testSaveWithFile(): void
    {
        $photo = new Photo();
        $fileMock = $this->createMock(UploadedFile::class);
        $filename = 'test.jpg';

        $fileUploaderMock = $this->createMock(FileUploader::class);
        $fileUploaderMock->expects($this->once())
            ->method('upload')
            ->with($fileMock)
            ->willReturn($filename);

        $photosRepoMock = $this->createMock(PhotosRepository::class);
        $photosRepoMock->expects($this->once())
            ->method('save')
            ->with($photo);

        $service = new PhotosService(
            $photosRepoMock,
            $this->createMock(CommentsRepository::class),
            $this->createMock(PaginatorInterface::class),
            $fileUploaderMock
        );

        $service->save($photo, $fileMock);

        $this->assertSame($filename, $photo->getFilename());
        $this->assertInstanceOf(\DateTime::class, $photo->getUpdatedAt());
    }

    /**
     * Test saving a photo without an uploaded file.
     *
     * @return void this test performs assertions and does not return a value
     */
    public function testSaveWithoutFile(): void
    {
        $photo = new Photo();

        $photosRepoMock = $this->createMock(PhotosRepository::class);
        $photosRepoMock->expects($this->once())
            ->method('save')
            ->with($photo);

        $service = new PhotosService(
            $photosRepoMock,
            $this->createMock(CommentsRepository::class),
            $this->createMock(PaginatorInterface::class),
            $this->createMock(FileUploader::class)
        );

        $service->save($photo, null);

        $this->assertInstanceOf(\DateTime::class, $photo->getUpdatedAt());
        $this->assertNull($photo->getFilename());
    }

    /**
     * Test deleting a photo.
     *
     * @return void this test performs assertions and does not return a value
     */
    public function testDelete(): void
    {
        $photo = new Photo();

        $photosRepoMock = $this->createMock(PhotosRepository::class);
        $photosRepoMock->expects($this->once())
            ->method('delete')
            ->with($photo);

        $service = new PhotosService(
            $photosRepoMock,
            $this->createMock(CommentsRepository::class),
            $this->createMock(PaginatorInterface::class),
            $this->createMock(FileUploader::class)
        );

        $service->delete($photo);
    }
}
