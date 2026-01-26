<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ProfilePasswordChangeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // 1. The Current Password Field with automatic validation
            ->add('currentPassword', PasswordType::class, [
                'mapped' => false,
                'label' => 'Mot de passe actuel',
                'attr' => ['autocomplete' => 'current-password'],
                'constraints' => [
                    new NotBlank(),
                    new UserPassword(['message' => 'password.change.wrong_current']),
                ],
            ])
            // 2. The New Password Field (Repeated)
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'first_options' => [
                    'attr' => ['autocomplete' => 'new-password'],
                    'label' => 'Nouveau mot de passe',
                ],
                'second_options' => [
                    'attr' => ['autocomplete' => 'new-password'],
                    'label' => 'Répéter le mot de passe',
                ],
                'invalid_message' => 'Les mots de passe doivent correspondre.',
                'mapped' => false,
                'constraints' => [
                    new NotBlank(null, 'Veuillez entrer un mot de passe',),
                    new Length(null, 6, 4096, null, null, null,'Votre mot de passe doit faire au moins {{ limit }} caractères'),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
