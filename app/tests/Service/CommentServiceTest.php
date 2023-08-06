<?php
/**
 * Comment service tests.
 */

namespace Service;

use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\Recipe;
use App\Service\CommentService;
use App\Service\CommentServiceInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Class CommentServiceTest.
 */
class CommentServiceTest extends KernelTestCase
{
    /**
     * Comment repository.
     */
    private ?EntityManagerInterface $entityManager;

    /**
     * Comment service.
     */
    private ?CommentServiceInterface $commentService;

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
        $this->commentService = $container->get(CommentService::class);
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
        $category->setTitle('category');
        $this->entityManager->persist($category);
        $this->entityManager->flush();

        $recipe = new Recipe();
        $recipe->setTitle('Test Comment');
        $recipe->setIngredients('Ingredients');
        $recipe->setDescription('Description');
        $recipe->setCategory($category);
        $this->entityManager->persist($recipe);
        $this->entityManager->flush();

        $expectedComment = new Comment();
        $expectedComment->setContent('content');
        $expectedComment->setEmail('email');
        $expectedComment->setNick('nick');
        $expectedComment->setRecipe($recipe);

        // when
        $this->commentService->save($expectedComment);

        // then
        $expectedCommentId = $expectedComment->getId();
        $resultComment = $this->entityManager->createQueryBuilder()
            ->select('comment')
            ->from(Comment::class, 'comment')
            ->where('comment.id = :id')
            ->setParameter(':id', $expectedCommentId, Types::INTEGER)
            ->getQuery()
            ->getSingleResult();

        $this->assertEquals($expectedComment, $resultComment);
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
        $category->setTitle('category');
        $this->entityManager->persist($category);
        $this->entityManager->flush();

        $recipe = new Recipe();
        $recipe->setTitle('Test Comment');
        $recipe->setIngredients('Ingredients');
        $recipe->setDescription('Description');
        $recipe->setCategory($category);
        $this->entityManager->persist($recipe);
        $this->entityManager->flush();

        $commentToDelete = new Comment();
        $commentToDelete->setContent('content');
        $commentToDelete->setEmail('email');
        $commentToDelete->setNick('nick');
        $commentToDelete->setRecipe($recipe);
        $this->entityManager->persist($commentToDelete);
        $this->entityManager->flush();
        $deletedCommentId = $commentToDelete->getId();

        // when
        $this->commentService->delete($commentToDelete);

        // then
        $resultComment = $this->entityManager->createQueryBuilder()
            ->select('comment')
            ->from(Comment::class, 'comment')
            ->where('comment.id = :id')
            ->setParameter(':id', $deletedCommentId, Types::INTEGER)
            ->getQuery()
            ->getOneOrNullResult();

        $this->assertNull($resultComment);
    }

    /**
     * Test pagination empty list.
     */
    public function testGetPaginatedList(): void
    {
        // given
        $category = new Category();
        $category->setTitle('category');
        $this->entityManager->persist($category);
        $this->entityManager->flush();

        $recipe = new Recipe();
        $recipe->setTitle('Test Comment');
        $recipe->setIngredients('Ingredients');
        $recipe->setDescription('Description');
        $recipe->setCategory($category);
        $this->entityManager->persist($recipe);
        $this->entityManager->flush();

        $page = 1;
        $dataSetSize = 3;
        $expectedResultSize = 3;

        $counter = 0;
        while ($counter < $dataSetSize) {
            $comment = new Comment();
            $comment->setContent('content');
            $comment->setEmail('email');
            $comment->setNick('nick');
            $comment->setRecipe($recipe);
            $this->commentService->save($comment);

            ++$counter;
        }

        // when
        $result = $this->commentService->getPaginatedList($page);

        // then
        $this->assertEquals($expectedResultSize, $result->count());
    }
}