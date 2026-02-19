<?php

namespace App\Service;

use App\Entity\Contract;
use PhpOffice\PhpWord\TemplateProcessor;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Filesystem\Filesystem;

class ContractDocumentService
{
    public function __construct(
        private GotenbergPdfService $gotenbergPdfService,
        #[Autowire('%kernel.project_dir%')] private string $projectDir
    ) {}

    public function generateContractPdf(Contract $contract): string
    {
        // 1. Load the Template
        $templatePath = $this->projectDir . '/assets/docs/convention-template.docx';
        $templateProcessor = new TemplateProcessor($templatePath);

        // 2. Simple Variable Replacement
        // Syntax in DOCX must be ${variable}
        $student = $contract->getStudent();
        $org = $contract->getOrganisation();
        $tutor = $contract->getTutor();
        $session = $contract->getSession();

        //$templateProcessor->setValue('student_fullname', $student->getFullName());
        //$templateProcessor->setValue('student_email', $student->getPersonalEmail());

        $templateProcessor->setValue('organisationName', $org->getName());
        $templateProcessor->setValue('addressHq', $org->getAddressHq());
        $templateProcessor->setValue('postalCodeHq', $org->getPostalCodeHq());
        $templateProcessor->setValue('cityHq', $org->getCityHq());
        $templateProcessor->setValue('countryHq', $org->getCountryHq());
        $templateProcessor->setValue('siret', $org->getSiret());
        $templateProcessor->setValue('respName', $org->getRespName());
        $templateProcessor->setValue('respEmail', $org->getRespEmail());
        $templateProcessor->setValue('website', $org->getWebsite());
        $templateProcessor->setValue('addressInternship', $contract->getAddressInternShip());
        $templateProcessor->setValue('postalCodeInternship', $contract->getPostalCodeInternship());
        $templateProcessor->setValue('cityInternship', $contract->getCityInternship());
        $templateProcessor->setValue('countryInternship', $contract->getCountryInternship());
        $templateProcessor->setValue('insuranceName', $org->getInsuranceName());
        $templateProcessor->setValue('insuranceContract', $org->getInsuranceContract());

        $templateProcessor->setValue('tutorName', $tutor ? $tutor->getFullName() : 'Non assigné');
        $templateProcessor->setValue('tutorPhone', $tutor ? $tutor->getTelMobile() : 'Non assigné');
        $templateProcessor->setValue('tutorEmail', $tutor ? $tutor->getEmail() : 'Non assigné');

        $templateProcessor->setValue('start_date', $session->getSessionDates()->first()?->getStartDate()->format('d/m/Y') ?? '--');
        $templateProcessor->setValue('end_date', $session->getSessionDates()->first()?->getEndDate()->format('d/m/Y') ?? '--');

        //$templateProcessor->setValue('activities', $contract->getPlannedActivities());

        // Logistical Checkboxes (Example: replace with "OUI" or "NON")
        //$templateProcessor->setValue('gratification', $contract->isBonus() ? 'OUI' : 'NON');
        // ... add other fields

        // 3. Handle Work Hours (Table Row Cloning)
        // In the DOCX, create a row with: ${day} | ${am} | ${pm}
        $workHours = json_decode($contract->getWorkHours() ?: '[]', true);
        $days = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'];

        $values = [];
        foreach ($days as $day) {
            $data = $workHours[$day] ?? [];
            $values[] = [
                'day' => $day,
                'am' => isset($data['am_start']) ? "{$data['am_start']} - {$data['am_end']}" : '-',
                'pm' => isset($data['pm_start']) ? "{$data['pm_start']} - {$data['pm_end']}" : '-',
            ];
        }

        // This clones the row containing ${day} as many times as there are days
        //$templateProcessor->cloneRowAndSetValues('day', $values);

        // 4. Save Filled DOCX Temporarily
        $fs = new Filesystem();
        $tempDir = $this->projectDir . '/var/temp';
        if (!$fs->exists($tempDir)) {
            $fs->mkdir($tempDir);
        }

        $tempFileName = $tempDir . '/contract_' . $contract->getId() . '_' . uniqid() . '.docx';
        $templateProcessor->saveAs($tempFileName);

        // 5. Convert to PDF via Gotenberg
        try {
            $pdfContent = $this->gotenbergPdfService->convertOfficeDocument($tempFileName);
        } catch (\Throwable $e) {
            // Dump the error and file information to the screen
            dd([
                'error_message' => $e->getMessage(),
                'file_path' => $tempFileName,
                'file_exists' => file_exists($tempFileName),
                'file_size' => file_exists($tempFileName) ? filesize($tempFileName) . ' bytes' : 'N/A',
                'gotenberg_url' => $_ENV['GOTENBERG_URL'] ?? 'Not set in ENV',
            ]);
        } finally {
            // IMPORTANT: Comment out the removal so you can open the DOCX file manually!
            $fs->remove($tempFileName);
        }

        return $pdfContent;
    }
}
