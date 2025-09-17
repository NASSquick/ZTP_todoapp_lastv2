<?php

/*
 This work, including the code samples, is licensed under a Creative Commons BY-SA 3.0 license.
 */

namespace App\Service;

use App\Entity\Comment;
use App\Repository\CommentsRepository;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * Class CommentsService.
 *
 * Provides methods to manage Comment entities including
 * listing, saving, and deleting them.
 */
class CommentsService implements CommentsServiceInterface
{
    public const PAGINATOR_ITEMS_PER_PAGE = 10;

    private CommentsRepository $commentsRepository;
    private PaginatorInterface $paginator;

    /**
     * CommentsService constructor.
     *
     * @param CommentsRepository $commentsRepository Repository for comments
     * @param PaginatorInterface $paginator          Paginator service
     */
    public function __construct(CommentsRepository $commentsRepository, PaginatorInterface $paginator)
    {
        $this->commentsRepository = $commentsRepository;
        $this->paginator = $paginator;
    }

    /**
     * Create a paginated list of comments.
     *
     * @param int $page The current page number
     *
     * @return PaginationInterface<string, mixed> Paginated list of Comment entities
     */
    public function createPaginatedList(int $page): PaginationInterface
    {
        return $this->paginator->paginate(
            $this->commentsRepository->queryAll(),
            $page,
            self::PAGINATOR_ITEMS_PER_PAGE
        );
    }

    /**
     * Save a comment entity.
     *
     * @param Comment $comment The Comment entity to save
     */
    public function save(Comment $comment): void
    {
        $this->commentsRepository->save($comment);
    }

    /**
     * Delete a comment entity.
     *
     * @param Comment $comment The Comment entity to delete
     */
    public function delete(Comment $comment): void
    {
        $this->commentsRepository->delete($comment);
    }
}
