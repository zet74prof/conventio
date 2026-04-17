<?php

namespace App\Entity;

use App\Repository\LevelRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

#[ORM\Entity(repositoryClass: LevelRepository::class)]
class Level
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $levelCode = null;

    #[ORM\Column(length: 255)]
    private ?string $levelName = null;

    /**
     * @var Collection<int, InternshipDate>
     */
    #[ORM\OneToMany(targetEntity: InternshipDate::class, mappedBy: 'level', cascade: ['persist', 'remove'], orphanRemoval: true)]
    #[Assert\Valid]
    private Collection $internshipDates;

    /**
     * @var Collection<int, Student>
     */
    #[ORM\OneToMany(targetEntity: Student::class, mappedBy: 'level', orphanRemoval: true)]
    private Collection $students;

    /**
     * @var Collection<int, Professor>
     */
    #[ORM\ManyToMany(targetEntity: Professor::class, mappedBy: 'levels')]
    private Collection $professors;

    /**
     * @var Collection<int, Professor>
     */
    #[ORM\ManyToMany(targetEntity: Professor::class, mappedBy: 'referentLevels')]
    private Collection $referentProfessors;

    public function __construct()
    {
        $this->internshipDates = new ArrayCollection();
        $this->students = new ArrayCollection();
        $this->professors = new ArrayCollection();
        $this->referentProfessors = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLevelCode(): ?string
    {
        return $this->levelCode;
    }

    public function setLevelCode(?string $levelCode): static
    {
        $this->levelCode = $levelCode;

        return $this;
    }

    public function getLevelName(): ?string
    {
        return $this->levelName;
    }

    public function setLevelName(string $levelName): static
    {
        $this->levelName = $levelName;

        return $this;
    }

    /**
     * @return Collection<int, InternshipDate>
     */
    public function getInternshipDates(): Collection
    {
        return $this->internshipDates;
    }

    public function addInternshipDate(InternshipDate $internshipDate): static
    {
        if (!$this->internshipDates->contains($internshipDate)) {
            $this->internshipDates->add($internshipDate);
            $internshipDate->setLevel($this);
        }

        return $this;
    }

    public function removeInternshipDate(InternshipDate $internshipDate): static
    {
        if ($this->internshipDates->removeElement($internshipDate)) {
            // set the owning side to null (unless already changed)
            if ($internshipDate->getLevel() === $this) {
                $internshipDate->setLevel(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Student>
     */
    public function getStudents(): Collection
    {
        return $this->students;
    }

    public function addStudent(Student $student): static
    {
        if (!$this->students->contains($student)) {
            $this->students->add($student);
            $student->setLevel($this);
        }

        return $this;
    }

    public function removeStudent(Student $student): static
    {
        if ($this->students->removeElement($student)) {
            // set the owning side to null (unless already changed)
            if ($student->getLevel() === $this) {
                $student->setLevel(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Professor>
     */
    public function getProfessors(): Collection
    {
        return $this->professors;
    }

    public function addProfessor(Professor $professor): static
    {
        if (!$this->professors->contains($professor)) {
            $this->professors->add($professor);
            $professor->addLevel($this);
        }

        return $this;
    }

    public function removeProfessor(Professor $professor): static
    {
        if ($this->professors->removeElement($professor)) {
            $professor->removeLevel($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Professor>
     */
    public function getReferentProfessors(): Collection
    {
        return $this->referentProfessors;
    }

    public function addReferentProfessor(Professor $referentProfessor): static
    {
        if (!$this->referentProfessors->contains($referentProfessor)) {
            $this->referentProfessors->add($referentProfessor);
            $referentProfessor->addReferentLevel($this);
        }

        return $this;
    }

    public function removeReferentProfessor(Professor $referentProfessor): static
    {
        if ($this->referentProfessors->removeElement($referentProfessor)) {
            $referentProfessor->removeReferentLevel($this);
        }

        return $this;
    }

    #[Assert\Callback]
    public function validateSequentialDates(ExecutionContextInterface $context): void
    {
        $dates = $this->internshipDates->toArray();

        // Sort by start date to check chronological order
        usort($dates, function(InternshipDate $a, InternshipDate $b) {
            if (!$a->getStartDate() || !$b->getStartDate()) return 0;
            return $a->getStartDate() <=> $b->getStartDate();
        });

        $lastEndDate = null;

        foreach ($dates as $index => $internshipDate) {
            $start = $internshipDate->getStartDate();
            $end = $internshipDate->getEndDate();

            if (!$start || !$end) continue;

            if ($lastEndDate !== null) {
                if ($start <= $lastEndDate) {
                    $context->buildViolation('session.dates.overlap')
                        ->atPath("internshipDates[$index].startDate")
                        ->addViolation();
                }
            }

            $lastEndDate = $end;
        }
    }
}
