<?php
/**
 * Recipe service tests.
 */

namespace App\Tests\Service;

use App\Entity\Recipe;
use App\Entity\Category;
use DateTime;
use App\Service\RecipeService;
use App\Service\RecipeServiceInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\EntityManagerInterface;
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
        $expectedRecipe = new Recipe();
        $createdAt = new DateTime();  // Current date and time
        $updatedAt = new DateTime();  // Current date and time
        $expectedRecipe->setTitle('Test_Recipe');
        $category = new Category();
        $category->setName('Test Category for Recipe');
        $createdAt = new DateTime();  // Current date and time
        $updatedAt = new DateTime();  // Current date and time
        $category->setCreatedAt($createdAt);
        $category->setUpdatedAt($updatedAt);
        $this->entityManager->persist($category);
        $expectedRecipe->setCategory($category);
        $expectedRecipe->setCreatedAt($createdAt);
        $expectedRecipe->setUpdatedAt($updatedAt);

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
     * @throws ORMException
     */
    public function testDelete(): void
    {
        // given
        $createdAt = new DateTime();  // Current date and time
        $updatedAt = new DateTime();  // Current date and time
        $recipeToDelete = new Recipe();
        $recipeToDelete->setTitle('Test Recipe');
        $category = new Category();
        $category->setName('Test Category for Recipe #');
        $createdAt = new DateTime();  // Current date and time
        $updatedAt = new DateTime();  // Current date and time
        $category->setCreatedAt($createdAt);
        $category->setUpdatedAt($updatedAt);
        $this->entityManager->persist($category);
        $recipeToDelete->setCategory($category);
        $recipeToDelete->setCreatedAt($createdAt);
        $recipeToDelete->setUpdatedAt($updatedAt);
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
     * Test find by id.
     *
     * @throws ORMException
     */
    public function testFindById(): void
    {

        $createdAt = new DateTime();  // Current date and time
        $updatedAt = new DateTime();  // Current date and time

        // given
        $expectedRecipe = new Recipe();
        $expectedRecipe->setTitle('Test Recipe');
        $category = new Category();
        $category->setName('Test Category for Recipe #');
        $createdAt = new DateTime();  // Current date and time
        $updatedAt = new DateTime();  // Current date and time
        $category->setCreatedAt($createdAt);
        $category->setUpdatedAt($updatedAt);
        $this->entityManager->persist($category);
        $expectedRecipe->setCategory($category);
        $expectedRecipe->setCreatedAt($createdAt);
        $expectedRecipe->setUpdatedAt($updatedAt);

        $this->entityManager->persist($expectedRecipe);
        $this->entityManager->flush();
        $expectedRecipeId = $expectedRecipe->getId();

        // when
        $resultRecipe = $this->recipeService->findOneById($expectedRecipeId);

        // then
        $this->assertEquals($expectedRecipe, $resultRecipe);
    }

    /**
     * Test pagination empty list.
     */
    public function testGetPaginatedList(): void
    {
        // given
        $page = 1;
        $dataSetSize = 10;
        $expectedResultSize = 10;
        $createdAt = new \DateTime('2022-03-28');
        $updatedAt = new \DateTime('2022-04-29');

        $counter = 0;
        while ($counter < $dataSetSize) {
            $recipe = new Recipe();
            $recipe->setTitle('Test Recipe #'.$counter);
            $category = new Category();
            $category->setName('Test Category for Recipe #' . $counter);
            $createdAt = new DateTime();  // Current date and time
            $updatedAt = new DateTime();  // Current date and time
            $category->setCreatedAt($createdAt);
            $category->setUpdatedAt($updatedAt);
            $this->entityManager->persist($category);
            $recipe->setCategory($category);

            $recipe->setCreatedAt($createdAt);
            $recipe->setUpdatedAt($updatedAt);
            $this->recipeService->save($recipe);

            ++$counter;
        }

        // when
        $result = $this->recipeService->getPaginatedList($page);

        // then
        $this->assertEquals($expectedResultSize, $result->count());
    }

    // other tests for paginated list
}