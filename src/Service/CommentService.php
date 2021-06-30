<?php
/**
 * CommentService
 */

namespace App\Service;

use App\Entity\Comment;
use App\Repository\CommentRepository;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * Class CommentService
 */
class CommentService
{
    const PAGINATOR_ITEMS_PER_PAGE = 10;

    /** @var CommentRepository */
    private $commentRepository;
    /** @var PaginatorInterface */
    private $paginator;

    /**
     * CommentService constructor.
     * @param \App\Repository\CommentRepository         $commentRepository
     * @param \Knp\Component\Pager\PaginatorInterface $paginator
     */
    public function __construct(CommentRepository $commentRepository, PaginatorInterface $paginator)
    {
        $this->commentRepository = $commentRepository;
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
            $this->commentRepository->queryAll($filters),
            $page,
            self::PAGINATOR_ITEMS_PER_PAGE
        );
    }

    /**
     * Zapisuje do bazy
     * @param \App\Entity\Comment $comment
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function save(Comment $comment)
    {
        $this->commentRepository->save($comment);
    }

    /**
     * Usuwa z bazy
     * @param \App\Entity\Comment $comment
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function delete(Comment $comment)
    {
        $this->commentRepository->delete($comment);
    }
}
