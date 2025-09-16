<?php

/**
 * Photos service.
 */

namespace App\Service;

use App\Entity\Photo;
use App\Repository\PhotosRepository;
use App\Repository\CommentsRepository;
use Doctrine\ORM\NonUniqueResultException;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class PhotosService.
 *
 * Provides methods to manage Photo entities and their comments.
 */
class PhotosService implements TaskServiceInterface
{
    public const PAGINATOR_ITEMS_PER_PAGE = 10;

    private PhotosRepository $photosRepository;
    private CommentsRepository $commentsRepository;
    private PaginatorInterface $paginator;
    private FileUploader $fileUploader;

    /**
     * Constructor.
     *
     * @param PhotosRepository   $photosRepository   Photos repository
     * @param CommentsRepository $commentsRepository Comments repository
     * @param PaginatorInterface $paginator          Paginator service
     * @param FileUploader       $fileUploader       File uploader service
     */
    public function __construct(PhotosRepository $photosRepository, CommentsRepository $commentsRepository, PaginatorInterface $paginator, FileUploader $fileUploader)
    {
        $this->photosRepository = $photosRepository;
        $this->commentsRepository = $commentsRepository;
        $this->paginator = $paginator;
        $this->fileUploader = $fileUploader;
    }

    /**
     * Create a paginated list of photos.
     *
     * @param int $page Page number
     *
     * @return PaginationInterface Paginated photos
     */
    public function createPaginatedList(int $page): PaginationInterface
    {
        return $this->paginator->paginate(
            $this->photosRepository->queryAll(),
            $page,
            self::PAGINATOR_ITEMS_PER_PAGE
        );
    }

    /**
     * Get one photo entity by ID.
     *
     * @param int $id Photo ID
     *
     * @return Photo|null Photo entity or null
     */
    public function getOne(int $id): ?Photo
    {
        return $this->photosRepository->find($id);
    }

    /**
     * Get a photo along with its comments.
     *
     * @param int $id Photo ID
     *
     * @return array|null Array with keys 'photo' and 'comments'
     *
     * @throws NonUniqueResultException
     */
    public function getOneWithComments(int $id): ?array
    {
        $photo = $this->photosRepository->find($id);

        if (!$photo) {
            return null;
        }

        $comments = $this->commentsRepository->findBy(['photo' => $photo]);

        return [
            'photo' => $photo,
            'comments' => $comments,
        ];
    }

    /**
     * Save a photo entity and optionally upload a file.
     *
     * @param Photo             $photo Photo entity
     * @param UploadedFile|null $file  Optional uploaded file
     */
    public function save(Photo $photo, ?UploadedFile $file = null): void
    {
        if ($file) {
            $filename = $this->fileUploader->upload($file);
            $photo->setFilename($filename);
        }

        $photo->setUpdatedAt(new \DateTime());
        $this->photosRepository->save($photo);
    }

    /**
     * Delete a photo entity.
     *
     * @param Photo $photo Photo entity
     */
    public function delete(Photo $photo): void
    {
        $this->photosRepository->delete($photo);
    }
}
