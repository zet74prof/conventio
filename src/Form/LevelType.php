<?php

namespace App\Form;

use App\Entity\Level;
use App\Entity\Professor;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class LevelType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('levelCode', TextType::class, [
                'label' => 'Code (ex: BTS SIO)',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Code',
                    'class' => 'form-control'
                ],
            ])
            ->add('levelName', TextType::class, [
                'label' => 'Libellé complet (ex: Services Informatiques aux Organisations)',
                'attr' => [
                    'placeholder' => 'Libellé',
                    'class' => 'form-control'
                ],
                'constraints' => [
                    new NotBlank(null, 'Veuillez saisir un libellé.'),
                ],
            ])
            ->add('referentProfessors', EntityType::class, [
                'class' => Professor::class,
                'choice_label' => function (Professor $professor) {
                    return $professor->getFullName();
                },
                'label' => 'Professeurs Principaux',
                'multiple' => true,  // <--- Allow multiple selection
                'expanded' => false, // Set to true for checkboxes, false for a multi-select box
                'by_reference' => false,
                'attr' => [
                    'class' => 'form-select',
                    'data-controller' => 'tom-select'
                ],
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Level::class,
        ]);
    }
}
