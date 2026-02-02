<?php

namespace App\Controller;

use App\Entity\Contract;
use App\Entity\Professor;
use App\Repository\ContractRepository;
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
    #[Route('/', name: 'app_professor_contract_index')]
    public function index(ContractRepository $contractRepo): Response
    {
        /** @var Professor $professor */
        $professor = $this->getUser();

        // Retrieve all contracts
        // Note: For performance optimization, a custom DQL query in repository would be better
        // e.g., JOIN contract.session s JOIN s.level l JOIN l.referentProfessors p WHERE p = :professor
        $allContracts = $contractRepo->findAll();
        $toReview = [];

        foreach ($allContracts as $contract) {
            $level = $contract->getSession()->getLevel();

            // Check if the current professor is in the collection of referents for this level
            if (
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

    #[Route('/{id}/review', name: 'app_professor_contract_review')]
    public function review(Contract $contract, Request $request, EntityManagerInterface $em): Response
    {
        /** @var Professor $professor */
        $professor = $this->getUser();

        // Security Check: Is this professor authorized for this Level?
        $level = $contract->getSession()->getLevel();
        if (!$level->getReferentProfessors()->contains($professor)) {
            throw $this->createAccessDeniedException('Vous n\'êtes pas référent pour cette formation.');
        }

        if ($request->isMethod('POST')) {
            if ($request->request->has('approve')) {
                $contract->setStatus(Contract::STATUS_APPROVED_PROF);
                $this->addFlash('success', 'La convention a été validée.');
                // Optional: Generate PDF / Send to signature workflow
            } elseif ($request->request->has('reject')) {
                $contract->setStatus(Contract::STATUS_FILLED_BY_TUTOR); // Return to previous step
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
