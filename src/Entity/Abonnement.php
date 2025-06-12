<?php

namespace App\Entity;

use App\Repository\AbonnementRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AbonnementRepository::class)]
class Abonnement {
  #[ORM\Id]
  #[ORM\GeneratedValue]
  #[ORM\Column]
  private ?int $id = NULL;

  #[ORM\ManyToOne(inversedBy: 'abonnements')]
  #[ORM\JoinColumn(nullable: FALSE)]
  private ?User $userID = NULL;

  #[ORM\ManyToOne(inversedBy: 'followers')]
  #[ORM\JoinColumn(nullable: TRUE)]
  private ?User $followedUser = NULL;

  #[ORM\ManyToOne(inversedBy: 'abonnements')]
  #[ORM\JoinColumn(nullable: TRUE)]
  private ?Categories $category = NULL;

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

  public function getFollowedUser(): ?User {
    return $this->followedUser;
  }

  public function setFollowedUser(?User $followedUser): static {
    $this->followedUser = $followedUser;

    return $this;
  }

  public function getCategory(): ?Categories {
    return $this->category;
  }

  public function setCategory(?Categories $category): static {
    $this->category = $category;

    return $this;
  }

}
