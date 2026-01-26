<?php

namespace App\Form;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class RegistrationFormType extends AbstractType
{
    // 1. Inject the UserRepository
    public function __construct(private UserRepository $userRepository)
    {
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'attr' => ['class' => 'form-control', 'placeholder' => 'name@example.com'],
                'constraints' => [
                    // 2. Add the custom check
                    new Callback([$this, 'validateEmailUniqueness'])
                ],
            ])
            ->add('firstname', TextType::class, [
                'label' => 'Prénom', // "First Name"
                'attr' => ['class' => 'form-control', 'placeholder' => 'Prénom']
            ])
            ->add('lastname', TextType::class, [
                'label' => 'Nom de famille', // "Surname"
                'attr' => ['class' => 'form-control', 'placeholder' => 'Nom']
            ])
            ->add('plainPassword', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'label' => 'Mot de passe', // "Password"
                'attr' => ['autocomplete' => 'new-password', 'class' => 'form-control'],
                'constraints' => [
                    new NotBlank(null, 'Veuillez entrer un mot de passe',),
                    new Length(null, 6, 4096, null, null, null,'Votre mot de passe doit faire au moins {{ limit }} caractères'),
                ],
            ])
        ;
    }

    public function validateEmailUniqueness($email, ExecutionContextInterface $context): void
    {
        // If email is empty, let standard validators handle it
        if (empty($email)) {
            return;
        }

        // Check database
        if ($this->userRepository->findOneBy(['email' => $email])) {
            $context->buildViolation('register.error.email_exists')
                ->atPath('email')
                ->addViolation();
        }
    }
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => null,
        ]);
    }
}
