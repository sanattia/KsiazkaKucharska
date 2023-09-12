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
     * @param                       $data
     * @param User $user User entity
     */
    public function register($data, User $user): void;

}// end interface
