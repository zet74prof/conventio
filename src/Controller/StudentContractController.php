<?php

namespace App\Controller;

use App\Entity\Contract;
use App\Entity\Student;
use App\Entity\Tutor;
use App\Form\ContractInitiateType;
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
#[IsGranted('ROLE_STUDENT')]
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

        $level = $student->getLevel();

        $form = $this->createForm(ContractInitiateType::class, $contract, [
            'level' => $level
        ]);
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
                $tutor->setIsVerified(false);
                $em->persist($tutor);

                $contract->setTutor($tutor);
            }

            // 4. Contract Setup
            $token = Uuid::v4()->toBase58();
            $contract->setSharingToken($token);
            $contract->setTokenExpDate(new \DateTime('+7 days'));
            $contract->setStatus(Contract::STATUS_STARTED);

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

    #[Route('/{id}/request-approval', name: 'app_student_contract_request_approval', methods: ['POST'])]
    public function requestApproval(
        Contract $contract,
        EntityManagerInterface $em,
        MailerInterface $mailer,
        Request $request
    ): Response
    {
        // Security: Ensure user owns the contract
        if ($contract->getStudent() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        // Logic: Can only request if currently filled by tutor (Status 1)
        if ($contract->getStatus() !== Contract::STATUS_FILLED_BY_TUTOR) {
            $this->addFlash('error', 'La convention ne peut pas être soumise à validation pour le moment.');
            return $this->redirectToRoute('app_student_contract_index');
        }

        if ($this->isCsrfTokenValid('request_approval'.$contract->getId(), $request->request->get('_token'))) {

            // 1. Change Status
            $contract->setStatus(Contract::STATUS_APPROVAL_REQUESTED);
            $em->flush();

            // 2. Find Professors (via InternshipDate -> Level -> ReferentProfessors)
            $internshipDate = $contract->getInternshipDate();
            $level = $internshipDate ? $internshipDate->getLevel() : null;
            $professors = $level ? $level->getReferentProfessors() : [];

            $emailsSent = 0;

            foreach ($professors as $professor) {
                if ($professor->getEmail()) {
                    // 3. Send Email to each professor
                    $email = (new TemplatedEmail())
                        ->from(new Address('stages@conventio.com', 'Conventio'))
                        ->to($professor->getEmail())
                        ->subject('Validation requise : Convention de ' . $contract->getStudent()->getFullName())
                        ->htmlTemplate('emails/professor_approval_request.html.twig')
                        ->context([
                            'contract' => $contract,
                            'professor' => $professor,
                            'student' => $contract->getStudent()
                        ]);

                    $mailer->send($email);
                    $emailsSent++;
                }
            }

            if ($emailsSent > 0) {
                $this->addFlash('success', 'La demande de validation a été envoyée aux professeurs référents.');
            } else {
                $this->addFlash('warning', 'Convention soumise, mais aucun professeur référent n\'a été trouvé pour l\'envoi du mail.');
            }
        }

        return $this->redirectToRoute('app_student_contract_index');
    }
}
