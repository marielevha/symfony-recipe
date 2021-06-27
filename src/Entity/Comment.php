<?php

namespace App\Entity;

use App\Data\Service;
use App\Repository\CommentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=CommentRepository::class)
 */
class Comment
{
    use Service;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups("comment:read")
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     * @Groups("comment:read")
     */
    private $content;

    /**
     * @ORM\Column(type="datetime")
     * @Groups("comment:read")
     */
    private $publish_at;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="comments")
     * @Groups("comment:read")
     */
    private $auteur;

    /**
     * @ORM\ManyToOne(targetEntity=Comment::class, inversedBy="comments")
     */
    private $main_comment;

    /**
     * @ORM\OneToMany(targetEntity=Comment::class, mappedBy="main_comment")
     */
    private $comments;

    /**
     * @ORM\ManyToOne(targetEntity=Recette::class, inversedBy="comments")
     */
    private $recette;

    /**
     * @Groups("comment:read")
     */
    private $time_ago;

    public function __construct()
    {
        $this->publish_at = new \DateTime();
        $this->comments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getPublishAt(): ?\DateTimeInterface
    {
        return $this->publish_at;
    }

    public function setPublishAt(\DateTimeInterface $publish_at): self
    {
        $this->publish_at = $publish_at;

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

    public function getMainComment(): ?self
    {
        return $this->main_comment;
    }

    public function setMainComment(?self $main_comment): self
    {
        $this->main_comment = $main_comment;

        return $this;
    }

    public function getRecette(): ?Recette
    {
        return $this->recette;
    }

    public function setRecette(?Recette $recette): self
    {
        $this->recette = $recette;

        return $this;
    }

    /**
     * @return Collection|self[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(self $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setMainComment($this);
        }

        return $this;
    }

    public function removeComment(self $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getMainComment() === $this) {
                $comment->setMainComment(null);
            }
        }

        return $this;
    }

    public function getTimeAgo(): string
    {
        //return $this->time_ago;
        return $this->time_ago(date_format($this->publish_at, 'Y-m-d H:i:s'));
        //return $this->time_ago($this->publish_at);
    }

    public function setTimeAgo(string $time_ago): void
    {
        $this->time_ago = $time_ago;
    }
}
