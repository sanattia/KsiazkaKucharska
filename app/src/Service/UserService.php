<?php
/**
 * User Service.
 */

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Class UserService.
 */
class UserService implements UserServiceInterface
{
    /**
     * User repository.
     */
    private UserRepository $userRepository;

    /**
     * Paginator.
     */
    private PaginatorInterface $paginator;

    /**
     * Recipe service.
     */
    private RecipeServiceInterface $recipeService;

    /**
     * Category service.
     */
    private CategoryServiceInterface $categoryService;

    /**
     * Report service.
     */
    private ReportServiceInterface $reportService;

    /**
     * UserService constructor.
     *
     * @param UserRepository              $userRepository  User repository
     * @param UserPasswordHasherInterface $passwordHasher  Password hasher
     * @param PaginatorInterface          $paginator       Paginator
     * @param CategoryServiceInterface    $categoryService Category service
     * @param RecipeServiceInterface      $recipeService   Recipe service
     */
    public function __construct(UserRepository $userRepository, UserPasswordHasherInterface $passwordHasher, PaginatorInterface $paginator, CategoryServiceInterface $categoryService, RecipeServiceInterface $recipeService)
    {
        $this->userRepository = $userRepository;
        $this->paginator = $paginator;
        $this->passwordHasher = $passwordHasher;
        $this->recipeService = $recipeService;
        $this->categoryService = $categoryService;
    }// end __construct()

    /**
     * Create paginated list.
     *
     * @param int $page Page number
     *
     * @return PaginationInterface<string, mixed> Paginated list
     */
    public function createPaginationList(int $page): PaginationInterface
    {
        return $this->paginator->paginate(
            $this->userRepository->queryAll(),
            $page,
            UserRepository::PAGINATOR_ITEMS_PER_PAGE
        );
    }// end createPaginationList()

    /**
     * Save entity.
     *
     * @param User   $user     User entity
     * @param string $password Password
     */
    public function save(User $user, string $password): void
    {
        // encode the plain password
        $user->setPassword(
            $this->passwordHasher->hashPassword(
                $user,
                $password
            )
        );

        $user->setRoles(['ROLE_USER']);

        $this->userRepository->save($user, true);
    }// end save()

    /**
     * Delete entity.
     *
     * @param User $user User entity
     */
    public function delete(User $user): void
    {
        $reports = $this->reportService->findByUser($user);
        foreach ($reports as $report) {
            $this->reportService->delete($report);
        }

        $recipes = $this->recipeService->findByUser($user);
        foreach ($recipes as $recipe) {
            $this->recipeService->delete($recipe);
        }

        $categories = $this->categoryService->findByUser($user);
        foreach ($categories as $category) {
            $this->categoryService->delete($category);
        }

        $this->userRepository->remove($user, true);
    }// end delete()

    /**
     * Edit password.
     *
     * @param User   $user     User entity
     * @param string $password Password
     */
    public function upgradePassword(User $user, string $password): void
    {
        // encode the plain password
        $password = $this->passwordHasher->hashPassword(
            $user,
            $password
        );

        $this->userRepository->upgradePassword($user, $password);
    }// end upgradePassword()

    /**
     * Edit data.
     *
     * @param User $user User entity
     */
    public function editData(User $user): void
    {
        $this->userRepository->save($user, true);
    }// end editData()

    /**
     * Edit role.
     *
     * @param User $user User entity
     */
    public function editRole(User $user): void
    {
        // count admins
        $admins = $this->userRepository->queryByRole('ROLE_ADMIN')->getQuery()->getResult();
        $adminsCount = count($admins);

        // if there is only one admin, do not change role
        if ($adminsCount < 2 && !in_array('ROLE_ADMIN', $user->getRoles(), true)) {
            return;
        }

        $this->userRepository->save($user, true);
    }// end editRole()
}// end class
