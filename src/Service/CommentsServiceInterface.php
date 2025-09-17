<?php

/*
 This work, including the code samples, is licensed under a Creative Commons BY-SA 3.0 license.
 */

namespace App\Service;

use App\Entity\Comment;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Knp\Component\Pager\Pagination\PaginationInterface;

/**
 * Interface CommentsServiceInterface.
 *
 * Defines the public API for CommentsService, including listing,
 * saving, and deleting Comment entities.
 */
interface CommentsServiceInterface
{
    /**
     * Get a paginated list of comments.
     *
     * @param int $page Page number to retrieve
     *
     * @return PaginationInterface<string, mixed> Paginated list of Comment entities
     */
    public function createPaginatedList(int $page): PaginationInterface;

    /**
     * Save a comment entity.
     *
     * @param Comment $comment The Comment entity to save
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function save(Comment $comment): void;

    /**
     * Delete a comment entity.
     *
     * @param Comment $comment The Comment entity to delete
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function delete(Comment $comment): void;
}
