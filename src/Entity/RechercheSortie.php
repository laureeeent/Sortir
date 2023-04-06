<?php

namespace App\Entity;

class RechercheSortie
{
    private ?Campus $campus = null;

    private ?string $nomSortieContient = null;

    private ?\DateTime $dateAPartirDe = null;

    private ?\DateTime $dateJusqu_a = null;

    private ?bool $isOrganisateur = false;

    private ?bool $isInscrit = false;

    private ?bool $isNonInscrit = false;

    private ?bool $sortiePassee = false;

    private ?Participant $participant = null;


    /**
     * @return Campus|null
     */
    public function getCampus(): ?Campus
    {
        return $this->campus;
    }

    /**
     * @param Campus|null $campus
     */
    public function setCampus(?Campus $campus): void
    {
        $this->campus = $campus;
    }

    /**
     * @return string|null
     */
    public function getNomSortieContient(): ?string
    {
        return $this->nomSortieContient;
    }

    /**
     * @param string|null $nomSortieContient
     */
    public function setNomSortieContient(?string $nomSortieContient): void
    {
        $this->nomSortieContient = $nomSortieContient;
    }

    /**
     * @return \DateTime|null
     */
    public function getDateAPartirDe(): ?\DateTime
    {
        return $this->dateAPartirDe;
    }

    /**
     * @param \DateTime|null $dateAPartirDe
     */
    public function setDateAPartirDe(?\DateTime $dateAPartirDe): void
    {
        $this->dateAPartirDe = $dateAPartirDe;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getDateJusquA(): ?\DateTimeInterface
    {
        return $this->dateJusqu_a;
    }

    /**
     * @param \DateTimeInterface|null $dateJusqu_a
     */
    public function setDateJusquA(?\DateTimeInterface $dateJusqu_a): void
    {
        $this->dateJusqu_a = $dateJusqu_a;
    }

    /**
     * @return bool|null
     */
    public function getIsOrganisateur(): ?bool
    {
        return $this->isOrganisateur;
    }

    /**
     * @param bool|null $isOrganisateur
     */
    public function setIsOrganisateur(?bool $isOrganisateur): void
    {
        $this->isOrganisateur = $isOrganisateur;
    }

    /**
     * @return bool|null
     */
    public function getIsInscrit(): ?bool
    {
        return $this->isInscrit;
    }

    /**
     * @param bool|null $isInscrit
     */
    public function setIsInscrit(?bool $isInscrit): void
    {
        $this->isInscrit = $isInscrit;
    }

    /**
     * @return bool|null
     */
    public function getIsNonInscrit(): ?bool
    {
        return $this->isNonInscrit;
    }

    /**
     * @param bool|null $isNonInscrit
     */
    public function setIsNonInscrit(?bool $isNonInscrit): void
    {
        $this->isNonInscrit = $isNonInscrit;
    }

    /**
     * @return bool|null
     */
    public function getSortiePassee(): ?bool
    {
        return $this->sortiePassee;
    }

    /**
     * @param bool|null $sortiePassee
     */
    public function setSortiePassee(?bool $sortiePassee): void
    {
        $this->sortiePassee = $sortiePassee;
    }

    /**
     * @return Participant|null
     */
    public function getParticipant(): ?Participant
    {
        return $this->participant;
    }

    /**
     * @param Participant|null $participant
     */
    public function setParticipant(?Participant $participant): void
    {
        $this->participant = $participant;
    }




}