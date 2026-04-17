<?php

namespace App\Entity;

use App\Repository\ContractRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ContractRepository::class)]
class Contract
{
    public const STATUS_STARTED = 0;
    public const STATUS_FILLED_BY_TUTOR = 1;
    public const STATUS_APPROVAL_REQUESTED = 2;
    public const STATUS_APPROVED_PROF = 3;
    public const STATUS_SIGNATURE_REQUESTED = 4;
    public const STATUS_SIGNED = 5;
    public const STATUS_CANCELLED = 6;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $status = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $placeNameInternship = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $addressInternShip = null;

    #[ORM\Column(length: 15, nullable: true)]
    private ?string $postalCodeInternship = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $cityInternship = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $countryInternship = null;

    #[ORM\Column(nullable: true)]
    private ?bool $deplacement = null;

    #[ORM\Column(nullable: true)]
    private ?bool $transportFeeTaken = null;

    #[ORM\Column(nullable: true)]
    private ?bool $lunchTaken = null;

    #[ORM\Column(nullable: true)]
    private ?bool $hostTaken = null;

    #[ORM\Column(nullable: true)]
    private ?bool $bonus = null;

    #[ORM\Column(length: 3000, nullable: true)]
    private ?string $workHours = null;

    #[ORM\Column(length: 8000, nullable: true)]
    private ?string $plannedActivities = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $sharingToken = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTime $tokenExpDate = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $pdfUnsigned = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $pdfSigned = null;

    #[ORM\ManyToOne(inversedBy: 'contracts')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Student $student = null;

    #[ORM\ManyToOne(inversedBy: 'contracts')]
    private ?Tutor $tutor = null;

    #[ORM\ManyToOne(inversedBy: 'contracts')]
    #[ORM\JoinColumn(nullable: false)]
    private ?InternshipDate $internshipDate = null;

    /**
     * @var Collection<int, ContractDate>
     */
    #[ORM\OneToMany(targetEntity: ContractDate::class, mappedBy: 'contract', orphanRemoval: true)]
    private Collection $contractDates;

    #[ORM\ManyToOne(inversedBy: 'contracts', cascade: ['persist'])]
    private ?Organisation $organisation = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $signatureRequestId = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $signedContractPath = null;

