<?php

namespace App\Entity;

use App\Repository\ProgramRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;


#[ORM\Entity(repositoryClass: ProgramRepository::class)]
#[UniqueEntity(
    fields: ['title'],
    message: 'ce titre existe déjà'
)]
class Program
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank(message: 'Le titre ne doit pas être vide')]
    #[Assert\Length(
        max: 255,
        maxMessage: 'Le titre doit faire moins de 255 caractères'
    )]
    private $title;

    #[ORM\Column(type: 'text')]
    #[Assert\NotBlank(message: 'Le synopsis ne doit pas être vide')]
    #[Assert\Regex(
        pattern: '/plus belle la vie/i',
        match: false,
        message: 'On parle de vraies séries ici'
    )]
    private $synopsis;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $poster = null;

    #[ORM\ManyToOne(inversedBy: 'programs')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Category $category = null;

    #[ORM\OneToMany(mappedBy: 'program', targetEntity: Season::class)]
    private Collection $seasons;

    public function __construct()
    {
        $this->seasons = new ArrayCollection();
    }

    public function addSeason(Season $season): void
    {
        if (!$this->seasons->contains($season)) {
            $this->seasons[] = $season;
            $season->setProgram($this); // Set the current program to the season
        }
    }

    public function removeSeason(Season $season): void
    {
        if ($this->seasons->removeElement($season)) {
            // If the program was set on the season, set it to null
            if ($season->getProgram() === $this) {
                $season->setProgram(null);
            }
        }
    }

    public function getSeasons(): Collection
    {
        return $this->seasons;
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

    public function getSynopsis(): ?string
    {
        return $this->synopsis;
    }

    public function setSynopsis(string $synopsis): self
    {
        $this->synopsis = $synopsis;

        return $this;
    }

    public function getPoster(): ?string
    {
        return $this->poster;
    }

    public function setPoster(?string $poster): self
    {
        $this->poster = $poster;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }
}
