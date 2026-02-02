<?php

namespace App\Controller;

use App\Entity\Contract;
use App\Entity\Organisation;
use App\Form\ContractCompleteType;
use App\Repository\ContractRepository;
use App\Repository\OrganisationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PublicContractController extends AbstractController
{
    #[Route('/contract/fill/{token}', name: 'app_public_contract_fill')]
    public function fill(
        string $token,
        ContractRepository $contractRepo,
        OrganisationRepository $orgRepo,
        Request $request,
        EntityManagerInterface $em
    ): Response
    {
        $contract = $contractRepo->findOneBy(['sharingToken' => $token]);

        if (!$contract or $contract->getTokenExpDate() < new \DateTime()) {
            throw $this->createNotFoundException('Lien invalide ou expiré.');
        }

        if (!$contract->getOrganisation()) {
            $contract->setOrganisation(new Organisation());
        }

        $form = $this->createForm(ContractCompleteType::class, $contract);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // --- 1. De-duplication Logic ---
            $submittedOrg = $contract->getOrganisation();
            $existingOrg = null;

            if ($submittedOrg->getSiret()) {
                $existingOrg = $orgRepo->findOneBy(['siret' => $submittedOrg->getSiret()]);
            }

            if (!$existingOrg && $submittedOrg->getName()) {
                $existingOrg = $orgRepo->findOneBy([
                    'name' => $submittedOrg->getName(),
                    'cityHq' => $submittedOrg->getCityHq(),
                    'countryHq' => $submittedOrg->getCountryHq()
                ]);
            }

            if ($existingOrg) {
                // Update existing org
                $existingOrg->setAddressHq($submittedOrg->getAddressHq());
                $existingOrg->setPostalCodeHq($submittedOrg->getPostalCodeHq());
                $existingOrg->setCityHq($submittedOrg->getCityHq());
                $existingOrg->setCountryHq($submittedOrg->getCountryHq());
                $existingOrg->setWebsite($submittedOrg->getWebsite());

                $existingOrg->setRespName($submittedOrg->getRespName());
                $existingOrg->setRespFunction($submittedOrg->getRespFunction());
                $existingOrg->setRespEmail($submittedOrg->getRespEmail());
                $existingOrg->setRespPhone($submittedOrg->getRespPhone());

                $existingOrg->setInsuranceName($submittedOrg->getInsuranceName());
                $existingOrg->setInsuranceContract($submittedOrg->getInsuranceContract());

                $contract->setOrganisation($existingOrg);
            }

            // At this point, $contract->getOrganisation() refers to the correct, up-to-date entity.

            // --- 2. Handle "Same Address" Logic ---
            if ($form->get('sameAddress')->getData()) {
                $org = $contract->getOrganisation();

                $contract->setAddressInternship($org->getAddressHq());
                $contract->setPostalCodeInternship($org->getPostalCodeHq());
                $contract->setCityInternship($org->getCityHq());
                $contract->setCountryInternship($org->getCountryHq());
            }

            // --- 3. Finalize ---
            $contract->setStatus(1); // FILLED
            $em->persist($contract);
            $em->flush();

            return $this->redirectToRoute('app_public_contract_success');

        }

        return $this->render('contract/fill.html.twig', [
            'form' => $form->createView(),
            'contract' => $contract,
        ]);
    }

    #[Route('/contract/success', name: 'app_public_contract_success')]
    public function success(): Response
    {
        return $this->render('contract/success.html.twig');
    }
}
