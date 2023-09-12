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



}// end class
