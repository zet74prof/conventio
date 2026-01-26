<?php

namespace App\Entity;

use App\Repository\LevelRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

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
     * @var Collection<int, Session>
     */
    #[ORM\OneToMany(targetEntity: Session::class, mappedBy: 'level', orphanRemoval: true)]
    private Collection $sessions;

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
        $this->sessions = new ArrayCollection();
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
     * @return Collection<int, Session>
     */
    public function getSessions(): Collection
    {
        return $this->sessions;
    }

    public function addSession(Session $session): static
    {
        if (!$this->sessions->contains($session)) {
            $this->sessions->add($session);
            $session->setLevel($this);
        }

        return $this;
    }

    public function removeSession(Session $session): static
    {
        if ($this->sessions->removeElement($session)) {
            // set the owning side to null (unless already changed)
            if ($session->getLevel() === $this) {
                $session->setLevel(null);
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
}
