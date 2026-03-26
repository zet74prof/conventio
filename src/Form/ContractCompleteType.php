<?php

namespace App\Form;

use App\Entity\Contract;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContractCompleteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // 1. Embed Organisation Form
            ->add('organisation', OrganisationType::class, [
                'label' => false,
            ])

            // 2. Internship Location
            ->add('sameAddress', CheckboxType::class, [
                'mapped' => false, // Not stored in DB directly
                'label' => 'L\'adresse du stage est identique au siège de l\'organisme',
                'required' => false,
                'attr' => ['class' => 'form-check-input js-same-address-toggle'],
            ])

            ->add('placeNameInternship', TextType::class, ['label' => 'Lieu du stage', 'required' => false])
            ->add('addressInternship', TextType::class, ['label' => 'Adresse du stage', 'required' => false])
            ->add('postalCodeInternship', TextType::class, ['label' => 'Code Postal', 'required' => false])
            ->add('cityInternship', TextType::class, ['label' => 'Ville', 'required' => false])
            ->add('countryInternship', TextType::class, ['label' => 'Pays', 'required' => false])

            // 3. Logistics
            ->add('deplacement', CheckboxType::class, ['label' => 'Des déplacements sont-ils prévus ?', 'required' => false])
            ->add('transportFeeTaken', CheckboxType::class, ['label' => 'Prise en charge des frais de transport ?', 'required' => false])
            ->add('lunchTaken', CheckboxType::class, ['label' => 'Prise en charge des repas ?', 'required' => false])
            ->add('hostTaken', CheckboxType::class, ['label' => 'Hébergement fourni ?', 'required' => false])
            ->add('bonus', CheckboxType::class, ['label' => 'Gratification prévue ?', 'required' => false])

            // 4. Activities
            ->add('plannedActivities', TextareaType::class, [
                'label' => 'Activités confiées au stagiaire',
                'attr' => ['rows' => 6],
            ])

            // 5. Work Hours
            ->add('workHours', HiddenType::class, [
                'attr' => ['class' => 'js-work-hours-input'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Contract::class]);
    }
}
