<?php

namespace App\Form;

use App\Entity\Level;
use App\Entity\Session;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SessionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('active', CheckboxType::class, [
                'label' => 'Session active',
                'required' => false,
                'help' => 'Décochez cette case pour désactiver/archiver cette session.',
                'attr' => ['class' => 'form-check-input'],
                'row_attr' => ['class' => 'form-switch mb-3'], // Bootstrap Switch style
            ])
            ->add('level', EntityType::class, [
                'class' => Level::class,
                'choice_label' => 'levelName',
                'label' => 'Formation / Niveau',
                'attr' => ['class' => 'form-select'],
            ])
            ->add('sessionDates', CollectionType::class, [
                'entry_type' => SessionDateType::class,
                'entry_options' => ['label' => false],
                'label' => false,
                'allow_add' => true,    // Allow JS to add new rows
                'allow_delete' => true, // Allow JS to remove rows
                'by_reference' => false,// Important for OneToMany persistence
                'prototype' => true,    // Generates the HTML template for JS
                'attr' => [
                    'class' => 'session-dates-collection',
                ],
                'error_bubbling' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Session::class,
        ]);
    }
}
