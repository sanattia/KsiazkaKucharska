<?php
/**
 * Comment Controller test.
 */

namespace Controller;

use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\Recipe;
use App\Entity\User;
use App\Repository\CategoryRepository;
use App\Repository\CommentRepository;
use App\Repository\RecipeRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Validator\Constraints\Date;

/**
 * Class CommentControllerTest.
 */
class CommentControllerTest extends WebTestCase
{
    /**
     * Test route.
     *
     * @const string
     */
    public const TEST_ROUTE = '/comment';

    /**
     * Test client.
     */
    private KernelBrowser $httpClient;

    /**
     * Set up tests.
     */
    public function setUp(): void
    {
        $this->httpClient = static::createClient();
    }

    /**
     * Test comment delete route.
     */
    public function testCommentDeleteRoute(): void
    {
        // given
        $user = null;
        try {
            $user = $this->createUser(['ROLE_USER', 'ROLE_ADMIN'], 'testDeleteComment@example.com'
            );
        } catch (OptimisticLockException|NotFoundExceptionInterface|ORMException|ContainerExceptionInterface) {
        }

        $this->httpClient->loginUser($user);

        $commentRepository = static::getContainer()->get(CommentRepository::class);
        $recipeRepository = static::getContainer()->get(RecipeRepository::class);
        $categoryRepository = static::getContainer()->get(CategoryRepository::class);

        $testCategory = new Category();
        $testCategory->setName('testCatComment');
        $testCategory->setCreatedAt(new \DateTime());
        $testCategory->setUpdatedAt(new \DateTime());
        $categoryRepository->save($testCategory);

        $testRecipe = new Recipe();
        $testRecipe->setAuthor($user);
        $testRecipe->setUpdatedAt(new \DateTime());
        $testRecipe->setCreatedAt(new \DateTime());
        $testRecipe->setTitle("testRecipeForComment");
        $testRecipe->setCategory($testCategory);
        $recipeRepository->save($testRecipe);

        $testComment = new Comment();
        $testComment->setContent('TestComment');
        $testComment->setAuthor($user);
        $testComment->setRecipe($testRecipe);
        $testComment->setCreatedAt(new \DateTime('now'));
        $testComment->setUpdatedAt(new \DateTime('now'));
        $commentRepository->save($testComment);


        $testCommentId = $testComment->getId();


        $this->httpClient->request('GET', self::TEST_ROUTE.'/'.$testCommentId.'/delete');

        // when
        $this->httpClient->submitForm(
            'Zapisz',
        );

        // then
        $this->assertNull($commentRepository->findOneById($testCommentId));
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