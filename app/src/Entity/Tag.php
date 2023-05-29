<?php
/**
 * Tag entity.
 */

namespace App\Entity;

use App\Repository\TagRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TagRepository::class)]
class Tag
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\ManyToMany(targetEntity: Recipe::class, mappedBy: "tags")]
    private $recipes;

    /**
     * Tag constructor.
     */
    public function __construct()
    {
        $this->recipes = new ArrayCollection();
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
     * Getter for recipes.
     *
     * @return \Doctrine\Common\Collections\Collection|\App\Entity\Recipe[] Recipes collection
     */
    public function getRecipes(): Collection
    {
        return $this->recipes;
    }

    /**
     * Add recipe to collection.
     *
     * @param \App\Entity\Recipe $recipe Recipe entity
     */
    public function addRecipe(Recipe $recipe): void
    {
        if (!$this->recipes->contains($recipe)) {
            $this->recipes[] = $recipe;
            $recipe->addTag($this);
        }
    }

    /**
     * Remove recipe from collection.
     *
     * @param \App\Entity\Recipe $recipe Recipe entity
     */
    public function removeRecipe(Recipe $recipe): void
    {
        if ($this->recipes->contains($recipe)) {
            $this->recipes->removeElement($recipe);
            $recipe->removeTag($this);
        }
    }
}
