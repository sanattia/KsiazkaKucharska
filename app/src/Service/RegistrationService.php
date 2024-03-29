<?php
/**
 * Registration service.
 */

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Class RegistrationService.
 */
class RegistrationService implements RegistrationServiceInterface
{
    /**
     * Password encoder.
     */
    private UserPasswordHasherInterface $passwordHasher;

    /**
     * User repository.
     */
    private UserRepository $userRepository;

    /**
     * RegistrationService constructor.
     *
     * @param UserRepository              $userRepository User repository
     * @param UserPasswordHasherInterface $passwordHasher Password Hasher
     */
    public function __construct(UserRepository $userRepository, UserPasswordHasherInterface $passwordHasher)
    {
        $this->userRepository = $userRepository;
        $this->passwordHasher = $passwordHasher;
    }

    /**
     * Register a user
     *
     * @param array $data An array of user registration data (e.g., ['email' => 'user@example.com', 'password' => 'password'])
     * @param User  $user The User entity to be registered
     */
    public function register(array $data, User $user): void
    {
        $user->setEmail($data['email']);
        $user->setPassword(
            $this->passwordHasher->hashPassword($user, $data['password'])
        );
        $user->setRoles(['ROLE_USER']);

        $this->userRepository->save($user);
    }
}
