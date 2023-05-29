<?php
/**
 * User Entity.
 */

namespace App\Entity;

use App\Entity\Enum\UserRole;
use App\Repository\UserRepository;
use DateTime;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 180, unique: true)]
    private ?string $email;

    #[ORM\Column(type: 'string', length: 180)]
    private ?string $username;

    #[ORM\Column(type: 'json')]
    private array $roles = [];

    #[ORM\Column(type: 'string')]
    private string $password;

    #[ORM\Column(type: 'datetime')]
    #[Assert\Type(type: '\DateTimeInterface')]
    private DateTimeInterface $createdAt;

    #[ORM\Column(type: 'datetime')]
    #[Assert\Type(type: '\DateTimeInterface')]
    private DateTimeInterface $updatedAt;


    /**
     * A non-persisted field that's used to create the encoded password.
     *
     * @var string
     */
    private $plainPassword;

    /**
     * Role user.
     *
     * @var string
     */
    public const ROLE_USER = 'ROLE_USER';

    /**
     * Role admin.
     *
     * @var string
     */
    public const ROLE_ADMIN = 'ROLE_ADMIN';

    /**
     * construct.
     */
    public function __construct()
    {
        $this->setUpdatedAt(new DateTime('now'));
        $this->setCreatedAt(new DateTime('now'));
    }

    /**
     * Getter for id.
     *
     * @return int|null Id
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Getter for email.
     *
     * @return string|null Email
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * Setter for email.
     *
     * @param string $email Email
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     *
     * @return string options string
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @return string options string
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }


    /**
     * Setter for the Username.
     *
     * @param string $username Username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * Getter for roles.
     *
     * @return array
     *
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param array $roles
     */
    public function setRoles(array $roles): void
    {
        $this->roles = $roles;
    }


    /**
     * Getter for password.
     *
     * @return string|null Password
     *
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * Setter for password.
     *
     * @param string options $password
     */
    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @return string|null options string
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * Removes sensitive information from the token.
     *
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * Getter for Create At.
     *
     * @return DateTimeInterface|null options datetime
     */
    public function getCreatedAt(): ?DateTimeInterface
    {
        return $this->createdAt;
    }

    /**
     * Setter for Create At.
     *
     * @param DateTimeInterface options $createdAt
     *
     * @return $this options this
     */
    public function setCreatedAt(DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Getter for Update At.
     *
     * @return DateTimeInterface|null options date time
     */
    public function getUpdatedAt(): ?DateTimeInterface
    {
        return $this->updatedAt;
    }

    /**
     * Setter for Update At.
     *
     * @param DateTimeInterface options $updatedAt
     *
     * @return $this
     */
    public function setUpdatedAt(DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return string|null
     */
    public function __toString()
    {
        return $this->email;
    }
}
