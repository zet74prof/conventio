<?php

namespace App\Controller;

use App\Entity\Professor;
use App\Entity\Student;
use App\Form\ProfessorProfileType;
use App\Form\ProfilePasswordChangeType;
use App\Form\StudentProfileType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/profile')]
#[IsGranted('IS_AUTHENTICATED_FULLY')]
class ProfileController extends AbstractController
{
    #[Route('/', name: 'app_profile_edit')]
    public function edit(
        Request $request,
        EntityManagerInterface $entityManager,
        TranslatorInterface $translator
    ): Response
    {
        $user = $this->getUser();
        $form = null;
        $template = 'profile/edit.html.twig'; // We can use a shared template

        // 1. Determine User Type and Create Form
        if ($user instanceof Student) {
            $form = $this->createForm(StudentProfileType::class, $user);
        } elseif ($user instanceof Professor) {
            $form = $this->createForm(ProfessorProfileType::class, $user);
        } else {
            // Handle regular users or admins if they have different profiles
            // For now, redirect or show error
            return $this->redirectToRoute('app_home');
        }

        $form->handleRequest($request);

        // 2. Handle Submission
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', $translator->trans('profile.update.success', [], 'messages'));

            return $this->redirectToRoute('app_profile_edit');
        }

        return $this->render($template, [
            'form' => $form->createView(),
            'user_type' => ($user instanceof Student) ? 'Student' : 'Professor'
        ]);
    }

    #[Route('/changepassword', name: 'app_profile_changepassword')]
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

        return $this->render('profile/change_password.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
