<?php
/**
 * Recipe service tests.
 */

namespace App\Tests\Service;

use App\Entity\Category;
use App\Entity\Recipe;
use App\Service\RecipeService;
use App\Service\RecipeServiceInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Class RecipeServiceTest.
 */
class RecipeServiceTest extends KernelTestCase
{
    /**
     * Recipe repository.
     */
    private ?EntityManagerInterface $entityManager;

    /**
     * Recipe service.
     */
    private ?RecipeServiceInterface $recipeService;

    /**
     * Set up test.
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function setUp(): void
    {
        $container = static::getContainer();
        $this->entityManager = $container->get('doctrine.orm.entity_manager');
        $this->recipeService = $container->get(RecipeService::class);
    }

    /**
     * Test save.
     *
     * @throws ORMException
     */
    public function testSave(): void
    {
        // given
        $category = new Category();
        $category->setName('Test Category');
        $this->entityManager->persist($category);
        $this->entityManager->flush();

        $expectedRecipe = new Recipe();
        $expectedRecipe->setTitle('Test Recipe');
        $expectedRecipe->setCategory($category);

        // when
        $this->recipeService->save($expectedRecipe);

        // then
        $expectedRecipeId = $expectedRecipe->getId();
        $resultRecipe = $this->entityManager->createQueryBuilder()
            ->select('recipe')
            ->from(Recipe::class, 'recipe')
            ->where('recipe.id = :id')
            ->setParameter(':id', $expectedRecipeId, Types::INTEGER)
            ->getQuery()
            ->getSingleResult();

        $this->assertEquals($expectedRecipe, $resultRecipe);
    }

    /**
     * Test delete.
     *
     * @throws OptimisticLockException|ORMException
     */
    public function testDelete(): void
    {
        // given
        $category = new Category();
        $category->setName('Test Category');
        $this->entityManager->persist($category);
        $this->entityManager->flush();

        $recipeToDelete = new Recipe();
        $recipeToDelete->setTitle('Test Recipe');
        $recipeToDelete->setCategory($category);
        $this->entityManager->persist($recipeToDelete);
        $this->entityManager->flush();
        $deletedRecipeId = $recipeToDelete->getId();

        // when
        $this->recipeService->delete($recipeToDelete);

        // then
        $resultRecipe = $this->entityManager->createQueryBuilder()
            ->select('recipe')
            ->from(Recipe::class, 'recipe')
            ->where('recipe.id = :id')
            ->setParameter(':id', $deletedRecipeId, Types::INTEGER)
            ->getQuery()
            ->getOneOrNullResult();

        $this->assertNull($resultRecipe);
    }

    /**
     * Test get by id.
     *
     * @throws ORMException
     */
    public function testGetById(): void
    {
        // given
        $category = new Category();
        $category->setName('Test Category');
        $this->entityManager->persist($category);
        $this->entityManager->flush();

        $expectedRecipe = new Recipe();
        $expectedRecipe->setTitle('Test Recipe');
        $expectedRecipe->setCategory($category);
        $this->entityManager->persist($expectedRecipe);
        $this->entityManager->flush();
        $expectedRecipeId = $expectedRecipe->getId();

        // when
        $resultRecipe = $this->recipeService->getById($expectedRecipeId);

        // then
        $this->assertEquals($expectedRecipe, $resultRecipe);
    }

    /**
     * Test pagination empty list.
     */
    public function testGetPaginatedList(): void
    {
        // given
        $category = new Category();
        $category->setTitle('Test Category');
        $this->entityManager->persist($category);
        $this->entityManager->flush();

        $page = 1;
        $dataSetSize = 3;
        $expectedResultSize = 3;

        $counter = 0;
        while ($counter < $dataSetSize) {
            $recipe = new Recipe();
            $recipe->setTitle('Test Recipe #'.$counter);
            $recipe->setIngredients('Ingredients');
            $recipe->setDescription('Description');
            $recipe->setCategory($category);
            $this->recipeService->save($recipe);

            ++$counter;
        }

        $filters = array(
            'category_id' => $category->getId()
        );

        // when
        $result = $this->recipeService->getPaginatedList($page, $filters);

        // then
        $this->assertEquals($expectedResultSize, $result->count());
    }

    /**
     * Test prepare filters.
     */
    public function testPrepareFilters(): void
    {
        // given
        $category = new Category();
        $category->setTitle('Test Category');
        $this->entityManager->persist($category);
        $this->entityManager->flush();
        $categoryId = $category->getId();

        $filters = array(
            'category_id' => $categoryId,
        );

        // when
        $result = $this->recipeService->prepareFilters($filters);

        // then
        $this->assertEquals($result, array('category' => $category));
    }
}