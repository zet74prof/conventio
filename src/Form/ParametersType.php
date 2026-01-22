<?php

namespace App\Form;

use App\Entity\Parameters;
use Symfony\Component\Form\AbstractType;
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
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Parameters::class]);
    }
}
