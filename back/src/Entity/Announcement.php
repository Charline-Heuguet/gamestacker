<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\AnnouncementRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: AnnouncementRepository::class)]
class Announcement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['announcement:read', 'announcement:details'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['announcement:read', 'announcement:details', 'user:announcement', 'article:annonce'])]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['announcement:details'])]
    private ?string $content = null;

    #[ORM\Column(length: 30)]
    #[Groups(['announcement:read', 'announcement:details', 'user:announcement', 'article:annonce'])]
    private ?string $game = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(['announcement:read', 'announcement:details', 'user:announcement', 'article:annonce'])]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column]
    #[Groups(['announcement:read', 'announcement:details', 'user:announcement'])]
    private ?int $max_nb_players = null;

    /**
     * @var Collection<int, Category>
     */
    #[ORM\ManyToMany(targetEntity: Category::class, inversedBy: 'announcements')]
    #[Groups(['announcement:read', 'announcement:details', 'article:annonce'])]
    private Collection $category;

    // Relation ManyToOne avec User (l'utilisateur qui publie l'annonce)
    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'announcements')]
    #[Groups(['announcement:read', 'announcement:details', 'article:annonce'])]
    private ?User $user = null;

    /**
     * @var Collection<int, User>
     */
    // Relation ManyToMany avec User (les participants de l'annonce)
    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'announcementsParticipated')]
    #[Groups(['announcement:details'])]
    private Collection $participants;

    #[ORM\Column(length: 15)]
    #[Groups(['announcement:read','announcement:details'])]
    private ?string $roomId = null;

    public function __construct()
    {
        $this->category = new ArrayCollection();
        $this->participants = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
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

    public function getGame(): ?string
    {
        return $this->game;
    }

    public function setGame(string $game): static
    {
        $this->game = $game;

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

    public function getMaxNbPlayers(): ?int
    {
        return $this->max_nb_players;
    }

    public function setMaxNbPlayers(int $max_nb_players): static
    {
        $this->max_nb_players = $max_nb_players;

        return $this;
    }

    /**
     * @return Collection<int, Category>
     */
    public function getCategory(): Collection
    {
        return $this->category;
    }

    public function addCategory(Category $category): static
    {
        if (!$this->category->contains($category)) {
            $this->category->add($category);
        }

        return $this;
    }

    public function removeCategory(Category $category): static
    {
        $this->category->removeElement($category);

        return $this;
    }

    // GESTION DU PUBLISHER: 
    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

  // Gestion des participants
    /**
     * @return Collection<int, User>
     */
    public function getParticipants(): Collection
    {
        return $this->participants;
    }

    public function addParticipant(User $participant): static
    {
        if (!$this->participants->contains($participant)) {
            $this->participants->add($participant);
        }

        return $this;
    }

    public function removeParticipant(User $participant): static
    {
        $this->participants->removeElement($participant);

        return $this;
    }

    public function getRoomId(): ?string
    {
        return $this->roomId;
    }

    public function setRoomId(string $roomId): static
    {
        $this->roomId = $roomId;

        return $this;
    }

}
