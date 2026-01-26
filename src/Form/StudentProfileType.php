<?php

namespace App\Form;

use App\Entity\Level;
use App\Entity\Student;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StudentProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('personalEmail', EmailType::class, [
                'label' => 'Email personnel',
                'required' => false,
                'attr' => ['placeholder' => 'ex: mon.email@gmail.com'],
            ])
            ->add('level', EntityType::class, [
                'class' => Level::class,
                'choice_label' => 'levelName', // Or whatever property displays the name nicely
                'label' => 'Ma classe / Formation',
                'placeholder' => 'Sélectionnez votre formation',
                'attr' => ['class' => 'form-select'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Student::class,
        ]);
    }
}
