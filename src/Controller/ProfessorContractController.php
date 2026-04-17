<?php

namespace App\Controller;

use App\Entity\Contract;
use App\Entity\Professor;
use App\Repository\ContractRepository;
use App\Repository\LevelRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/professor/contracts')]
#[IsGranted('ROLE_PROFESSOR')]
class ProfessorContractController extends AbstractController
{
    private const STATUS_LABELS = [
        Contract::STATUS_STARTED => 'Initié',
        Contract::STATUS_FILLED_BY_TUTOR => 'Rempli par tuteur',
        Contract::STATUS_APPROVAL_REQUESTED => 'En attente de validation',
        Contract::STATUS_APPROVED_PROF => 'Validé par prof',
        Contract::STATUS_SIGNATURE_REQUESTED => 'En cours de signature',
        Contract::STATUS_SIGNED => 'Signé',
        Contract::STATUS_CANCELLED => 'Annulé'
    ];

    private const STATUS_COLORS = [
        Contract::STATUS_STARTED => 'secondary',
        Contract::STATUS_FILLED_BY_TUTOR => 'info',
        Contract::STATUS_APPROVAL_REQUESTED => 'warning',
        Contract::STATUS_APPROVED_PROF => 'primary',
        Contract::STATUS_SIGNATURE_REQUESTED => 'info',
        Contract::STATUS_SIGNED => 'success',
        Contract::STATUS_CANCELLED => 'danger'
    ];

    #[Route('/', name: 'app_professor_contract_index')]
    public function index(ContractRepository $contractRepo): Response
    {
        /** @var Professor $professor */
        $professor = $this->getUser();

        $allContracts = $contractRepo->findAll();
        $toReview = [];

        foreach ($allContracts as $contract) {
            $internshipDate = $contract->getInternshipDate();
            $level = $internshipDate ? $internshipDate->getLevel() : null;

            if (
                $level &&
                $contract->getStatus() === Contract::STATUS_APPROVAL_REQUESTED &&
                $level->getReferentProfessors()->contains($professor)
            ) {
                $toReview[] = $contract;
            }
        }

        return $this->render('contract/professor_contract_index.html.twig', [
            'contracts' => $toReview,
        ]);
    }

    #[Route('/students', name: 'app_professor_students')]
    public function students(LevelRepository $levelRepo): Response
    {
        /** @var Professor $professor */
        $professor = $this->getUser();

        $levels = $levelRepo->findLevelsWithStudentsAndContracts($professor);

        return $this->render('contract/professor_students.html.twig', [
            'levels' => $levels,
            'statusLabels' => self::STATUS_LABELS,
            'statusColors' => self::STATUS_COLORS,
        ]);
    }

    #[Route('/{id}/review', name: 'app_professor_contract_review')]
    public function review(Contract $contract, Request $request, EntityManagerInterface $em): Response
    {
        /** @var Professor $professor */
        $professor = $this->getUser();

        $internshipDate = $contract->getInternshipDate();
        $level = $internshipDate ? $internshipDate->getLevel() : null;
        
        if (!$level || !$level->getReferentProfessors()->contains($professor)) {
            throw $this->createAccessDeniedException('Vous n\'êtes pas référent pour cette formation.');
        }

        if ($request->isMethod('POST')) {
            if ($request->request->has('approve')) {
                $contract->setStatus(Contract::STATUS_APPROVED_PROF);
                $this->addFlash('success', 'La convention a été validée.');
            } elseif ($request->request->has('reject')) {
                $contract->setStatus(Contract::STATUS_FILLED_BY_TUTOR);
                $this->addFlash('warning', 'La convention a été refusée et renvoyée à l\'étudiant.');
            }
            $em->flush();

            return $this->redirectToRoute('app_professor_contract_index');
        }

        return $this->render('contract/professor_contract_review.html.twig', [
            'contract' => $contract,
            'workHours' => json_decode($contract->getWorkHours() ?: '[]', true)
        ]);
    }
}
