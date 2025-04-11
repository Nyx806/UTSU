<?php

namespace App\Entity;

use App\Repository\CommentaireRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CommentaireRepository::class)]
class Commentaire
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $com_ID = null;

    #[ORM\Column]
    private ?int $ID_post = null;

    #[ORM\Column]
    private ?int $ID_com_parent = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $contenu = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date_creation = null;

    /**
     * @var Collection<int, Posts>
     */
    #[ORM\ManyToMany(targetEntity: Posts::class, mappedBy: 'com')]
    private Collection $posts;

    public function __construct()
    {
        $this->posts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getComID(): ?int
    {
        return $this->com_ID;
    }

    public function setComID(int $com_ID): static
    {
        $this->com_ID = $com_ID;

        return $this;
    }

    public function getIDPost(): ?int
    {
        return $this->ID_post;
    }

    public function setIDPost(int $ID_post): static
    {
        $this->ID_post = $ID_post;

        return $this;
    }

    public function getIDComParent(): ?int
    {
        return $this->ID_com_parent;
    }

    public function setIDComParent(int $ID_com_parent): static
    {
        $this->ID_com_parent = $ID_com_parent;

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

    public function getDateCreation(): ?\DateTimeInterface
    {
        return $this->date_creation;
    }

    public function setDateCreation(\DateTimeInterface $date_creation): static
    {
        $this->date_creation = $date_creation;

        return $this;
    }

    /**
     * @return Collection<int, Posts>
     */
    public function getPosts(): Collection
    {
        return $this->posts;
    }

    public function addPost(Posts $post): static
    {
        if (!$this->posts->contains($post)) {
            $this->posts->add($post);
            $post->addCom($this);
        }

        return $this;
    }

    public function removePost(Posts $post): static
    {
        if ($this->posts->removeElement($post)) {
            $post->removeCom($this);
        }

        return $this;
    }
}
