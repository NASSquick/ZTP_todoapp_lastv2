<?php

/**
 * This file is part of the TODO App project.
 *
 * (c) Hlib Ivanov
 *
 * Unit tests for the CommentsService class.
 * Ensures correct behavior for paginated list creation, saving, and deleting comments.
 */

namespace App\Tests\Service;

use App\Entity\Comment;
use App\Repository\CommentsRepository;
use App\Service\CommentsService;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use Doctrine\ORM\QueryBuilder;
use PHPUnit\Framework\TestCase;

/**
 * Class CommentsServiceTest.
 *
 * Tests the CommentsService functionality:
 * - createPaginatedList()
 * - save()
 * - delete()
 */
class CommentsServiceTest extends TestCase
{
    /**
     * Test that createPaginatedList returns a PaginationInterface object.
     *
     * Mocks repository and paginator to verify correct integration.
     *
     * @return void this test performs assertions and does not return a value
     */
    public function testCreatePaginatedList(): void
    {
        $page = 1;

        $qbMock = $this->createMock(QueryBuilder::class);

        $repoMock = $this->createMock(CommentsRepository::class);
        $repoMock->expects($this->once())
            ->method('queryAll')
            ->willReturn($qbMock);

        $paginationMock = $this->createMock(PaginationInterface::class);
        $paginatorMock = $this->createMock(PaginatorInterface::class);
        $paginatorMock->expects($this->once())
            ->method('paginate')
            ->with($qbMock, $page, CommentsService::PAGINATOR_ITEMS_PER_PAGE)
            ->willReturn($paginationMock);

        $service = new CommentsService($repoMock, $paginatorMock);

        $result = $service->createPaginatedList($page);

        $this->assertSame($paginationMock, $result);
    }

    /**
     * Test that save() persists a comment via the repository.
     *
     * @return void this test performs assertions and does not return a value
     */
    public function testSave(): void
    {
        $comment = new Comment();

        $repoMock = $this->createMock(CommentsRepository::class);
        $repoMock->expects($this->once())
            ->method('save')
            ->with($comment);

        $paginatorMock = $this->createMock(PaginatorInterface::class);

        $service = new CommentsService($repoMock, $paginatorMock);
        $service->save($comment);
    }

    /**
     * Test that delete() removes a comment via the repository.
     *
     * @return void this test performs assertions and does not return a value
     */
    public function testDelete(): void
    {
        $comment = new Comment();

        $repoMock = $this->createMock(CommentsRepository::class);
        $repoMock->expects($this->once())
            ->method('delete')
            ->with($comment);

        $paginatorMock = $this->createMock(PaginatorInterface::class);

        $service = new CommentsService($repoMock, $paginatorMock);
        $service->delete($comment);
    }
}
