<?php

namespace App\Entity;

use App\Repository\AbonnementRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AbonnementRepository::class)]
class Abonnement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $followerId = null;

    #[ORM\Column]
    private ?int $utilisateur_ID = null;

    #[ORM\Column]
    private ?int $cible_ID = null;

    /**
     * @var Collection<int, User>
     */
    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'abonnement')]
    private Collection $users;

    public function __construct()
    {
        $this->users = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFollowerId(): ?int
    {
        return $this->followerId;
    }

    public function setFollowerId(int $followerId): static
    {
        $this->followerId = $followerId;

        return $this;
    }

    public function getUtilisateurID(): ?int
    {
        return $this->utilisateur_ID;
    }

    public function setUtilisateurID(int $utilisateur_ID): static
    {
        $this->utilisateur_ID = $utilisateur_ID;

        return $this;
    }

    public function getCibleID(): ?int
    {
        return $this->cible_ID;
    }

    public function setCibleID(int $cible_ID): static
    {
        $this->cible_ID = $cible_ID;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): static
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
            $user->addAbonnement($this);
        }

        return $this;
    }

    public function removeUser(User $user): static
    {
        if ($this->users->removeElement($user)) {
            $user->removeAbonnement($this);
        }

        return $this;
    }
}
