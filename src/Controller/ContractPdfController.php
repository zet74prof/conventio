<?php

namespace App\Controller;

use App\Entity\Contract;
use App\Service\GotenbergPdfService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/contract/pdf')]
class ContractPdfController extends AbstractController
{
    #[Route('/{id}', name: 'app_contract_pdf_download', methods: ['GET'])]
    #[IsGranted('ROLE_USER')] // Adjust security as needed (ROLE_STUDENT, ROLE_ADMIN, etc.)
    public function download(
        Contract $contract,
        GotenbergPdfService $pdfService
    ): Response
    {
        // Security check: ensure user can view this contract
        // if ($contract->getStudent() !== $this->getUser() && !$this->isGranted('ROLE_ADMIN')) {
        //    throw $this->createAccessDeniedException();
        // }

        // Decode Work Hours JSON for the template
        $workHours = [];
        if ($contract->getWorkHours()) {
            // If stored as JSON string in DB
            if (is_string($contract->getWorkHours())) {
                $workHours = json_decode($contract->getWorkHours(), true);
            } else {
                $workHours = $contract->getWorkHours();
            }
        }

        // Render the HTML Template
        $html = $this->renderView('contract/pdf_template.html.twig', [
            'contract' => $contract,
            'student' => $contract->getStudent(),
            'organisation' => $contract->getOrganisation(),
            'tutor' => $contract->getTutor(),
            'session' => $contract->getSession(),
            'workHours' => $workHours
        ]);

        // Generate PDF
        $pdfContent = $pdfService->generatePdfFromHtml($html);

        // Return File Download
        return new Response($pdfContent, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="Convention_' . $contract->getStudent()->getLastname() . '.pdf"',
        ]);
    }
}
