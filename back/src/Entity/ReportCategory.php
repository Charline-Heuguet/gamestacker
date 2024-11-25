<?php

namespace App\Entity;

use App\Repository\ReportCategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReportCategoryRepository::class)]
class ReportCategory
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    /**
     * @var Collection<int, ReportComment>
     */
    #[ORM\OneToMany(targetEntity: ReportComment::class, mappedBy: 'subject')]
    private Collection $reportComments;

    public function __construct()
    {
        $this->reportComments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

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
            $reportComment->setSubject($this);
        }

        return $this;
    }

    public function removeReportComment(ReportComment $reportComment): static
    {
        if ($this->reportComments->removeElement($reportComment)) {
            // set the owning side to null (unless already changed)
            if ($reportComment->getSubject() === $this) {
                $reportComment->setSubject(null);
            }
        }

        return $this;
    }
}
