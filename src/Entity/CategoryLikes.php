<?php

namespace App\Entity;

use App\Repository\CategoryLikesRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CategoryLikesRepository::class)]
class CategoryLikes {
  #[ORM\Id]
  #[ORM\GeneratedValue]
  #[ORM\Column]
  private ?int $id = NULL;

  #[ORM\ManyToOne(inversedBy: 'categoryLikes')]
  #[ORM\JoinColumn(nullable: FALSE)]
  private ?User $userID = NULL;

  #[ORM\ManyToOne(inversedBy: 'likes')]
  #[ORM\JoinColumn(nullable: FALSE)]
  private ?Categories $category = NULL;

  #[ORM\Column]
  private ?string $type = NULL;

  public function getId(): ?int {
    return $this->id;
  }

  public function getUserID(): ?User {
    return $this->userID;
  }

  public function setUserID(?User $userID): static {
    $this->userID = $userID;

    return $this;
  }

  public function getCategory(): ?Categories {
    return $this->category;
  }

  public function setCategory(?Categories $category): static {
    $this->category = $category;

    return $this;
  }

  public function getType(): ?string {
    return $this->type;
  }

  public function setType(string $type): static {
    $this->type = $type;
    return $this;
  }

  public function isSafe(): bool {
    return $this->type === 'safe';
  }

  public function isDangerous(): bool {
    return $this->type === 'dangerous';
  }

  public function __toString(): string {
    return $this->type;
  }

}
