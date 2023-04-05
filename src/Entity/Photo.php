<?php

namespace App\Entity;

use App\Repository\PhotoRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PhotoRepository::class)]
class Photo
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $photoProfil = null;

    #[ORM\OneToOne(mappedBy: 'photo', cascade: ['persist', 'remove'])]
    private ?Participant $participant = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPhotoProfil(): ?string
    {
        return $this->photoProfil;
    }

    public function setPhotoProfil(string $photoProfil): self
    {
        $this->photoProfil = $photoProfil;

        return $this;
    }

    public function getParticipant(): ?Participant
    {
        return $this->participant;
    }

    public function setParticipant(?Participant $participant): self
    {
        // unset the owning side of the relation if necessary
        if ($participant === null && $this->participant !== null) {
            $this->participant->setPhoto(null);
        }

        // set the owning side of the relation if necessary
        if ($participant !== null && $participant->getPhoto() !== $this) {
            $participant->setPhoto($this);
        }

        $this->participant = $participant;

        return $this;
    }
}