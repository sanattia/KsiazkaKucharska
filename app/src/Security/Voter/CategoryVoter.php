<?php

/**
 * Category voter.
 */

namespace App\Security\Voter;

use App\Entity\Category;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

/**
 * Class CategoryVoter.
 */
class CategoryVoter extends Voter
{
    /**
     * Edit permission.
     *
     * @const string
     */
    public const EDIT = 'EDIT';

    /**
     * Delete permission.
     *
     * @const string
     */
    public const DELETE = 'DELETE';

    /**
     * Create permission.
     *
     * @const string
     */
    public const CREATE = 'CREATE';

    /**
     * Security helper.
     */
    private Security $security;

    /**
     * OrderVoter constructor.
     *
     * @param Security $security Security helper
     */
    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    /**
     * Determines if the attribute and subject are supported by this voter.
     *
     * @param string $attribute An attribute
     * @param mixed  $subject   The subject to secure, e.g. an object the user wants to access or any other PHP type
     *
     * @return bool Result
     */
    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::EDIT, self::DELETE, self::CREATE])
            && $subject instanceof Category;
    }

    /**
     * Perform a single access check operation on a given attribute, subject and token.
     * It is safe to assume that $attribute and $subject already passed the "supports()" method check.
     *
     * @param string         $attribute Permission name
     * @param mixed          $subject   Object
     * @param TokenInterface $token     Security token
     *
     * @return bool Vote result
     */
    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        switch ($attribute) {
            case self::EDIT:
                return $this->canEdit();
            case self::DELETE:
                return $this->canDelete();
            case self::CREATE:
                return $this->canCreate();
        }

        return false;
    }

    /**
     * Checks if user can edit category.
     *
     * @return bool Result
     */
    private function canEdit(): bool
    {
        return $this->security->isGranted('ROLE_ADMIN');
    }

    /**
     * Checks if user can delete category.
     *
     * @return bool Result
     */
    private function canDelete(): bool
    {
        return $this->security->isGranted('ROLE_ADMIN');
    }

    /**
     * Checks if user can create category.
     *
     * @return bool Result
     */
    private function canCreate(): bool
    {
        return $this->security->isGranted('ROLE_ADMIN');
    }
}
