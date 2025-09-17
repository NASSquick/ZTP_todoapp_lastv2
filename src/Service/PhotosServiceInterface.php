<?php

/*
 This work, including the code samples, is licensed under a Creative Commons BY-SA 3.0 license.
 */

namespace App\Service;

use App\Entity\Photo;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Interface PhotosServiceInterface.
 *
 * Defines the public API for PhotosService, including listing, retrieving,
 * saving, and deleting Photo entities.
 */
interface PhotosServiceInterface
{
    /**
     * Create a paginated list of photos.
     *
     * @param int $page The page number to retrieve
     *
     * @return PaginationInterface Paginated list of Photo entities
     */
    public function createPaginatedList(int $page): PaginationInterface;

    /**
     * Get one photo entity by ID.
     *
     * @param int $id The photo ID
     *
     * @return Photo|null The Photo entity or null if not found
     */
    public function getOne(int $id): ?Photo;

    /**
     * Get a photo along with its comments.
     *
     * @param int $id The photo ID
     *
     * @return array|null Array with keys 'photo' and 'comments', or null if not found
     */
    public function getOneWithComments(int $id): ?array;

    /**
     * Save a photo entity and optionally upload a file.
     *
     * @param Photo             $photo The Photo entity to save
     * @param UploadedFile|null $file  Optional uploaded file
     */
    public function save(Photo $photo, ?UploadedFile $file = null): void;

    /**
     * Delete a photo entity.
     *
     * @param Photo $photo The Photo entity to delete
     */
    public function delete(Photo $photo): void;
}
