<?php

namespace App\Entity;

use App\Repository\NotificationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: NotificationRepository::class)]
class Notification {
  #[ORM\Id]
  #[ORM\GeneratedValue]
  #[ORM\Column]
  private ?int $id = NULL;

  #[ORM\ManyToOne(inversedBy: 'notifications')]
  #[ORM\JoinColumn(nullable: FALSE)]
  private ?User $user = NULL;

  #[ORM\ManyToOne]
  #[ORM\JoinColumn(nullable: FALSE)]
  private ?Commentaires $comment = NULL;

  #[ORM\Column(type: Types::DATETIME_MUTABLE)]
  private ?\DateTimeInterface $createdAt = NULL;

  #[ORM\Column]
  private ?bool $isRead = FALSE;

  public function __construct() {
    $this->createdAt = new \DateTime();
    $this->isRead = FALSE;
  }

  public function getId(): ?int {
    return $this->id;
  }

  public function getUser(): ?User {
    return $this->user;
  }

  public function setUser(?User $user): static {
    $this->user = $user;
    return $this;
  }

  public function getComment(): ?Commentaires {
    return $this->comment;
  }

  public function setComment(?Commentaires $comment): static {
    $this->comment = $comment;
    return $this;
  }

  public function getCreatedAt(): ?\DateTimeInterface {
    return $this->createdAt;
  }

  public function setCreatedAt(\DateTimeInterface $createdAt): static {
    $this->createdAt = $createdAt;
    return $this;
  }

  public function isRead(): ?bool {
    return $this->isRead;
  }

  public function setIsRead(bool $isRead): static {
    $this->isRead = $isRead;
    return $this;
  }

}
