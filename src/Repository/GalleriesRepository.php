<?php

/**
 * Galleries repository.
 */

namespace App\Repository;

use App\Entity\Gallery;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Class GalleriesRepository.
 *
 * Provides methods to manage Gallery entities.
 *
 * @method Gallery|null find($id, $lockMode = null, $lockVersion = null)
 * @method Gallery|null findOneBy(array $criteria, array $orderBy = null)
 * @method Gallery[]    findAll()
 * @method Gallery[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GalleriesRepository extends ServiceEntityRepository
{
    /**
     * Items per page for pagination.
     */
    public const PAGINATOR_ITEMS_PER_PAGE = 10;

    /**
     * GalleriesRepository constructor.
     *
     * @param ManagerRegistry $registry The manager registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Gallery::class);
    }

    /**
     * Save a gallery entity.
     *
     * @param Gallery $gallery The gallery entity
     */
    public function save(Gallery $gallery): void
    {
        $this->_em->persist($gallery);
        $this->_em->flush();
    }

    /**
     * Delete a gallery entity.
     *
     * @param Gallery $gallery The gallery entity
     */
    public function delete(Gallery $gallery): void
    {
        $this->_em->remove($gallery);
        $this->_em->flush();
    }

    /**
     * Query all galleries.
     *
     * @return QueryBuilder Query builder
     */
    public function queryAll(): QueryBuilder
    {
        return $this->createQueryBuilder('g')
            ->orderBy('g.updatedAt', 'DESC');
    }
}
