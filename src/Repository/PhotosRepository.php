<?php

/**
 * Photos repository.
 */

namespace App\Repository;

use App\Entity\Photo;
use App\Entity\Gallery;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Class PhotosRepository.
 *
 * Provides methods to manage Photo entities.
 *
 * @method Photo|null find($id, $lockMode = null, $lockVersion = null)
 * @method Photo|null findOneBy(array $criteria, array $orderBy = null)
 * @method Photo[]    findAll()
 * @method Photo[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PhotosRepository extends ServiceEntityRepository
{
    /**
     * Items per page for pagination.
     */
    private const PAGINATOR_PER_PAGE = 10;

    /**
     * PhotosRepository constructor.
     *
     * @param ManagerRegistry $registry The manager registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Photo::class);
    }

    /**
     * Query all photos.
     *
     * @return QueryBuilder Query builder
     */
    public function queryAll(): QueryBuilder
    {
        return $this->createQueryBuilder('p')
            ->orderBy('p.updatedAt', 'DESC');
    }

    /**
     * Get all photos for a specific gallery.
     *
     * @param Gallery $gallery The gallery entity
     *
     * @return Photo[] Array of Photo entities
     */
    public function findByGallery(Gallery $gallery): array
    {
        return $this->createQueryBuilder('p')
            ->where('p.gallery = :gallery')
            ->setParameter('gallery', $gallery)
            ->orderBy('p.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Get one photo with its comments.
     *
     * Since Photo entity is unidirectional for comments, we fetch comments separately.
     *
     * @param int $id Photo ID
     *
     * @return array|null Array with keys 'photo' and 'comments', or null if not found
     */
    public function getOneWithComments(int $id): ?array
    {
        $photo = $this->find($id);

        if (!$photo) {
            return null;
        }

        // Assuming you have CommentsRepository injected in your service layer
        return [
            'photo' => $photo,
            'comments' => $photo->getComments() ?? [],
        ];
    }

    /**
     * Save a photo entity.
     *
     * @param Photo $photo The photo entity to save
     */
    public function save(Photo $photo): void
    {
        $this->_em->persist($photo);
        $this->_em->flush();
    }

    /**
     * Delete a photo entity.
     *
     * @param Photo $photo The photo entity to delete
     */
    public function delete(Photo $photo): void
    {
        $this->_em->remove($photo);
        $this->_em->flush();
    }
}
