<?php

namespace App\Entity;

use App\Repository\PostsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PostsRepository::class)]
class Posts {
  #[ORM\Id]
  #[ORM\GeneratedValue]
  #[ORM\Column]
  private ?int $id = NULL;

  #[ORM\Column(length: 255)]
  private ?string $title = NULL;

  #[ORM\Column(type: Types::TEXT)]
  private ?string $contenu = NULL;

  #[ORM\Column(type: Types::DATETIME_MUTABLE)]
  private ?\DateTimeInterface $date = NULL;

  #[ORM\Column(type: Types::TEXT, nullable: TRUE)]
  private ?string $photo;

  #[ORM\ManyToOne(inversedBy: 'posts')]
  #[ORM\JoinColumn(nullable: FALSE)]
  private ?User $userID = NULL;

  #[ORM\ManyToOne(inversedBy: 'posts')]
  #[ORM\JoinColumn(nullable: FALSE)]
  private ?Categories $cat = NULL;

  /**
   * @var \Doctrine\Common\Collections\Collection<int, Likes>
   */
  #[ORM\OneToMany(targetEntity: Likes::class, mappedBy: 'post', orphanRemoval: TRUE)]
  private Collection $likes;

  /**
   * @var \Doctrine\Common\Collections\Collection<int, Commentaires>
   */
  #[ORM\OneToMany(targetEntity: Commentaires::class, mappedBy: 'post')]
  private Collection $commentaires;

  #[ORM\Column(type: Types::INTEGER, nullable: FALSE)]
  private ?int $dangerous = NULL;

  public function __construct() {
    $this->likes = new ArrayCollection();
    $this->commentaires = new ArrayCollection();
  }

  public function getId(): ?int {
    return $this->id;
  }

  public function getTitle(): ?string {
    return $this->title;
  }

  public function setTitle(string $title): static {
    $this->title = $title;

    return $this;
  }

  public function getContenu(): ?string {
    return $this->contenu;
  }

  public function setContenu(string $contenu): static {
    $this->contenu = $contenu;

    return $this;
  }

  public function getDate(): ?\DateTimeInterface {
    return $this->date;
  }

  public function setDate(\DateTimeInterface $date): static {
    $this->date = $date;

    return $this;
  }

  public function getPhoto() {
    return $this->photo;
  }

  public function setPhoto($photo): static {
    $this->photo = $photo;

    return $this;
  }

  public function getUserID(): ?User {
    return $this->userID;
  }

  public function setUserID(?User $userID): static {
    $this->userID = $userID;

    return $this;
  }

  public function getCat(): ?Categories {
    return $this->cat;
  }

  public function setCat(?Categories $cat): static {
    $this->cat = $cat;

    return $this;
  }

  /**
   * @return \Doctrine\Common\Collections\Collection<int, Likes>
   */
  public function getLikes(): Collection {
    return $this->likes;
  }

  public function addLike(Likes $like): static {
    if (!$this->likes->contains($like)) {
      $this->likes->add($like);
      $like->setPost($this);
    }

    return $this;
  }

  public function removeLike(Likes $like): static {
    if ($this->likes->removeElement($like)) {
      // Set the owning side to null (unless already changed)
      if ($like->getPost() === $this) {
        $like->setPost(NULL);
      }
    }

    return $this;
  }

  /**
   * @return \Doctrine\Common\Collections\Collection<int, Commentaires>
   */
  public function getCommentaires(): Collection {
    return $this->commentaires;
  }

  public function addCommentaire(Commentaires $commentaire): static {
    if (!$this->commentaires->contains($commentaire)) {
      $this->commentaires->add($commentaire);
      $commentaire->setPost($this);
    }

    return $this;
  }

  public function removeCommentaire(Commentaires $commentaire): static {
    if ($this->commentaires->removeElement($commentaire)) {
      // Set the owning side to null (unless already changed)
      if ($commentaire->getPost() === $this) {
        $commentaire->setPost(NULL);
      }
    }

    return $this;
  }

  public function countSafeLikes(): int {
    return $this->likes->filter(fn(Likes $like) => $like->isSafe())->count();
  }

  public function countDangerousLikes(): int {
    return $this->likes->filter(fn(Likes $like) => $like->isDangerous())->count();
  }

  public function getDangerous(): ?int {
    return $this->dangerous;
  }

  public function setDangerous(int $dangerous): static {
    $this->dangerous = $dangerous;

    return $this;
  }

}
