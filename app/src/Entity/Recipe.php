<?php
/**
 * Recipe Entity.
 */

namespace App\Entity;

use App\Repository\RecipeRepository;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Recipe.
 */
#[ORM\Entity(repositoryClass: RecipeRepository::class)]
#[ORM\Table(name: 'recipes')]
#[UniqueEntity(fields: ['title'])]
class Recipe
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\Type(type: 'string')]
    #[Assert\Length(min: '3', max: '64')]
    private $title;

    #[ORM\ManyToOne(targetEntity: Category::class, inversedBy: 'recipes')]
    #[ORM\JoinColumn(nullable: false)]
    private $category;

    #[ORM\Column(type: 'datetime')]
    #[Assert\Type(type: "\DateTimeInterface")]
    private $createdAt;

    #[ORM\Column(type: 'datetime')]
    #[Assert\Type(type: "\DateTimeInterface")]
    private $updatedAt;

    #[ORM\ManyToMany(targetEntity: Tag::class, inversedBy: 'recipes')]
    #[ORM\JoinTable(name: 'recipes_tags')]
    private $tags;

    #[ORM\OneToMany(mappedBy: 'recipe', targetEntity: Comment::class, orphanRemoval: true)]
    private $comments;

    #[ORM\Column(type: Types::TIME_MUTABLE, nullable: true)]
    private ?DateTimeInterface $time = null;

    #[ORM\Column(length: 125, nullable: true)]
    private ?string $difficulty = null;

    #[ORM\Column(nullable: true)]
    private ?int $portion = null;

    #[ORM\Column(nullable: true)]
    private ?int $calories = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $content = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    private ?User $author;

    /**
     * Recipe constructor.
     */
    public function __construct()
    {
        $this->tags = new ArrayCollection();
        $this->comments = new ArrayCollection();
    }

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
     * Getter for Title.
     *
     * @return string|null Title
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * Setter for Title.
     *
     * @param string $title Title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * Getter for Author.
     *
     * @return User|null User entity
     */
    public function getAuthor(): ?User
    {
        return $this->author;
    }

    /**
     * Setter for Author.
     *
     * @param User|null $author
     *
     * @return Recipe
     */
    public function setAuthor(?User $author): self
    {
        $this->author = $author;

        return $this;
    }

    /**
     * Getter for category.
     *
     * @return Category|null Category
     */
    public function getCategory(): ?Category
    {
        return $this->category;
    }

    /**
     * Setter for category.
     *
     * @param Category|null $category Category
     *
     * @return Recipe Category
     */
    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Getter for Created At.
     *
     * @return DateTimeInterface|null Created at
     */
    public function getCreatedAt(): ?DateTimeInterface
    {
        return $this->createdAt;
    }

    /**
     * Setter for Created at.
     *
     * @param DateTimeInterface $createdAt Created at
     */
    public function setCreatedAt(DateTimeInterface $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    /**
     * Getter for Updated at.
     *
     * @return DateTimeInterface|null Updated at
     */
    public function getUpdatedAt(): ?DateTimeInterface
    {
        return $this->updatedAt;
    }

    /**
     * Setter for Updated at.
     *
     * @param DateTimeInterface $updatedAt Updated at
     */
    public function setUpdatedAt(DateTimeInterface $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * Getter for tags.
     *
     * @return Collection Tags collection
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    /**
     * Add tag to collection.
     *
     * @param Tag $tag Tag entity
     */
    public function addTag(Tag $tag): void
    {
        if (!$this->tags->contains($tag)) {
            $this->tags[] = $tag;
        }
    }

    /**
     * Remove tag from collection.
     *
     * @param Tag $tag Tag entity
     */
    public function removeTag(Tag $tag): void
    {
        if ($this->tags->contains($tag)) {
            $this->tags->removeElement($tag);
        }
    }

    /**
     * Function getComments.
     *
     * @return Collection
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    /**
     * Function addComment.
     *
     * @param Comment $comment
     *
     * @return Recipe comment
     */
    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setRecipe($this);
        }

        return $this;
    }

    /**
     * Function removeComment.
     *
     * @param Comment $comment
     *
     * @return Comment|null $comment
     */
    public function removeComment(Comment $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getRecipe() === $this) {
                $comment->setRecipe(null);
            }
        }

        return $this;
    }

    /**
     * @return string|null Result
     */
    public function __toString()
    {
        return $this->title;
    }
    /**
     * Gets the time.
     *
     * @return DateTimeInterface|null
     */
    public function getTime(): ?DateTimeInterface
    {
        return $this->time;
    }

    /**
     * Sets the time.
     *
     * @param DateTimeInterface|null $time
     *
     * @return self
     */
    public function setTime(?DateTimeInterface $time): self
    {
        $this->time = $time;

        return $this;
    }

    /**
     * Gets the difficulty.
     *
     * @return string|null
     */
    public function getDifficulty(): ?string
    {
        return $this->difficulty;
    }

    /**
     * Sets the difficulty.
     *
     * @param string|null $difficulty
     *
     * @return self
     */
    public function setDifficulty(?string $difficulty): self
    {
        $this->difficulty = $difficulty;

        return $this;
    }

    /**
     * Gets the portion.
     *
     * @return int|null
     */
    public function getPortion(): ?int
    {
        return $this->portion;
    }

    /**
     * Sets the portion.
     *
     * @param int|null $portion
     *
     * @return self
     */
    public function setPortion(?int $portion): self
    {
        $this->portion = $portion;

        return $this;
    }

    /**
     * Gets the calories.
     *
     * @return int|null
     */
    public function getCalories(): ?int
    {
        return $this->calories;
    }

    /**
     * Sets the calories.
     *
     * @param int|null $calories
     *
     * @return self
     */
    public function setCalories(?int $calories): self
    {
        $this->calories = $calories;

        return $this;
    }

    /**
     * Gets the content.
     *
     * @return string|null
     */
    public function getContent(): ?string
    {
        return $this->content;
    }

    /**
     * Sets the content.
     *
     * @param string|null $content
     *
     * @return self
     */
    public function setContent(?string $content): self
    {
        $this->content = $content;

        return $this;
    }
}
