<?php
/**
 * Registration service.
 */

namespace App\Service;

use App\Entity\User;
use App\Entity\UsersData;
use App\Repository\UserRepository;
use App\Repository\UsersDataRepository;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class RegistrationService.
 */
class RegistrationService
{
    /**
     * Password encoder.
     */
    private UserPasswordEncoderInterface $passwordEncoder;

    /**
     * User repository.
     */
    private UserRepository $userRepository;


    /**
     * RegistrationService constructor.
     *
     * @param \App\Repository\UserRepository $userRepository  User repository
     * @param UserPasswordEncoderInterface   $passwordEncoder Password Encoder
     */
    public function __construct(UserRepository $userRepository, UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->userRepository = $userRepository;
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * Save user.
     *
     * @param User $user User entity
     *
     */
    public function save(User $user): void
    {
        $this->userRepository->save($user);
    }

    /**
     * Register.
     *
     * @param                       $data
     * @param User $user User entity
     *
     */
    public function register($data, User $user): void
    {
        $user->setEmail($data['email']);
        $user->setPassword(
            $this->passwordEncoder->encodePassword($user, $data['password'])
        );
        $user->setRoles(['ROLE_USER']);

        $this->userRepository->save($user);
    }
}
