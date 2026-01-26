<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Parameters;
use App\Form\ParametersType;
use App\Repository\ParametersRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin')]
// #[IsGranted('ROLE_ADMIN')]
class AdminController extends AbstractController
{
    #[Route('/parameters', name: 'app_admin_parameters')]
    public function index(Request $request, ParametersRepository $repository, EntityManagerInterface $em): Response
    {
        // Get the first parameter row, or create it if it doesn't exist
        $parameters = $repository->findOneBy([]) ?? new Parameters();

        $form = $this->createForm(ParametersType::class, $parameters);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($parameters);
            $em->flush();

            $this->addFlash('success', 'Paramètres mis à jour !');
            return $this->redirectToRoute('app_admin_parameters');
        }

        return $this->render('admin/parameters.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
