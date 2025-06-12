<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private ?string $email = null;

  /**
   * @var list<string> The user roles
   */
    #[ORM\Column]
    private array $roles = [];

  /**
   * @var string The hashed password
   */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $pp_img = null;

    #[ORM\Column]
    private ?int $type = null;

  /**
   * @var \Doctrine\Common\Collections\Collection<int, Abonnement>
   */
    #[ORM\OneToMany(targetEntity: Abonnement::class, mappedBy: 'userID')]
    private Collection $abonnements;

  /**
   * @var \Doctrine\Common\Collections\Collection<int, Abonnement>
   */
    #[ORM\OneToMany(targetEntity: Abonnement::class, mappedBy: 'followedUser')]
    private Collection $followers;

  /**
   * @var \Doctrine\Common\Collections\Collection<int, Posts>
   */
    #[ORM\OneToMany(targetEntity: Posts::class, mappedBy: 'userID')]
    private Collection $posts;

  /**
   * @var \Doctrine\Common\Collections\Collection<int, Likes>
   */
    #[ORM\OneToMany(targetEntity: Likes::class, mappedBy: 'userID', orphanRemoval: true)]
    private Collection $likes;

  /**
   * @var \Doctrine\Common\Collections\Collection<int, CategoryLikes>
   */
    #[ORM\OneToMany(targetEntity: CategoryLikes::class, mappedBy: 'userID', orphanRemoval: true)]
    private Collection $categoryLikes;

    #[ORM\Column(length: 255)]
    private ?string $username = null;

  /**
   * @var \Doctrine\Common\Collections\Collection<int, Commentaires>
   */
    #[ORM\OneToMany(targetEntity: Commentaires::class, mappedBy: 'userID', orphanRemoval: true)]
    private Collection $commentaires;

  /**
   * @var \Doctrine\Common\Collections\Collection<int, Notification>
   */
    #[ORM\OneToMany(targetEntity: Notification::class, mappedBy: 'user', orphanRemoval: true)]
    private Collection $notifications;

    #[ORM\Column(type: Types::INTEGER, nullable: false)]
    private ?int $dangerous = null;

    public function __construct()
    {
        $this->abonnements = new ArrayCollection();
        $this->followers = new ArrayCollection();
        $this->posts = new ArrayCollection();
        $this->likes = new ArrayCollection();
        $this->categoryLikes = new ArrayCollection();
        $this->commentaires = new ArrayCollection();
        $this->notifications = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

  /**
   * A visual identifier that represents this user.
   *
   * @see UserInterface
   */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

  /**
   * @see UserInterface
   *
   * @return list<string>
   */
    public function getRoles(): array
    {
        $roles = $this->roles;
      // Guarantee every user at least has ROLE_USER.
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

  /**
   * @param list<string> $roles
   */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

  /**
   * @see PasswordAuthenticatedUserInterface
   */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

  /**
   * @see UserInterface
   */
    public function eraseCredentials(): void
    {
      // If you store any temporary, sensitive data on the user, clear it here
      // $this->plainPassword = null;.
    }

    public function getPpImg()
    {
        return $this->pp_img;
    }

    public function setPpImg($pp_img): static
    {
        $this->pp_img = $pp_img;

        return $this;
    }

    public function getType(): ?int
    {
        return $this->type;
    }

    public function setType(int $type): static
    {
        $this->type = $type;

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
            $abonnement->setUserID($this);
        }

        return $this;
    }

    public function removeAbonnement(Abonnement $abonnement): static
    {
        if ($this->abonnements->removeElement($abonnement)) {
          // Set the owning side to null (unless already changed)
            if ($abonnement->getUserID() === $this) {
                $abonnement->setUserID(null);
            }
        }

        return $this;
    }

  /**
   * @return \Doctrine\Common\Collections\Collection<int, Abonnement>
   */
    public function getFollowers(): Collection
    {
        return $this->followers;
    }

    public function addFollower(Abonnement $follower): static
    {
        if (!$this->followers->contains($follower)) {
            $this->followers->add($follower);
            $follower->setFollowedUser($this);
        }

        return $this;
    }

    public function removeFollower(Abonnement $follower): static
    {
        if ($this->followers->removeElement($follower)) {
          // Set the owning side to null (unless already changed)
            if ($follower->getFollowedUser() === $this) {
                $follower->setFollowedUser(null);
            }
        }

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
            $post->setUserID($this);
        }

        return $this;
    }

    public function removePost(Posts $post): static
    {
        if ($this->posts->removeElement($post)) {
          // Set the owning side to null (unless already changed)
            if ($post->getUserID() === $this) {
                $post->setUserID(null);
            }
        }

        return $this;
    }

  /**
   * @return \Doctrine\Common\Collections\Collection<int, Likes>
   */
    public function getLikes(): Collection
    {
        return $this->likes;
    }

    public function addLike(Likes $like): static
    {
        if (!$this->likes->contains($like)) {
            $this->likes->add($like);
            $like->setUserID($this);
        }

        return $this;
    }

    public function removeLike(Likes $like): static
    {
        if ($this->likes->removeElement($like)) {
          // Set the owning side to null (unless already changed)
            if ($like->getUserID() === $this) {
                $like->setUserID(null);
            }
        }

        return $this;
    }

  /**
   * @return \Doctrine\Common\Collections\Collection<int, CategoryLikes>
   */
    public function getCategoryLikes(): Collection
    {
        return $this->categoryLikes;
    }

    public function addCategoryLike(CategoryLikes $categoryLike): static
    {
        if (!$this->categoryLikes->contains($categoryLike)) {
            $this->categoryLikes->add($categoryLike);
            $categoryLike->setUserID($this);
        }

        return $this;
    }

    public function removeCategoryLike(CategoryLikes $categoryLike): static
    {
        if ($this->categoryLikes->removeElement($categoryLike)) {
          // Set the owning side to null (unless already changed)
            if ($categoryLike->getUserID() === $this) {
                $categoryLike->setUserID(null);
            }
        }

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

  /**
   * @return \Doctrine\Common\Collections\Collection<int, Commentaires>
   */
    public function getCommentaires(): Collection
    {
        return $this->commentaires;
    }

    public function addCommentaire(Commentaires $commentaire): static
    {
        if (!$this->commentaires->contains($commentaire)) {
            $this->commentaires->add($commentaire);
            $commentaire->setUserID($this);
        }

        return $this;
    }

    public function removeCommentaire(Commentaires $commentaire): static
    {
        if ($this->commentaires->removeElement($commentaire)) {
          // Set the owning side to null (unless already changed)
            if ($commentaire->getUserID() === $this) {
                $commentaire->setUserID(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
      // Ou email, ou nom complet, selon ce que tu veux afficher.
        return $this->getUsername();
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
   * @return \Doctrine\Common\Collections\Collection<int, Notification>
   */
    public function getNotifications(): Collection
    {
        return $this->notifications;
    }

    public function addNotification(Notification $notification): static
    {
        if (!$this->notifications->contains($notification)) {
            $this->notifications->add($notification);
            $notification->setUser($this);
        }

        return $this;
    }

    public function removeNotification(Notification $notification): static
    {
        if ($this->notifications->removeElement($notification)) {
          // Set the owning side to null (unless already changed)
            if ($notification->getUser() === $this) {
                $notification->setUser(null);
            }
        }

        return $this;
    }
}
