<?php

namespace App\Controller;

use App\Form\ProfilePasswordChangeType; // <--- Use the new form
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/settings')]
#[IsGranted('IS_AUTHENTICATED_FULLY')]
class SettingsController extends AbstractController
{
    #[Route('/password', name: 'app_settings_password')]
    public function changePassword(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $entityManager,
        TranslatorInterface $translator
    ): Response
    {
        $user = $this->getUser();

        // 1. Use the new ProfilePasswordChangeType
        $form = $this->createForm(ProfilePasswordChangeType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // 2. Hash the NEW password
            $encodedPassword = $passwordHasher->hashPassword(
                $user,
                $form->get('plainPassword')->getData()
            );

            $user->setPassword($encodedPassword);
            $entityManager->flush();

            $this->addFlash('success', $translator->trans('password.change.success', [], 'messages'));

            return $this->redirectToRoute('app_home');
        }

        return $this->render('settings/change_password.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
