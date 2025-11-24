<?php

namespace App\Entity;

use App\Repository\SessionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SessionRepository::class)]
class Session
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $label = null;

    /**
     * @var Collection<int, SessionDate>
     */
    #[ORM\OneToMany(targetEntity: SessionDate::class, mappedBy: 'session', orphanRemoval: true)]
    private Collection $sessionDates;

    #[ORM\ManyToOne(inversedBy: 'sessions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Level $level = null;

    /**
     * @var Collection<int, Contract>
     */
    #[ORM\OneToMany(targetEntity: Contract::class, mappedBy: 'session', orphanRemoval: true)]
    private Collection $contracts;

    public function __construct()
    {
        $this->sessionDates = new ArrayCollection();
        $this->contracts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): static
    {
        $this->label = $label;

        return $this;
    }

    /**
     * @return Collection<int, SessionDate>
     */
    public function getSessionDates(): Collection
    {
        return $this->sessionDates;
    }

    public function addSessionDate(SessionDate $sessionDate): static
    {
        if (!$this->sessionDates->contains($sessionDate)) {
            $this->sessionDates->add($sessionDate);
            $sessionDate->setSession($this);
        }

        return $this;
    }

    public function removeSessionDate(SessionDate $sessionDate): static
    {
        if ($this->sessionDates->removeElement($sessionDate)) {
            // set the owning side to null (unless already changed)
            if ($sessionDate->getSession() === $this) {
                $sessionDate->setSession(null);
            }
        }

        return $this;
    }

    public function getLevel(): ?Level
    {
        return $this->level;
    }

    public function setLevel(?Level $level): static
    {
        $this->level = $level;

        return $this;
    }

    /**
     * @return Collection<int, Contract>
     */
    public function getContracts(): Collection
    {
        return $this->contracts;
    }

    public function addContract(Contract $contract): static
    {
        if (!$this->contracts->contains($contract)) {
            $this->contracts->add($contract);
            $contract->setSession($this);
        }

        return $this;
    }

    public function removeContract(Contract $contract): static
    {
        if ($this->contracts->removeElement($contract)) {
            // set the owning side to null (unless already changed)
            if ($contract->getSession() === $this) {
                $contract->setSession(null);
            }
        }

        return $this;
    }
}
