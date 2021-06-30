<?php
/**
 * ImageRepository
 */

namespace App\Repository;

use App\Entity\Image;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Class ImageRepository
 */
class ImageRepository extends ServiceEntityRepository
{
    /**
     * UserRepository constructor.
     * @param \Doctrine\Persistence\ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Image::class);
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
            ->createQueryBuilder('image')
            //->leftJoin('image.comments', 'comments')
            ->orderBy('image.id', 'DESC');

        return $qb;
    }

    /**
     * Save record.
     * @param \App\Entity\Image $image Image entity
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function save(Image $image): void
    {
        $this->_em->persist($image);
        $this->_em->flush();
    }


    /**
     * Delete record.
     * @param \App\Entity\Image $image Image entity
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function delete(Image $image): void
    {
        $this->_em->remove($image);
        $this->_em->flush();
    }
}
