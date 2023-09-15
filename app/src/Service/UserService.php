<?php
/**
 * User Service.
 */

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;
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
     * Save user.
     *
     * @param User $user User entity
     */
    public function save(User $user): void
    {
        $this->userRepository->save($user);
    }

    /**
     * Upgrade password.
     *
     * @param User   $user     User
     * @param string $password Password
     */
    public function upgradePassword(User $user, string $password): void
    {
        $this->userRepository->upgradePassword($user, $password);
    }
}// end class
