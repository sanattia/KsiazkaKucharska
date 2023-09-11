<?php
/**
 * Comment Entity.
 */

namespace App\Entity;

use App\Repository\CommentRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CommentRepository::class)]
#[ORM\Table(name: 'comments')]
class Comment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'datetime')]
    #[Assert\Type(type: '\DateTimeInterface')]
    private \DateTimeInterface $createdAt;

    #[ORM\Column(type: 'datetime')]
    #[Assert\Type(type: '\DateTimeInterface')]
    private \DateTimeInterface $updatedAt;

    #[ORM\ManyToOne(targetEntity: User::class, fetch: 'EXTRA_LAZY')]
    #[ORM\JoinColumn(nullable: false)]
    private $author;

    #[ORM\ManyToOne(targetEntity: Recipe::class, inversedBy: 'comments', fetch: 'EXTRA_LAZY')]
    #[ORM\JoinColumn(nullable: false)]
    private $recipe;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\Type(type: 'string')]
    #[Assert\Length(min: 3, max: 255)]
    private $content;

    /**
     * Getter for Id.
     *
     * @return int|null Result
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Getter for Created At.
     *
     * @return \DateTimeInterface|null Created at
     */
    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    /**
     * Setter for Created at.
     *
     * @param \DateTimeInterface $createdAt Created at
     */
    public function setCreatedAt(\DateTimeInterface $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    /**
     * Getter for Updated at.
     *
     * @return \DateTimeInterface|null Updated at
     */
    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    /**
     * Setter for Updated at.
     *
     * @param \DateTimeInterface $updatedAt Updated at
     */
    public function setUpdatedAt(\DateTimeInterface $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * Getter for author.
     *
     * @return \App\Entity\User|null User
     */
    public function getAuthor(): ?User
    {
        return $this->author;
    }

    /**
     * Setter for author.
     *
     * @param \App\Entity\User|null $author User
     *
     * @return \App\Entity\User|null User
     */
    public function setAuthor(?User $author): void
    {
        $this->author = $author;
    }

    /**
     * Getter for recipe.
     *
     * @return \App\Entity\Recipe|null Recipe
     */
    public function getRecipe(): ?Recipe
    {
        return $this->recipe;
    }

    /**
     * Setter for recipe.
     *
     * @param \App\Entity\Recipe|null $recipe Recipe
     *
     * @return \App\Entity\Recipe|null Recipe
     */
    public function setRecipe(?Recipe $recipe): void
    {
        $this->recipe = $recipe;
    }

    /**
     * Getter for Content.
     *
     * @return string|null Content
     */
    public function getContent(): ?string
    {
        return $this->content;
    }

    /**
     * Setter for Content.
     *
     * @param string $content Content
     *
     * @return string|null Content
     */
    public function setContent(string $content): void
    {
        $this->content = $content;
    }
}
