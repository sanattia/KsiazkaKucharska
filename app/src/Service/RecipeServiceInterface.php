<?php
/**
 * Recipe service interface.
 */

namespace App\Service;

use App\Entity\Recipe;
use Knp\Component\Pager\Pagination\PaginationInterface;

/**
 * Recipe Service Interface.
 */
interface RecipeServiceInterface
{
    /**
     * Get paginated list.
     *
     * @param int $page Page number
     *
     * @return PaginationInterface<string, mixed> Paginated list
     */
    public function getPaginatedList(int $page): PaginationInterface;

    /**
     * Find entity by ID.
     *
     * @param int $id Entity ID
     *
     * @return Recipe|null Recipe entity
     */
    public function findOneById(int $id): ?Recipe;

    /**
     * Find recipe by category.
     *
     * @param array $category Category array
     *
     * @return Recipe[]
     */
    public function findBy(array $category): array;

    /**
     * Save entity.
     *
     * @param Recipe $recipe Recipe entity
     */
    public function save(Recipe $recipe): void;

    /**
     * Delete entity.
     *
     * @param Recipe $recipe Recipe entity
     */
    public function delete(Recipe $recipe): void;
}
