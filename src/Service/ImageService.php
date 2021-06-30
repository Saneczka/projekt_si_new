<?php
/**
 * ImageService
 */

namespace App\Service;

use App\Entity\Image;
use App\Repository\ImageRepository;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * Class ImageService
 */
class ImageService
{
    const PAGINATOR_ITEMS_PER_PAGE = 10;

    /** @var ImageRepository */
    private $imageRepository;
    /** @var PaginatorInterface */
    private $paginator;

    /**
     * ImageService constructor.
     * @param \App\Repository\ImageRepository         $imageRepository
     * @param \Knp\Component\Pager\PaginatorInterface $paginator
     */
    public function __construct(ImageRepository $imageRepository, PaginatorInterface $paginator)
    {
        $this->imageRepository = $imageRepository;
        $this->paginator = $paginator;
    }

    /**
     * @param array $filters
     * @param int   $page
     *
     * @return \Knp\Component\Pager\Pagination\PaginationInterface
     */
    public function createPaginatedList(array $filters, int $page): PaginationInterface
    {
        return $this->paginator->paginate(
            $this->imageRepository->queryAll($filters),
            $page,
            self::PAGINATOR_ITEMS_PER_PAGE
        );
    }

    /**
     * Zapisuje do bazy
     * @param \App\Entity\Image $image
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function save(Image $image)
    {
        $this->imageRepository->save($image);
    }

    /**
     * Usuwa z bazy
     * @param \App\Entity\Image $image
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function delete(Image $image)
    {
        $this->imageRepository->delete($image);
    }
}
