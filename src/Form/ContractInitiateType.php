<?php

namespace App\Form;

use App\Entity\Contract;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class ContractInitiateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // Fields are not mapped because we need to find/create the Tutor entity manually
            ->add('tutorFirstname', TextType::class, [
                'mapped' => false,
                'label' => 'Prénom du tuteur',
                'constraints' => [new NotBlank()],
            ])
            ->add('tutorLastname', TextType::class, [
                'mapped' => false,
                'label' => 'Nom du tuteur',
                'constraints' => [new NotBlank()],
            ])
            ->add('tutorEmail', EmailType::class, [
                'mapped' => false,
                'label' => 'Email du tuteur',
                'help' => 'Le lien de remplissage sera envoyé à cette adresse.',
                'constraints' => [new NotBlank()],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Contract::class,
        ]);
    }
}
