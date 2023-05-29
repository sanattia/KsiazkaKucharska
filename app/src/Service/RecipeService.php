<?php
/**
 * Recipe service.
 */

namespace App\Service;

use App\Entity\Recipe;
use App\Repository\RecipeRepository;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * Class RecipeService.
 */
class RecipeService
{
    /**
     * Recipe repository.
     *
     * @var \App\Repository\RecipeRepository
     */
    private RecipeRepository $recipeRepository;

    /**
     * Paginator.
     *
     * @var \Knp\Component\Pager\PaginatorInterface
     */
    private PaginatorInterface $paginator;

    /**
     * RecipeService constructor.
     *
     * @param \App\Repository\RecipeRepository          $recipeRepository Recipe repository
     * @param \Knp\Component\Pager\PaginatorInterface $paginator      Paginator
     */
    public function __construct(RecipeRepository $recipeRepository, PaginatorInterface $paginator)
    {
        $this->recipeRepository = $recipeRepository;
        $this->paginator = $paginator;
    }

    /**
     * Create paginated list.
     *
     * @param int $page Page number
     *
     * @return \Knp\Component\Pager\Pagination\PaginationInterface Paginated list
     */
    public function createPaginatedList(int $page): PaginationInterface
    {
        return $this->paginator->paginate(
            $this->recipeRepository->queryAll(),
            $page,
            RecipeRepository::PAGINATOR_ITEMS_PER_PAGE
        );
    }

    /**
     * Save recipe.
     *
     * @param \App\Entity\Recipe $recipe Recipe entity
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function save(Recipe $recipe): void
    {
        $this->recipeRepository->save($recipe);
    }

    /**
     * Delete recipe.
     *
     * @param \App\Entity\Recipe $recipe Recipe entity
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function delete(Recipe $recipe): void
    {
        $this->recipeRepository->delete($recipe);
    }

    /**
     * Find recipe by Id.
     *
     * @param int $id Recipe Id
     *
     * @return \App\Entity\Recipe|null Recipe entity
     */
    public function findOneById(int $id): ?Recipe
    {
        return $this->recipeRepository->findOneById($id);
    }

    /**
     * Find recipe by category
     * @param array $category
     *
     * @return Recipe[]
     */
    public function findBy(array $category): array
    {
        return $this->recipeRepository->findBy($category);
    }
}
