<?php

/*
 This work, including the code samples, is licensed under a Creative Commons BY-SA 3.0 license.
 */

namespace App\Service;

use App\Entity\Gallery;
use Doctrine\ORM\NonUniqueResultException;
use Knp\Component\Pager\Pagination\PaginationInterface;

/**
 * Interface GalleriesServiceInterface.
 *
 * Defines the public API for GalleriesService, including listing,
 * retrieving, saving, and deleting Gallery entities.
 */
interface GalleriesServiceInterface
{
    /**
     * Create a paginated list of galleries.
     *
     * @param int $page The page number to retrieve
     *
     * @return PaginationInterface Paginated list of Gallery entities
     */
    public function createPaginatedList(int $page): PaginationInterface;

    /**
     * Save a gallery entity.
     *
     * @param Gallery $gallery The Gallery entity to save
     */
    public function save(Gallery $gallery): void;

    /**
     * Delete a gallery entity.
     *
     * @param Gallery $gallery The Gallery entity to delete
     */
    public function delete(Gallery $gallery): void;

    /**
     * Get one gallery by ID with its photos.
     *
     * @param int $id The Gallery ID
     *
     * @return array|null Array containing the gallery and its photos, or null if not found
     *
     * @throws NonUniqueResultException
     */
    public function getOneWithPhotos(int $id): ?array;
}
