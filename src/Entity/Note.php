<?php

namespace App\Entity;

use App\Repository\NoteRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=NoteRepository::class)
 */
class Note
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups("recette:read")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     * @Groups("recette:read")
     */
    private $valeur;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="notes")
     */
    private $auteur;

    /**
     * @ORM\ManyToOne(targetEntity=Recette::class, inversedBy="notes")
     */
    private $recette;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getValeur(): ?int
    {
        return $this->valeur;
    }

    public function setValeur(int $valeur): self
    {
        $this->valeur = $valeur;

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

    /*public function getRecette(): ?Recette
    {
        return $this->recette;
    }*/

    public function setRecette(?Recette $recette): self
    {
        $this->recette = $recette;

        return $this;
    }
}
