<?php
/**
 * Comment Service Interface.
 */

namespace App\Service;

use App\Entity\Comment;
use Knp\Component\Pager\Pagination\PaginationInterface;

/**
 * Interface CommentServiceInterface.
 */
interface CommentServiceInterface
{
    /**
     * Save entity.
     *
     * @param Comment $comment Comment entity
     */
    public function save(Comment $comment): void;

    /**
     * Delete entity.
     *
     * @param Comment $comment Comment entity
     */
    public function delete(Comment $comment): void;


    /**
     * Find comment by recipe.
     *
     * @param array $recipe Recipe entity
     *
     * @return array<string, mixed> Result
     */
    public function findBy(array $recipe): array;
}// end interface