    public function __construct()
    {
        $this->contractDates = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(int $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getPlaceNameInternship(): ?string
    {
        return $this->placeNameInternship;
    }

    public function setPlaceNameInternship(?string $placeNameInternship): void
    {
        $this->placeNameInternship = $placeNameInternship;
    }

    public function getAddressInternShip(): ?string
    {
        return $this->addressInternShip;
    }

    public function setAddressInternShip(string $addressInternShip): static
    {
        $this->addressInternShip = $addressInternShip;

        return $this;
    }

    public function getPostalCodeInternship(): ?string
    {
        return $this->postalCodeInternship;
    }

    public function setPostalCodeInternship(string $postalCodeInternship): static
    {
        $this->postalCodeInternship = $postalCodeInternship;

        return $this;
    }

    public function getCityInternship(): ?string
    {
        return $this->cityInternship;
    }

    public function setCityInternship(string $cityInternship): static
    {
        $this->cityInternship = $cityInternship;

        return $this;
    }

    public function getCountryInternship(): ?string
    {
        return $this->countryInternship;
    }

    public function setCountryInternship(string $countryInternship): static
    {
        $this->countryInternship = $countryInternship;

        return $this;
    }

    public function isDeplacement(): ?bool
    {
        return $this->deplacement;
    }

    public function setDeplacement(bool $deplacement): static
    {
        $this->deplacement = $deplacement;

        return $this;
    }

    public function isTransportFeeTaken(): ?bool
    {
        return $this->transportFeeTaken;
    }

    public function setTransportFeeTaken(bool $transportFeeTaken): static
    {
        $this->transportFeeTaken = $transportFeeTaken;

        return $this;
    }

    public function isLunchTaken(): ?bool
    {
        return $this->lunchTaken;
    }

    public function setLunchTaken(bool $lunchTaken): static
    {
        $this->lunchTaken = $lunchTaken;

        return $this;
    }

    public function isHostTaken(): ?bool
    {
        return $this->hostTaken;
    }

    public function setHostTaken(bool $hostTaken): static
    {
        $this->hostTaken = $hostTaken;

        return $this;
    }

    public function isBonus(): ?bool
    {
        return $this->bonus;
    }

    public function setBonus(bool $bonus): static
    {
        $this->bonus = $bonus;

        return $this;
    }

    public function getWorkHours(): ?string
    {
        return $this->workHours;
    }

    public function setWorkHours(string $workHours): static
    {
        $this->workHours = $workHours;

        return $this;
    }

    public function getPlannedActivities(): ?string
    {
        return $this->plannedActivities;
    }

    public function setPlannedActivities(string $plannedActivities): static
    {
        $this->plannedActivities = $plannedActivities;

        return $this;
    }

    public function getSharingToken(): ?string
    {
        return $this->sharingToken;
    }

    public function setSharingToken(?string $sharingToken): static
    {
        $this->sharingToken = $sharingToken;

        return $this;
    }

    public function getTokenExpDate(): ?\DateTime
    {
        return $this->tokenExpDate;
    }

    public function setTokenExpDate(?\DateTime $tokenExpDate): static
    {
        $this->tokenExpDate = $tokenExpDate;

        return $this;
    }

    public function getPdfUnsigned(): ?string
    {
        return $this->pdfUnsigned;
    }

    public function setPdfUnsigned(string $pdfUnsigned): static
    {
        $this->pdfUnsigned = $pdfUnsigned;

        return $this;
    }

    public function getPdfSigned(): ?string
    {
        return $this->pdfSigned;
    }

    public function setPdfSigned(?string $pdfSigned): static
    {
        $this->pdfSigned = $pdfSigned;

        return $this;
    }

    public function getStudent(): ?Student
    {
        return $this->student;
    }

    public function setStudent(?Student $student): static
    {
        $this->student = $student;

        return $this;
    }

    public function getTutor(): ?Tutor
    {
        return $this->tutor;
    }

    public function setTutor(?Tutor $tutor): static
    {
        $this->tutor = $tutor;

        return $this;
    }

    public function getInternshipDate(): ?InternshipDate
    {
        return $this->internshipDate;
    }

    public function setInternshipDate(?InternshipDate $internshipDate): static
    {
        $this->internshipDate = $internshipDate;

        return $this;
    }

    /**
     * @return Collection<int, ContractDate>
     */
    public function getContractDates(): Collection
    {
        return $this->contractDates;
    }

    public function addContractDate(ContractDate $contractDate): static
    {
        if (!$this->contractDates->contains($contractDate)) {
            $this->contractDates->add($contractDate);
            $contractDate->setContract($this);
        }

        return $this;
    }

    public function removeContractDate(ContractDate $contractDate): static
    {
        if ($this->contractDates->removeElement($contractDate)) {
            // set the owning side to null (unless already changed)
            if ($contractDate->getContract() === $this) {
                $contractDate->setContract(null);
            }
        }

        return $this;
    }

    public function getOrganisation(): ?Organisation
    {
        return $this->organisation;
    }

    public function setOrganisation(?Organisation $organisation): static
    {
        $this->organisation = $organisation;

        return $this;
    }

    public function getSignatureRequestId(): ?string
    {
        return $this->signatureRequestId;
    }

    public function setSignatureRequestId(?string $signatureRequestId): static
    {
        $this->signatureRequestId = $signatureRequestId;

        return $this;
    }

    public function getSignedContractPath(): ?string
    {
        return $this->signedContractPath;
    }

    public function setSignedContractPath(?string $signedContractPath): static
    {
        $this->signedContractPath = $signedContractPath;

        return $this;
    }
}
