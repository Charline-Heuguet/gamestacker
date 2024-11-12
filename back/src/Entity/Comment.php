<?php

namespace App\Entity;

use App\Repository\CommentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: CommentRepository::class)]
class Comment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['forum:details','forum:read', 'comment:details','article:details', 'user:article:comments', 'user:forum:comments' ])]

    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['forum:details','forum:read', 'comment:details','article:details', 'user:article:comments', 'user:forum:comments' ])]
    private ?string $content = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(['forum:details', 'comment:details', 'article:details', 'user:article:comments' , 'user:forum:comments' ])]
    private ?\DateTimeInterface $date = null;

    #[ORM\ManyToOne(inversedBy: 'comment')]
    #[Groups(['user:forum:comments'])]
    private ?Forum $forum = null;

    #[ORM\ManyToOne(inversedBy: 'comment')]
    #[Groups(['forum:details', 'comment:details', 'article:details'])]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'comment')]
    #[Groups(['user:article:comments'])]
    private ?Article $article = null;

    #[ORM\Column(options: ['default' => 0])]
    #[Groups(['forum:details', 'comment:details', 'article:details' ])]

    private ?int $upvote = 0;

    #[ORM\Column(options: ['default' => 0])]
    #[Groups(['forum:details', 'comment:details', 'article:details' ])]
    private ?int $downvote = 0;

    /**
     * @var Collection<int, ReportComment>
     */
    #[ORM\OneToMany(targetEntity: ReportComment::class, mappedBy: 'comment')]
    private Collection $reportComments;

    public function __construct()
    {
        $this->reportComments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }


    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getForum(): ?Forum
    {
        return $this->forum;
    }

    public function setForum(?Forum $forum): static
    {
        $this->forum = $forum;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getArticle(): ?Article
    {
        return $this->article;
    }

    public function setArticle(?Article $article): static
    {
        $this->article = $article;

        return $this;
    }

    public function __toString(): string
    {
        return $this->content;  
    }

    public function getUpvote(): ?int
    {
        return $this->upvote;
    }

    public function setUpvote(int $upvote): static
    {
        $this->upvote = $upvote;

        return $this;
    }

    public function getDownvote(): ?int
    {
        return $this->downvote;
    }

    public function setDownvote(int $downvote): static
    {
        $this->downvote = $downvote;

        return $this;
    }

    /**
     * @return Collection<int, ReportComment>
     */
    public function getReportComments(): Collection
    {
        return $this->reportComments;
    }

    public function addReportComment(ReportComment $reportComment): static
    {
        if (!$this->reportComments->contains($reportComment)) {
            $this->reportComments->add($reportComment);
            $reportComment->setComment($this);
        }

        return $this;
    }

    public function removeReportComment(ReportComment $reportComment): static
    {
        if ($this->reportComments->removeElement($reportComment)) {
            // set the owning side to null (unless already changed)
            if ($reportComment->getComment() === $this) {
                $reportComment->setComment(null);
            }
        }

        return $this;
    }
}
