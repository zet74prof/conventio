<?php

namespace App\Form;

use App\Entity\Level;
use App\Entity\Professor;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProfessorProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('mobilePhone', TelType::class, [
                'label' => 'Numéro de téléphone',
                'required' => false,
                'attr' => ['placeholder' => 'ex: +33612345678'],
            ])
            ->add('levels', EntityType::class, [
                'class' => Level::class,
                'choice_label' => 'levelName',
                'label' => 'Classes accessibles',
                'multiple' => true,
                'expanded' => false, // Set to true if you prefer checkboxes
                'by_reference' => false, // Important for ManyToMany!
                'attr' => ['class' => 'form-select', 'style' => 'height: 200px;'],
                'help' => 'Maintenez Ctrl (ou Cmd) pour sélectionner plusieurs classes.',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Professor::class,
        ]);
    }
}
