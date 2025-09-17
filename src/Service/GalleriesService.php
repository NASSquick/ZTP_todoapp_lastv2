<?php

/**
 * Galleries service.
 */

namespace App\Service;

use App\Entity\Gallery;
use App\Repository\GalleriesRepository;
use App\Repository\PhotosRepository;
use Doctrine\ORM\NonUniqueResultException;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * Class GalleriesService.
 *
 * Provides methods to manage Gallery entities and fetch their photos.
 */
class GalleriesService implements GalleriesServiceInterface
{
    /**
     * Galleries repository.
     */
    private GalleriesRepository $galleriesRepository;

    /**
     * Photos repository.
     */
    private PhotosRepository $photosRepository;

    /**
     * Paginator service.
     */
    private PaginatorInterface $paginator;

    /**
     * GalleriesService constructor.
     *
     * @param GalleriesRepository $galleriesRepository Galleries repository
     * @param PhotosRepository    $photosRepository    Photos repository
     * @param PaginatorInterface  $paginator           Paginator service
     */
    public function __construct(GalleriesRepository $galleriesRepository, PhotosRepository $photosRepository, PaginatorInterface $paginator)
    {
        $this->galleriesRepository = $galleriesRepository;
        $this->photosRepository = $photosRepository;
        $this->paginator = $paginator;
    }

    /**
     * Create paginated list of galleries.
     *
     * @param int $page Page number
     *
     * @return PaginationInterface Paginated galleries
     */
    public function createPaginatedList(int $page): PaginationInterface
    {
        return $this->paginator->paginate(
            $this->galleriesRepository->queryAll(),
            $page,
            GalleriesRepository::PAGINATOR_ITEMS_PER_PAGE
        );
    }

    /**
     * Save a gallery entity.
     *
     * @param Gallery $gallery Gallery entity
     */
    public function save(Gallery $gallery): void
    {
        $this->galleriesRepository->save($gallery);
    }

    /**
     * Delete a gallery entity.
     *
     * @param Gallery $gallery Gallery entity
     */
    public function delete(Gallery $gallery): void
    {
        $this->galleriesRepository->delete($gallery);
    }

    /**
     * Get one gallery by ID with its photos.
     *
     * Since Gallery is unidirectional, photos are fetched separately
     * from the PhotosRepository.
     *
     * @param int $id Gallery ID
     *
     * @return array|null Array with keys 'gallery' and 'photos', or null if gallery not found
     *
     * @throws NonUniqueResultException
     */
    public function getOneWithPhotos(int $id): ?array
    {
        $gallery = $this->galleriesRepository->find($id);

        if (!$gallery) {
            return null;
        }

        $photos = $this->photosRepository->findBy(['gallery' => $gallery]);

        return [
            'gallery' => $gallery,
            'photos'  => $photos,
        ];
    }
}
