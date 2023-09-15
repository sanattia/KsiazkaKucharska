<?php
/**
 * Registration Service Interface.
 */

namespace App\Service;

use App\Entity\User;

/**
 * Interface RegistrationServiceInterface.
 */
interface RegistrationServiceInterface
{
    /**
     * Register.
     *
     * @param array $data An array of user registration data
     * @param User  $user User entity
     */
    public function register(array $data, User $user): void;
}// end interface
