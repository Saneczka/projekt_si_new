<?php
/**
 * AlbumRepository
 */

namespace App\Repository;

use App\Entity\Album;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Class AlbumRepository
 */
class AlbumRepository extends ServiceEntityRepository
{
    /**
     * UserRepository constructor.
     * @param \Doctrine\Persistence\ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Album::class);
    }

    /**
     * Query all records.
     * @param array $filters
     *
     * @return \Doctrine\ORM\QueryBuilder Query builder
     */
    public function queryAll(array $filters = []): QueryBuilder
    {
        $qb = $this
            ->createQueryBuilder('album')
            ->select('album', 'cover', 'images')
            ->leftJoin('album.cover', 'cover')
            ->leftJoin('album.images', 'images')
            ->orderBy('album.id', 'DESC');

        return $qb;
    }

    /**
     * Save record.
     * @param \App\Entity\Album $album Album entity
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function save(Album $album): void
    {
        $this->_em->persist($album);
        $this->_em->flush();
    }


    /**
     * Delete record.
     * @param \App\Entity\Album $album Album entity
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function delete(Album $album): void
    {
        $this->_em->remove($album);
        $this->_em->flush();
    }
}
