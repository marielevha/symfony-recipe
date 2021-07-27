<?php

namespace App\Entity;

use App\Repository\RecetteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=RecetteRepository::class)
 */
class Recette
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     * @Groups("recette:read")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups("recette:read")
     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups("comment:read")
     * @Groups("recette:read")
     */
    private $slug;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Groups("recette:read")
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups("recette:read")
     */
    private $image;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups("recette:read")
     */
    private $difficulte;

    /**
     * @ORM\Column(type="integer")
     * @Groups("recette:read")
     */
    private $personne;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Groups("recette:read")
     */
    private $duree;

    /**
     * @ORM\ManyToOne(targetEntity=Categorie::class, inversedBy="recettes")
     * @Groups("recette:read")
     */
    private $categorie;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="recettes")
     * @Groups("recette:read")
     */
    private $auteur;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Groups("recette:read")
     */
    private $date;

    /**
     * @ORM\OneToMany(targetEntity=Note::class, mappedBy="recette")
     * @Groups("recette:read")
     */
    private $notes;

    public $rating;

    public $new_image;

    /**
     * @ORM\OneToMany(targetEntity=Media::class, mappedBy="recette")
     */
    private $media;

    /**
     * @ORM\OneToMany(targetEntity=Ingredient::class, mappedBy="recette")
     */
    private $ingredients;

    /**
     * @ORM\OneToMany(targetEntity=Comment::class, mappedBy="recette")
     */
    private $comments;

    public function __construct()
    {
        $this->date = new \DateTime();
        $this->notes = new ArrayCollection();
        $this->media = new ArrayCollection();
        $this->ingredients = new ArrayCollection();
        $this->comments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getSlug()
    {
        return $this->slug;
    }

    public function setSlug($slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getDifficulte(): ?string
    {
        return $this->difficulte;
    }

    public function setDifficulte(string $difficulte): self
    {
        $this->difficulte = $difficulte;

        return $this;
    }

    public function getPersonne(): ?int
    {
        return $this->personne;
    }

    public function setPersonne(int $personne): self
    {
        $this->personne = $personne;

        return $this;
    }

    public function getDuree(): ?string
    {
        return $this->duree;
    }

    public function setDuree(?string $duree): self
    {
        $this->duree = $duree;

        return $this;
    }

    public function getCategorie(): ?Categorie
    {
        return $this->categorie;
    }

    public function setCategorie(?Categorie $categorie): self
    {
        $this->categorie = $categorie;

        return $this;
    }

    public function getAuteur(): ?User
    {
        return $this->auteur;
    }

    public function setAuteur(?User $auteur): self
    {
        $this->auteur = $auteur;

        return $this;
    }

    public function getDate(): ?\DateTime
    {
        return $this->date;
    }

    public function setDate(\DateTime $date): self
    {
        $this->date = $date;

        return $this;
    }

    /**
     * @return Collection|Note[]
     */
    public function getNotes(): Collection
    {
        return $this->notes;
    }

    public function addNote(Note $note): self
    {
        if (!$this->notes->contains($note)) {
            $this->notes[] = $note;
            $note->setRecette($this);
        }

        return $this;
    }

    public function removeNote(Note $note): self
    {
        if ($this->notes->removeElement($note)) {
            // set the owning side to null (unless already changed)
            if ($note->getRecette() === $this) {
                $note->setRecette(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Media[]
     */
    public function getMedia(): Collection
    {
        return $this->media;
    }

    public function addMedium(Media $medium): self
    {
        if (!$this->media->contains($medium)) {
            $this->media[] = $medium;
            $medium->setRecette($this);
        }

        return $this;
    }

    public function removeMedium(Media $medium): self
    {
        if ($this->media->removeElement($medium)) {
            // set the owning side to null (unless already changed)
            if ($medium->getRecette() === $this) {
                $medium->setRecette(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Ingredient[]
     */
    public function getIngredients(): Collection
    {
        return $this->ingredients;
    }

    public function addIngredient(Ingredient $ingredient): self
    {
        if (!$this->ingredients->contains($ingredient)) {
            $this->ingredients[] = $ingredient;
            $ingredient->setRecette($this);
        }

        return $this;
    }

    public function removeIngredient(Ingredient $ingredient): self
    {
        if ($this->ingredients->removeElement($ingredient)) {
            // set the owning side to null (unless already changed)
            if ($ingredient->getRecette() === $this) {
                $ingredient->setRecette(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Comment[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setRecette($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getRecette() === $this) {
                $comment->setRecette(null);
            }
        }

        return $this;
    }
}
