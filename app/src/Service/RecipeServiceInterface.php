<?php
/**
 * Recipe service interface.
 */

namespace App\Service;

use App\Entity\Recipe;
use Knp\Component\Pager\Pagination\PaginationInterface;

/**
 * Interface.
 */
interface RecipeServiceInterface
{
    /**
     * Get paginated list.
     * @param int $page
     *
     * @return PaginationInterface
     */
    public function getPaginatedList(int $page): PaginationInterface;

    /**
     * Save entity.
     * @param Recipe $recipe
     *
     * @return void
     */
    public function save(Recipe $recipe): void;

    /**
     * Delete recipe.
     * @param Recipe $recipe
     *
     * @return void
     */
    public function delete(Recipe $recipe): void;
}
