<?php
/**
 * Comment service tests.
 */

namespace Service;

use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\Enum\UserRole;
use App\Entity\Recipe;
use App\Entity\User;
use App\Repository\CommentRepository;
use App\Repository\UserRepository;
use App\Service\CommentService;
use App\Service\CommentServiceInterface;
use DateTime;
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
        $this->commentRepository = $container->get(commentRepository::class);
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

        $expectedComment = new Comment();
        $createdAt = new DateTime();  // Current date and time
        $updatedAt = new DateTime();  // Current date and time
        try {
            $testUser = $this->createUser([UserRole::ROLE_ADMIN->value, UserRole::ROLE_USER->value], 'test_comment_save_admin@example.com');
            $this->entityManager->persist($testUser);
        } catch (OptimisticLockException|ORMException|NotFoundExceptionInterface|ContainerExceptionInterface $e) {
        }
        $expectedComment->setAuthor($testUser);
        $expectedComment->setCreatedAt($createdAt);
        $expectedComment->setUpdatedAt($updatedAt);
        $expectedComment->setRecipe($expectedRecipe);
        $expectedComment->setContent('test');
        $this->entityManager->persist($expectedComment);
        $this->entityManager->flush();

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
     * @throws ORMException
     */
    public function testDelete(): void
    {
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

        $commentToDelete = new Comment();
        $createdAt = new DateTime();  // Current date and time
        $updatedAt = new DateTime();  // Current date and time
        try {
            $testUser = $this->createUser([UserRole::ROLE_ADMIN->value, UserRole::ROLE_USER->value], 'test_comment_delete_admin@example.com');
            $this->entityManager->persist($testUser);
        } catch (OptimisticLockException|ORMException|NotFoundExceptionInterface|ContainerExceptionInterface $e) {
        }
        $commentToDelete->setAuthor($testUser);
        $commentToDelete->setCreatedAt($createdAt);
        $commentToDelete->setUpdatedAt($updatedAt);
        $commentToDelete->setRecipe($expectedRecipe);
        $commentToDelete->setContent('test');
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
     * Test find comment by category.
     *
     * @throws ORMException
     */
    public function testFindBy(): void
    {
        //given

        $createdAt = new DateTime();  // Current date and time
        $updatedAt = new DateTime();  // Current date and time

        $category = new Category();
        $category->setName('Test Category');
        $category->setCreatedAt($createdAt);
        $category->setUpdatedAt($updatedAt);
        $this->entityManager->persist($category);

        $recipe = new Recipe();
        $recipe->setTitle('Test Recipe');
        $recipe->setCategory($category);
        $recipe->setCreatedAt($createdAt);
        $recipe->setUpdatedAt($updatedAt);
        $this->entityManager->persist($recipe);

        try {
            $testUser = $this->createUser([UserRole::ROLE_ADMIN->value, UserRole::ROLE_USER->value], 'test_comment_findby__admin@example.com');
            $this->entityManager->persist($testUser);
        } catch (OptimisticLockException|NotFoundExceptionInterface|ContainerExceptionInterface|ORMException $e) {
        }


        $expectedComment1 = new Comment();
        $expectedComment1->setAuthor($testUser);
        $expectedComment1->setCreatedAt($createdAt);
        $expectedComment1->setUpdatedAt($updatedAt);
        $expectedComment1->setRecipe($recipe);
        $expectedComment1->setContent('Test');
        $this->entityManager->persist($expectedComment1);

        $expectedComment2 = new Comment();
        $expectedComment2->setAuthor($testUser);
        $expectedComment2->setCreatedAt($createdAt);
        $expectedComment2->setUpdatedAt($updatedAt);
        $expectedComment2->setRecipe($recipe);
        $expectedComment2->setContent('Testo');
        $this->entityManager->persist($expectedComment2);

        $this->entityManager->flush();

        // when
        $resultComments = $this->commentService->findBy(['recipe' => $recipe]);

        // then
        $this->assertCount(2, $resultComments);
        $this->assertEquals($expectedComment1, $resultComments[0]);
        $this->assertEquals($expectedComment2, $resultComments[1]);
    }


    /**
     * Create user.
     *
     * @param array $roles User roles
     *
     * @return User User entity
     *
     * @throws ContainerExceptionInterface|NotFoundExceptionInterface|ORMException|OptimisticLockException
     */
    private function createUser(array $roles, $email): User
    {
        $passwordHasher = static::getContainer()->get('security.password_hasher');
        $user = new User();
        $user->setEmail($email);
        $user->setRoles($roles);
        $user->setPassword(
            $passwordHasher->hashPassword(
                $user,
                'p@55w0rd'
            )
        );
        $userRepository = static::getContainer()->get(UserRepository::class);
        $userRepository->save($user, true);

        return $user;
    }
}