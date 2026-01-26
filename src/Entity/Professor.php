<?php

namespace App\Entity;

use App\Repository\ProfessorRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProfessorRepository::class)]
class Professor extends User
{
    /**
     * @var Collection<int, Level>
     */
    #[ORM\ManyToMany(targetEntity: Level::class, inversedBy: 'professors')]
    private Collection $levels;

    /**
     * @var Collection<int, Level>
     */
    #[ORM\ManyToMany(targetEntity: Level::class, inversedBy: 'referentProfessors')]
    #[ORM\JoinTable(name: 'level_referent_professors')]
    private Collection $referentLevels;

    public function __construct()
    {
        $this->levels = new ArrayCollection();
        $this->referentLevels = new ArrayCollection();
    }

    /**
     * @return Collection<int, Level>
     */
    public function getLevels(): Collection
    {
        return $this->levels;
    }

    public function addLevel(Level $level): static
    {
        if (!$this->levels->contains($level)) {
            $this->levels->add($level);
        }

        return $this;
    }

    public function removeLevel(Level $level): static
    {
        $this->levels->removeElement($level);

        return $this;
    }

    /**
     * @return Collection<int, Level>
     */
    public function getReferentLevels(): Collection
    {
        return $this->referentLevels;
    }

    public function addReferentLevel(Level $referentLevel): static
    {
        if (!$this->referentLevels->contains($referentLevel)) {
            $this->referentLevels->add($referentLevel);
        }

        return $this;
    }

    public function removeReferentLevel(Level $referentLevel): static
    {
        $this->referentLevels->removeElement($referentLevel);

        return $this;
    }
}
