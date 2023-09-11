<?php
/**
 * Comment service.
 */

namespace App\Service;

use App\Entity\Comment;
use App\Repository\CommentRepository;
use Knp\Component\Pager\PaginatorInterface;

/**
 * Class CommentService.
 */
class CommentService
{
    /**
     * Comment repository.
     */
    private CommentRepository $commentRepository;

    /**
     * Paginator.
     */
    private PaginatorInterface $paginator;

    /**
     * CommentService constructor.
     *
     * @param \App\Repository\CommentRepository       $commentRepository Comment repository
     * @param \Knp\Component\Pager\PaginatorInterface $paginator         Paginator
     */
    public function __construct(CommentRepository $commentRepository, PaginatorInterface $paginator)
    {
        $this->commentRepository = $commentRepository;
        $this->paginator = $paginator;
    }

    /**
     * Save comment.
     *
     * @param Comment $comment Comment entity
     *
     */
    public function save(Comment $comment): void
    {
        $this->commentRepository->save($comment);
    }

    /**
     * Delete comment.
     *
     * @param Comment $comment Comment entity
     *
     */
    public function delete(Comment $comment): void
    {
        $this->commentRepository->delete($comment);
    }

    /**
     * Find comment by recipe.
     *
     * @param array $recipe Recipe array
     *
     * @return Comment[]
     */
    public function findBy(array $recipe): array
    {
        return $this->commentRepository->findBy($recipe);
    }
}
