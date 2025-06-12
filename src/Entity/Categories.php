<?php

namespace App\Entity;

use App\Repository\CategoriesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CategoriesRepository::class)]
class Categories
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    private ?string $icon = null;

    #[ORM\Column(type: Types::INTEGER, nullable: false)]
    private ?int $dangerous = null;

  /**
   * @var \Doctrine\Common\Collections\Collection<int, Posts>
   */
    #[ORM\OneToMany(targetEntity: Posts::class, mappedBy: 'cat', orphanRemoval: true)]
    private Collection $posts;

  /**
   * @var \Doctrine\Common\Collections\Collection<int, Abonnement>
   */
    #[ORM\OneToMany(targetEntity: Abonnement::class, mappedBy: 'category')]
    private Collection $abonnements;

  /**
   * @var \Doctrine\Common\Collections\Collection<int, CategoryLikes>
   */
    #[ORM\OneToMany(targetEntity: CategoryLikes::class, mappedBy: 'category', orphanRemoval: true)]
    private Collection $likes;

    public function __construct()
    {
        $this->posts = new ArrayCollection();
        $this->abonnements = new ArrayCollection();
        $this->likes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getIcon(): ?string
    {
        return $this->icon;
    }

    public function setIcon(?string $icon): static
    {
        $this->icon = $icon;

        return $this;
    }

    public function getDangerous(): ?int
    {
        return $this->dangerous;
    }

    public function setDangerous(int $dangerous): static
    {
        $this->dangerous = $dangerous;

        return $this;
    }

  /**
   * @return \Doctrine\Common\Collections\Collection<int, Posts>
   */
    public function getPosts(): Collection
    {
        return $this->posts;
    }

    public function addPost(Posts $post): static
    {
        if (!$this->posts->contains($post)) {
            $this->posts->add($post);
            $post->setCat($this);
        }

        return $this;
    }

    public function removePost(Posts $post): static
    {
        if ($this->posts->removeElement($post)) {
          // Set the owning side to null (unless already changed)
            if ($post->getCat() === $this) {
                $post->setCat(null);
            }
        }

        return $this;
    }

  /**
   * @return \Doctrine\Common\Collections\Collection<int, Abonnement>
   */
    public function getAbonnements(): Collection
    {
        return $this->abonnements;
    }

    public function addAbonnement(Abonnement $abonnement): static
    {
        if (!$this->abonnements->contains($abonnement)) {
            $this->abonnements->add($abonnement);
            $abonnement->setCategory($this);
        }

        return $this;
    }

    public function removeAbonnement(Abonnement $abonnement): static
    {
        if ($this->abonnements->removeElement($abonnement)) {
          // Set the owning side to null (unless already changed)
            if ($abonnement->getCategory() === $this) {
                $abonnement->setCategory(null);
            }
        }

        return $this;
    }

  /**
   * @return \Doctrine\Common\Collections\Collection<int, CategoryLikes>
   */
    public function getLikes(): Collection
    {
        return $this->likes;
    }

    public function addLike(CategoryLikes $like): static
    {
        if (!$this->likes->contains($like)) {
            $this->likes->add($like);
            $like->setCategory($this);
        }

        return $this;
    }

    public function removeLike(CategoryLikes $like): static
    {
        if ($this->likes->removeElement($like)) {
          // Set the owning side to null (unless already changed)
            if ($like->getCategory() === $this) {
                $like->setCategory(null);
            }
        }

        return $this;
    }

    public function countSafeLikes(): int
    {
        return $this->likes->filter(fn(CategoryLikes $like) => $like->isSafe())->count();
    }

    public function countDangerousLikes(): int
    {
        return $this->likes->filter(fn(CategoryLikes $like) => $like->isDangerous())->count();
    }

    public function __toString(): string
    {
      // Ou email, ou nom complet, selon ce que tu veux afficher.
        return $this->getName();
    }
}
