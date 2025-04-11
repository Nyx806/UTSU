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

    #[ORM\Column]
    private ?int $categorie_ID = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $name = null;

    /**
     * @var Collection<int, Posts>
     */
    #[ORM\OneToMany(targetEntity: Posts::class, mappedBy: 'categories')]
    private Collection $post;

    public function __construct()
    {
        $this->post = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCategorieID(): ?int
    {
        return $this->categorie_ID;
    }

    public function setCategorieID(int $categorie_ID): static
    {
        $this->categorie_ID = $categorie_ID;

        return $this;
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

    /**
     * @return Collection<int, Posts>
     */
    public function getPost(): Collection
    {
        return $this->post;
    }

    public function addPost(Posts $post): static
    {
        if (!$this->post->contains($post)) {
            $this->post->add($post);
            $post->setCategories($this);
        }

        return $this;
    }

    public function removePost(Posts $post): static
    {
        if ($this->post->removeElement($post)) {
            // set the owning side to null (unless already changed)
            if ($post->getCategories() === $this) {
                $post->setCategories(null);
            }
        }

        return $this;
    }
}
