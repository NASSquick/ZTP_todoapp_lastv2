<?php

namespace App\Tests\Service;

use App\Entity\Comment;
use App\Repository\CommentsRepository;
use App\Service\CommentsService;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use Doctrine\ORM\QueryBuilder;
use PHPUnit\Framework\TestCase;

class CommentsServiceTest extends TestCase
{
    public function testCreatePaginatedList(): void
    {
        $page = 1;

        // Mock the QueryBuilder
        $qbMock = $this->createMock(QueryBuilder::class);

        // Mock the repository to return the QueryBuilder
        $repoMock = $this->createMock(CommentsRepository::class);
        $repoMock->expects($this->once())
            ->method('queryAll')
            ->willReturn($qbMock);

        // Mock the paginator to return a PaginationInterface mock
        $paginationMock = $this->createMock(PaginationInterface::class);
        $paginatorMock = $this->createMock(PaginatorInterface::class);
        $paginatorMock->expects($this->once())
            ->method('paginate')
            ->with(
                $qbMock,
                $page,
                CommentsService::PAGINATOR_ITEMS_PER_PAGE
            )
            ->willReturn($paginationMock);

        $service = new CommentsService($repoMock, $paginatorMock);

        $result = $service->createPaginatedList($page);

        $this->assertSame($paginationMock, $result);
    }

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
