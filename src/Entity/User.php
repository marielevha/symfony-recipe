<?php

namespace App\Entity;

use App\Data\Service;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\Table(name="`user`")
 * @UniqueEntity(
 *     fields={"email"},
 *     message="Cet adresse est déjà utilisée"
 * )
 */
class User implements UserInterface
{
    use Service;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups("comment:read")
     * @Groups("recette:read")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Email()
     * @Assert\NotBlank(message="Input is required")
     * @Groups("recette:read")
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Input is required")
     * @Groups("comment:read")
     * @Groups("recette:read")
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups("comment:read")
     * @Groups("recette:read")
     */
    private $slug;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $biography;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $avatar;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Input is required")
     * @Assert\Length(min=8, minMessage="Min message customize")
     */
    private $password;

    /**
     * @Assert\NotBlank(message="Input is required")
     * @Assert\EqualTo(propertyPath="password", message="Confirm passwor not valid")
     */
    public $confirm_password;

    /**
     * @ORM\OneToMany(targetEntity=Note::class, mappedBy="auteur")
     */
    private $notes;

    /**
     * @ORM\OneToMany(targetEntity=Comment::class, mappedBy="auteur")
     */
    private $comments;

    /**
     * @ORM\OneToMany(targetEntity=Recette::class, mappedBy="auteur")
     */
    private $recettes;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $rules;

    private $roles;

    public function __construct()
    {
        //$this->slug = $this->slug($this->username);
        $this->notes = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->recettes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

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

    public function getBiography()
    {
        return $this->biography;
    }

    public function setBiography(string $biography): self
    {
        $this->biography = $biography;

        return $this;
    }

    public function getAvatar()
    {
        return $this->avatar;
    }

    public function setAvatar($avatar): self
    {
        $this->avatar = $avatar;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getRoles()
    {
        return explode(',', $this->rules);
    }

    public function getRules()
    {
        return $this->rules;
    }

    public function setRules(string $rules): self
    {
        $this->rules = $rules;

        return $this;
    }

    public function getSalt() {}

    public function eraseCredentials() {}

    public function __toString()
    {
        return $this->username;
    }

    /**
     * @return Collection|Note[]
     */
    /*public function getNotes(): Collection
    {
        return $this->notes;
    }*/

    public function addNote(Note $note): self
    {
        if (!$this->notes->contains($note)) {
            $this->notes[] = $note;
            $note->setAuteur($this);
        }

        return $this;
    }

    public function removeNote(Note $note): self
    {
        if ($this->notes->removeElement($note)) {
            // set the owning side to null (unless already changed)
            if ($note->getAuteur() === $this) {
                $note->setAuteur(null);
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
            $comment->setAuteur($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getAuteur() === $this) {
                $comment->setAuteur(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Comment[]
     */
    public function getRecettes(): Collection
    {
        return $this->comments;
    }

    public function addRecette(Recette $recette): self
    {
        if (!$this->recettes->contains($recette)) {
            $this->recettes[] = $recette;
            $recette->setRecette($this);
        }

        return $this;
    }

    public function removeRecette(Recette $recette): self
    {
        if ($this->recettes->removeElement($recette)) {
            // set the owning side to null (unless already changed)
            if ($recette->getRecette() === $this) {
                $recette->setRecette(null);
            }
        }

        return $this;
    }
}
