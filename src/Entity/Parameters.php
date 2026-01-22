<?php

namespace App\Entity;

use App\Repository\ParametersRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ParametersRepository::class)]
class Parameters
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $provisorName = null;

    #[ORM\Column(length: 320, nullable: true)]
    private ?string $provisorEmail = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $ddfptName = null;

    #[ORM\Column(length: 320, nullable: true)]
    private ?string $ddfptEmail = null;

    #[ORM\Column(length: 15, nullable: true)]
    private ?string $ddfptTel = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $studentEmailDomain;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $professorEmailDomain;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProvisorName(): ?string
    {
        return $this->provisorName;
    }

    public function setProvisorName(string $provisorName): static
    {
        $this->provisorName = $provisorName;

        return $this;
    }

    public function getProvisorEmail(): ?string
    {
        return $this->provisorEmail;
    }

    public function setProvisorEmail(string $provisorEmail): static
    {
        $this->provisorEmail = $provisorEmail;

        return $this;
    }

    public function getDdfptName(): ?string
    {
        return $this->ddfptName;
    }

    public function setDdfptName(string $ddfptName): static
    {
        $this->ddfptName = $ddfptName;

        return $this;
    }

    public function getDdfptEmail(): ?string
    {
        return $this->ddfptEmail;
    }

    public function setDdfptEmail(string $ddfptEmail): static
    {
        $this->ddfptEmail = $ddfptEmail;

        return $this;
    }

    public function getDdfptTel(): ?string
    {
        return $this->ddfptTel;
    }

    public function setDdfptTel(string $ddfptTel): static
    {
        $this->ddfptTel = $ddfptTel;

        return $this;
    }

    public function getStudentEmailDomain(): ?string
    {
        return $this->studentEmailDomain;
    }

    public function setStudentEmailDomain(?string $studentEmailDomain): static
    {
        $this->studentEmailDomain = $studentEmailDomain;
        return $this;
    }

    public function getProfessorEmailDomain(): ?string
    {
        return $this->professorEmailDomain;
    }

    public function setProfessorEmailDomain(?string $professorEmailDomain): static
    {
        $this->professorEmailDomain = $professorEmailDomain;
        return $this;
    }
}
