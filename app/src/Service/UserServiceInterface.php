<?php
/**
 * User Service Interface.
 */

namespace App\Service;

use App\Entity\User;

/**
 * Class UserServiceInterface.
 */
interface UserServiceInterface
{
    /**
     * Save entity.
     *
     * @param User $user User entity
     */
    public function save(User $user): void;

    /**
     * Upgrade password.
     *
     * @param User   $user     User
     * @param string $password Password
     */
    public function upgradePassword(User $user, string $password): void;
}// end interface
