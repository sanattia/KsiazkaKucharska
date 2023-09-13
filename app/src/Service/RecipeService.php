<?php
/**
 * Recipe service.
 */

namespace App\Service;

use App\Entity\Recipe;
use App\Repository\RecipeRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * Class RecipeService.
 */
class RecipeService implements RecipeServiceInterface
{
    /**
     * Recipe repository.
     */
    private RecipeRepository $recipeRepository;

    /**
     * Paginator.
     */
    private PaginatorInterface $paginator;

    /**
     * Constructor.
     *
     * @param RecipeRepository   $recipeRepository Recipe repository
     * @param PaginatorInterface $paginator        Paginator
     */
    public function __construct(RecipeRepository $recipeRepository, PaginatorInterface $paginator)
    {
        $this->recipeRepository = $recipeRepository;
        $this->paginator = $paginator;
    }

    /**
     * Get paginated list.
     *
     * @param int $page Page number
     *
     * @return PaginationInterface<string, mixed> Paginated list
     */
    public function getPaginatedList(int $page): PaginationInterface
    {
        return $this->paginator->paginate(
            $this->recipeRepository->queryAll(),
            $page,
            RecipeRepository::PAGINATOR_ITEMS_PER_PAGE
        );
    }

    /**
     * Save entity.
     *
     * @param Recipe $recipe Recipe entity
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function save(Recipe $recipe): void
    {
        $this->recipeRepository->save($recipe);
    }// end save()

    /**
     * Delete entity.
     *
     * @param Recipe $recipe Recipe entity
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function delete(Recipe $recipe): void
    {
        $this->recipeRepository->delete($recipe);
    }// end delete()

    /**
     * Find recipe by Id.
     *
     * @param int $id Recipe Id
     *
     * @return Recipe|null Recipe entity
     */
    public function findOneById(int $id): ?Recipe
    {
        return $this->recipeRepository->findOneBy(['id' => $id]);
    }

    /**
     * Find recipe by category.
     *
     * @param array $category Category array
     *
     * @return Recipe[]
     */
    public function findBy(array $category): array
    {
        return $this->recipeRepository->findBy($category);
    }
}
