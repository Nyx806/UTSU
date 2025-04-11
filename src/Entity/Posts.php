<?php

namespace App\Entity;

use App\Repository\PostsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PostsRepository::class)]
class Posts
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $post_ID = null;

    #[ORM\Column]
    private ?int $user_ID = null;

    #[ORM\Column]
    private ?int $cat_ID = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $contenu = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(type: Types::BLOB, nullable: true)]
    private $photo;

    #[ORM\ManyToOne(inversedBy: 'posts')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $poster = null;

    /**
     * @var Collection<int, Commentaire>
     */
    #[ORM\ManyToMany(targetEntity: Commentaire::class, inversedBy: 'posts')]
    private Collection $com;

    /**
     * @var Collection<int, Likes>
     */
    #[ORM\ManyToMany(targetEntity: Likes::class, inversedBy: 'posts')]
    private Collection $likes;

    #[ORM\ManyToOne(inversedBy: 'post')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Categories $categories = null;

    public function __construct()
    {
        $this->com = new ArrayCollection();
        $this->likes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPostID(): ?int
    {
        return $this->post_ID;
    }

    public function setPostID(int $post_ID): static
    {
        $this->post_ID = $post_ID;

        return $this;
    }

    public function getUserID(): ?int
    {
        return $this->user_ID;
    }

    public function setUserID(int $user_ID): static
    {
        $this->user_ID = $user_ID;

        return $this;
    }

    public function getCatID(): ?int
    {
        return $this->cat_ID;
    }

    public function setCatID(int $cat_ID): static
    {
        $this->cat_ID = $cat_ID;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getContenu(): ?string
    {
        return $this->contenu;
    }

    public function setContenu(string $contenu): static
    {
        $this->contenu = $contenu;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getPhoto()
    {
        return $this->photo;
    }

    public function setPhoto($photo): static
    {
        $this->photo = $photo;

        return $this;
    }

    public function getPoster(): ?User
    {
        return $this->poster;
    }

    public function setPoster(?User $poster): static
    {
        $this->poster = $poster;

        return $this;
    }

    /**
     * @return Collection<int, Commentaire>
     */
    public function getCom(): Collection
    {
        return $this->com;
    }

    public function addCom(Commentaire $com): static
    {
        if (!$this->com->contains($com)) {
            $this->com->add($com);
        }

        return $this;
    }

    public function removeCom(Commentaire $com): static
    {
        $this->com->removeElement($com);

        return $this;
    }

    /**
     * @return Collection<int, Likes>
     */
    public function getLikes(): Collection
    {
        return $this->likes;
    }

    public function addLike(Likes $like): static
    {
        if (!$this->likes->contains($like)) {
            $this->likes->add($like);
        }

        return $this;
    }

    public function removeLike(Likes $like): static
    {
        $this->likes->removeElement($like);

        return $this;
    }

    public function getCategories(): ?Categories
    {
        return $this->categories;
    }

    public function setCategories(?Categories $categories): static
    {
        $this->categories = $categories;

        return $this;
    }
}
