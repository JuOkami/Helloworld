<?php

namespace App\Entity;

use App\Repository\SerieRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: SerieRepository::class)]
class Serie
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
//    #[Groups('serie:read')]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
//    #[Groups('serie:read')]
    private ?string $titre = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Length(min:4, max: 255, minMessage:'Déso !!')]
    #[Assert\NotBlank]
    #[Assert\NotNull]
    #[Assert\NotCompromisedPassword]
//    #[Groups('serie:read')]
    private ?string $image = null;

    #[ORM\OneToMany(mappedBy: 'serie', targetEntity: Saison::class, orphanRemoval: true)]
    private Collection $saisons;

    public function __serialize(): array
    {
        return[
            'id' => $this->id,
            'titre' => $this->titre,
            'image' => $this->image
        ];
    }


    public function __construct()
    {
        $this->saisons = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): static
    {
        $this->titre = $titre;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): static
    {
        $this->image = $image;

        return $this;
    }

    /**
     * @return Collection<int, Saison>
     */
    public function getSaisons(): Collection
    {
        return $this->saisons;
    }

    public function addSaison(Saison $saison): static
    {
        if (!$this->saisons->contains($saison)) {
            $this->saisons->add($saison);
            $saison->setSerie($this);
        }

        return $this;
    }

    public function removeSaison(Saison $saison): static
    {
        if ($this->saisons->removeElement($saison)) {
            // set the owning side to null (unless already changed)
            if ($saison->getSerie() === $this) {
                $saison->setSerie(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
      return $this->titre;
    }


}
