<?php
/**
 * AlbumService
 */

namespace App\Service;

use App\Entity\Album;
use App\Repository\AlbumRepository;
use App\Repository\ImageRepository;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * Class AlbumService
 */
class AlbumService
{
    const PAGINATOR_ITEMS_PER_PAGE = 10;

    /** @var AlbumRepository */
    private $albumRepository;
    /** @var PaginatorInterface */
    private $paginator;
    /** @var ImageRepository */
    private $imageRepository;

    /**
     * AlbumService constructor.
     * @param \App\Repository\AlbumRepository         $albumRepository
     * @param \Knp\Component\Pager\PaginatorInterface $paginator
     * @param \App\Repository\ImageRepository         $imageRepository
     */
    public function __construct(AlbumRepository $albumRepository, PaginatorInterface $paginator, ImageRepository $imageRepository)
    {
        $this->albumRepository = $albumRepository;
        $this->paginator = $paginator;
        $this->imageRepository = $imageRepository;
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
            $this->albumRepository->queryAll($filters),
            $page,
            self::PAGINATOR_ITEMS_PER_PAGE
        );
    }

    /**
     * Zapisuje do bazy
     * @param \App\Entity\Album $album
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function save(Album $album)
    {
        $this->albumRepository->save($album);
    }

    /**
     * Usuwa z bazy
     * @param \App\Entity\Album $album
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function delete(Album $album)
    {
        if ($cover = $album->getCover()) {
            $this->imageRepository->delete($cover);
        }
        $this->albumRepository->delete($album);
    }
}
