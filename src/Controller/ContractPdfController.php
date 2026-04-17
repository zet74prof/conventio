<?php

namespace App\Controller;

use App\Entity\Contract;
use App\Service\ContractDocumentService;
use App\Service\GotenbergPdfService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/contract/pdf')]
class ContractPdfController extends AbstractController
{
    #[Route('/html/{id}', name: 'app_contract_pdf_download', methods: ['GET'])]
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
            'internshipDate' => $contract->getInternshipDate(),
            'organisation' => $contract->getOrganisation(),
            'tutor' => $contract->getTutor(),
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

    #[Route('/{id}', name: 'app_contract_pdf_download_docx', methods: ['GET'])]
    public function download_docx(
        Contract $contract,
        ContractDocumentService $docService
    ): Response
    {
        // ... Security checks ...

        // Generate PDF using DOCX template -> Gotenberg
        $pdfContent = $docService->generateContractPdf($contract);

        return new Response($pdfContent, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="Convention_' . $contract->getStudent()->getLastname() . '.pdf"',
        ]);
    }
}
