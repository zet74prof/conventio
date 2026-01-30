<?php

namespace App\Controller;

use App\Entity\Session;
use App\Form\SessionType;
use App\Repository\SessionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/admin/session')]
//#[IsGranted('ROLE_ADMIN')]
class AdminSessionController extends AbstractController
{
    #[Route('/', name: 'app_session_index', methods: ['GET'])]
    public function index(SessionRepository $sessionRepository, TranslatorInterface $translator): Response
    {
        $sessions = $sessionRepository->findAllSorted();

        // 1. Grouping Structure
        // structure = [
        //    'LevelName' => [
        //       'years' => [
        //           2024 => [ sessionObj, sessionObj ],
        //           2025 => [ ... ]
        //       ]
        //    ]
        // ]
        $grouped = [];

        foreach ($sessions as $session) {
            $levelName = $session->getLevel()->getLevelName();

            // Get Year from the helper method we created earlier
            $firstDate = $session->getFirstDate();
            $year = $firstDate ? $firstDate->format('Y') : 'N/A';

            // Initialize Level Key
            if (!isset($grouped[$levelName])) {
                $grouped[$levelName] = [
                    'level' => $session->getLevel(),
                    'years' => []
                ];
            }

            $grouped[$levelName]['years'][$year][] = $session;
        }

        // 2. Logic to Name Sessions (Session 1, 2, 3) AND Sort for Display
        foreach ($grouped as $lvlKey => $levelData) {
            // Sort years descending (2025 before 2024)
            krsort($grouped[$lvlKey]['years']);

            foreach ($grouped[$lvlKey]['years'] as $year => $yearSessions) {
                // A. Sort ASC by date to determine "Session 1", "Session 2"
                usort($yearSessions, function ($a, $b) {
                    return $a->getFirstDate() <=> $b->getFirstDate();
                });

                // B. Assign the computed name to a dynamic property
                // (We can't save this to DB, so we inject it into the object at runtime)
                foreach ($yearSessions as $index => $session) {
                    $session->computedName = $translator->trans('Session') . ' ' . ($index + 1);
                }

                // C. Sort DESC for display (Most recent session at the top)
                // If you prefer to keep them 1, 2, 3 down the page, remove this block.
                usort($yearSessions, function ($a, $b) {
                    return $b->getFirstDate() <=> $a->getFirstDate();
                });

                // Save back to array
                $grouped[$lvlKey]['years'][$year] = $yearSessions;
            }
        }

        return $this->render('admin_session/index.html.twig', [
            'groupedSessions' => $grouped,
        ]);
    }

    #[Route('/new', name: 'app_session_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $session = new Session();
        $form = $this->createForm(SessionType::class, $session);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($session);
            $entityManager->flush();

            $this->addFlash('success', 'La session a été créée.');

            return $this->redirectToRoute('app_session_index');
        }

        return $this->render('admin_session/new.html.twig', [
            'session' => $session,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_session_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Session $session, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(SessionType::class, $session);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'La session a été modifiée.');

            return $this->redirectToRoute('app_session_index');
        }

        return $this->render('admin_session/edit.html.twig', [
            'session' => $session,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_session_delete', methods: ['POST'])]
    public function delete(Request $request, Session $session, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$session->getId(), $request->request->get('_token'))) {
            $entityManager->remove($session);
            $entityManager->flush();
            $this->addFlash('success', 'La session a été supprimée.');
        }

        return $this->redirectToRoute('app_session_index');
    }
}
