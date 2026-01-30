<?php

namespace App\Entity;

use App\Repository\SessionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

#[ORM\Entity(repositoryClass: SessionRepository::class)]
class Session
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * @var Collection<int, SessionDate>
     */
    #[ORM\OneToMany(targetEntity: SessionDate::class, mappedBy: 'session', cascade: ['persist', 'remove'], orphanRemoval: true)]
    #[Assert\Count(
        min: 1,
        minMessage: 'session.dates.min_count'
    )]
    #[Assert\Valid] // Important: Validate the inner SessionDate objects too
    private Collection $sessionDates;

    #[ORM\ManyToOne(inversedBy: 'sessions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Level $level = null;

    #[ORM\Column(options: ['default' => true])]
    private ?bool $active = true;

    public string $computedName;

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

    public function isActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): static
    {
        $this->active = $active;

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

    // Helper to get the earliest start date for sorting
    public function getFirstDate(): ?\DateTimeInterface
    {
        if ($this->sessionDates->isEmpty()) {
            return null;
        }

        // Ensure we actually return the earliest date
        // (The collection might not be sorted by DB)
        $dates = $this->sessionDates->toArray();
        usort($dates, fn($a, $b) => $a->getStartDate() <=> $b->getStartDate());

        return $dates[0]->getStartDate();
    }

    #[Assert\Callback]
    public function validateSequentialDates(ExecutionContextInterface $context): void
    {
        // Convert to array to sort them
        $dates = $this->sessionDates->toArray();

        // Sort by start date to check chronological order
        usort($dates, function(SessionDate $a, SessionDate $b) {
            if (!$a->getStartDate() || !$b->getStartDate()) return 0;
            return $a->getStartDate() <=> $b->getStartDate();
        });

        $lastEndDate = null;

        foreach ($dates as $index => $sessionDate) {
            $start = $sessionDate->getStartDate();
            $end = $sessionDate->getEndDate();

            if (!$start || !$end) continue;

            // If this is not the first period, check against the previous end date
            if ($lastEndDate !== null) {
                // Rule: New start date must be strictly after the previous end date
                if ($start <= $lastEndDate) {
                    $context->buildViolation('session.dates.overlap')
                        // We try to attach this error to the specific item in the form collection
                        ->atPath("sessionDates[$index].startDate")
                        ->addViolation();
                }
            }

            $lastEndDate = $end;
        }
    }
}
