<?php

namespace App\Form;

use App\Entity\Parameters;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ParametersType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('studentEmailDomain', TextType::class, ['label' => 'Domaine Email Étudiants (ex: @lycee-faure.fr)'])
            ->add('professorEmailDomain', TextType::class, ['label' => 'Domaine Email Professeurs (ex: @ac-grenoble.fr)'])
            ->add('provisorName', TextType::class, ['label' => 'Nom du Proviseur', 'required' => false])
            ->add('provisorEmail', TextType::class, ['label' => 'Email du Proviseur', 'required' => false])
            ->add('provisorMobilePhone', TelType::class, ['label' => 'Téléphone du Proviseur', 'required' => false, 'attr' => ['placeholder' => 'ex: +33612345678']])
            ->add('schoolAddress', TextareaType::class, ['label' => 'Adresse de l\'établissement', 'required' => false, 'attr' => ['rows' => 4]])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Parameters::class]);
    }
}
