<?php

namespace App\Entity;

use App\Repository\ReportCommentRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReportCommentRepository::class)]
class ReportComment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'reportComments')]
    private ?ReportCategory $subject = null;

    #[ORM\ManyToOne(inversedBy: 'reportComments')]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'reportComments')]
    private ?Comment $comment = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSubject(): ?ReportCategory
    {
        return $this->subject;
    }

    public function setSubject(?ReportCategory $subject): static
    {
        $this->subject = $subject;

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

    public function getComment(): ?Comment
    {
        return $this->comment;
    }

    public function setComment(?Comment $comment): static
    {
        $this->comment = $comment;

        return $this;
    }
}
