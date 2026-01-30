<?php

namespace App\Controller;

use App\Entity\Contract;
use App\Entity\Student;
use App\Entity\Tutor;
use App\Entity\User;
use App\Form\ContractInitiateType;
use App\Repository\SessionRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Uid\Uuid;

#[Route('/student/contract')]
#[IsGranted('ROLE_STUDENT')] // Assuming your student role
class StudentContractController extends AbstractController
{
    #[Route('/', name: 'app_student_contract_index')]
    public function index(): Response
    {
        /** @var Student $student */
        $student = $this->getUser();

        return $this->render('contract/index.html.twig', [
            'contracts' => $student->getContracts(),
        ]);
    }

    #[Route('/new', name: 'app_student_contract_new')]
    public function new(
        Request $request,
        EntityManagerInterface $em,
        MailerInterface $mailer,
        SessionRepository $sessionRepo,
        UserRepository $userRepo
    ): Response
    {
        /** @var Student $student */
        $student = $this->getUser();

        // 1. Check Profile Completion
        if (empty($student->getPersonalEmail())) {
            $this->addFlash('error', 'Veuillez compléter votre email personnel dans votre profil.');
            return $this->redirectToRoute('app_profile_edit');
        }

        $contract = new Contract();
        $contract->setStudent($student);

        // 2. Assign Session
        // Note: Logic assumes you have added 'active' to Session as discussed previously
        $level = $student->getLevel();
        if ($level) {
            // Find active session for this level
            // Ideally: $sessionRepo->findOneBy(['level' => $level, 'active' => true]);
            // Fallback if 'active' not yet implemented:
            $session = $sessionRepo->findOneBy(['level' => $level], ['id' => 'DESC']);

            if ($session) {
                $contract->setSession($session);
            } else {
                $this->addFlash('error', 'Aucune session trouvée pour votre formation.');
                return $this->redirectToRoute('app_student_contract_index');
            }
        }

        $form = $this->createForm(ContractInitiateType::class, $contract);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // 3. Handle Tutor Creation/Linking
            $tutorEmail = $form->get('tutorEmail')->getData();
            $tutorFirstname = $form->get('tutorFirstname')->getData();
            $tutorLastname = $form->get('tutorLastname')->getData();

            $existingUser = $userRepo->findOneBy(['email' => $tutorEmail]);

            if ($existingUser) {
                if ($existingUser instanceof Tutor) {
                    $contract->setTutor($existingUser);
                } else {
                    $this->addFlash('error', 'Cet email est déjà utilisé par un compte qui n\'est pas un tuteur.');
                    return $this->render('contract/new.html.twig', ['form' => $form]);
                }
            } else {
                // Create new Tutor
                $tutor = new Tutor();
                $tutor->setEmail($tutorEmail);
                $tutor->setFirstname($tutorFirstname);
                $tutor->setLastname($tutorLastname);
                // Set dummy password or leave null (User.php password is nullable)
                // $tutor->setPassword(...);
                $tutor->setIsVerified(false);
                $em->persist($tutor);

                $contract->setTutor($tutor);
            }

            // 4. Contract Setup
            $token = Uuid::v4()->toBase58();
            $contract->setSharingToken($token);
            $contract->setTokenExpDate(new \DateTime('+7 days'));
            $contract->setStatus(0); // 0 = STARTED

            $em->persist($contract);
            $em->flush();

            // 5. Send Email
            $email = (new TemplatedEmail())
                ->from(new Address('stages@conventio.com', 'Service des Stages'))
                ->to($contract->getTutor()->getEmail())
                ->subject('Convention de stage - ' . $student->getFullName())
                ->htmlTemplate('emails/tutor_contract_request.html.twig')
                ->context([
                    'contract' => $contract,
                    'token' => $token,
                    'studentName' => $student->getFullName(),
                    'tutorName' => $contract->getTutor()->getFullName(),
                ]);

            $mailer->send($email);

            $this->addFlash('success', 'La demande a été envoyée au tuteur.');
            return $this->redirectToRoute('app_student_contract_index');
        }

        return $this->render('contract/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
