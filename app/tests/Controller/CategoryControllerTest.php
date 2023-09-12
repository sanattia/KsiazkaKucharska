<?php
/**
 * Category Controller test.
 */

namespace Controller;

use App\Entity\Category;
use App\Entity\Enum\UserRole;
use App\Entity\Recipe;
use App\Entity\User;
use App\Repository\CategoryRepository;
use App\Repository\UserRepository;
use Cassandra\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class CategoryControllerTest.
 */
class CategoryControllerTest extends WebTestCase
{
    /**
     * Test route.
     *
     * @const string
     */
    public const TEST_ROUTE = '/category';

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
     * Test index route for logged user.
     *
     * @throws ContainerExceptionInterface|NotFoundExceptionInterface|ORMException|OptimisticLockException|\Exception
     */
    public function testIndexRoute(): void
    {
        // given
        $expectedStatusCode = 200;
        $adminUser = $this->createUser([UserRole::ROLE_ADMIN->value, UserRole::ROLE_USER->value], 'test_category__admin@example.com');
        $this->httpClient->loginUser($adminUser);

        // when
        $this->httpClient->request('GET', self::TEST_ROUTE);
        $resultStatusCode = $this->httpClient->getResponse()->getStatusCode();

        // then
        $this->assertEquals($expectedStatusCode, $resultStatusCode);
    }

    /**
     * Test category show route.
     *
     * @dataProvider dataProviderForTestCategoryShow
     */
    public function testCategoryShowRoute(string $input, int $expectedCode): void
    {
        // given
        $expectedCategory = new Category();
        $expectedCategory->setName($input);
        $expectedCategory->setCreatedAt(new \DateTime());
        $expectedCategory->setUpdatedAt(new \DateTime());
        $categoryRepository = static::getContainer()->get(CategoryRepository::class);

        // when
        $categoryRepository->save($expectedCategory);

        // when
        $this->httpClient->request('GET', self::TEST_ROUTE.'/'.$expectedCategory->getId());
        $resultHttpStatusCode = $this->httpClient->getResponse()->getStatusCode();

        // then
        $this->assertEquals($expectedCode, $resultHttpStatusCode);
    }

    /**
     * Data provider for test category show.
     */
    public function dataProviderForTestCategoryShow(): \Generator
    {
        yield 'Status code' => [
            'input' => 'Test Category Controller Show',
            'expected' => 200,
        ];
    }

    /**
     * Test category edit route.
     */
    public function testCategoryEditRoute(): void
    {
        // given
        $user = null;
        try {
            $user = $this->createUser(
                ['ROLE_USER', 'ROLE_ADMIN'],
                'testEditCategory@example.com',
                'testCategoryEdit'
            );
        } catch (OptimisticLockException|NotFoundExceptionInterface|ORMException|ContainerExceptionInterface $e) {
        }

        $this->httpClient->loginUser($user);
        $categoryRepository = static::getContainer()->get(CategoryRepository::class);
        $testCategory = new Category();
        $editCategoryName = 'editCategoryName';
        $testCategory->setName($editCategoryName);
        $testCategory->setCreatedAt(new \DateTime('now'));
        $testCategory->setUpdatedAt(new \DateTime('now'));
        $categoryRepository->save($testCategory);
        $testCategoryId = $testCategory->getId();
        $this->httpClient->request('GET', self::TEST_ROUTE.'/'.$testCategoryId.'/edit');

        // when
        $this->httpClient->submitForm(
            'Zapisz',
            ['category' => ['name' => $editCategoryName]]
        );

        // then
        $savedCategory = $categoryRepository->findOneById($testCategoryId);
        $this->assertEquals($testCategoryId,
            $savedCategory->getId());

        $result = $this->httpClient->getResponse();
        $this->assertEquals(302, $result->getStatusCode());
    }

    /**
     * Test category create route.
     */
    public function testCategoryCreateRoute(): void
    {
        // given
        $user = null;
        try {
            $user = $this->createUser(
                ['ROLE_USER', 'ROLE_ADMIN'],
                'testCreateCat@example.com',
                'testCategoryCreate'
            );
        } catch (OptimisticLockException|NotFoundExceptionInterface|ORMException|ContainerExceptionInterface $e) {
        }

        $this->httpClient->loginUser($user);
        $categoryRepository = static::getContainer()->get(CategoryRepository::class);
        $this->httpClient->request('GET', self::TEST_ROUTE.'/create');
        $createCategoryName = 'createCategoryName';

        // when
        $this->httpClient->submitForm(
            'Zapisz',
            ['category' => ['name' => $createCategoryName]]
        );

        // then
        $savedCategory = $categoryRepository->findOneByName($createCategoryName);
        $this->assertEquals($createCategoryName,
            $savedCategory->getName());

        $result = $this->httpClient->getResponse();
        $this->assertEquals(302, $result->getStatusCode());
    }

    /**
     * Test category delete route.
     */
    public function testCategoryDeleteRoute(): void
    {
        // given
        $user = null;
        try {
            $user = $this->createUser(['ROLE_USER', 'ROLE_ADMIN'], 'testDeleteCat@example.com'
            );
        } catch (OptimisticLockException|NotFoundExceptionInterface|ORMException|ContainerExceptionInterface) {
        }

        $this->httpClient->loginUser($user);
        $categoryRepository = static::getContainer()->get(CategoryRepository::class);
        $testCategory = new Category();
        $testCategory->setName('CategoryToDelete');
        $testCategory->setCreatedAt(new \DateTime('now'));
        $testCategory->setUpdatedAt(new \DateTime('now'));
        $categoryRepository->save($testCategory);
        $testCategoryId = $testCategory->getId();


        $this->httpClient->request('GET', self::TEST_ROUTE.'/'.$testCategoryId.'/delete');

        // when
        $this->httpClient->submitForm(
            'Zapisz',
        );

        // then
        $this->assertNull($categoryRepository->findOneById($testCategoryId));
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