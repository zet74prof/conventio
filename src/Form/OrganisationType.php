<?php

namespace App\Form;

use App\Entity\Organisation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrganisationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // Identity
            ->add('name', TextType::class, ['label' => 'Nom de la structure'])
            ->add('siret', TextType::class, ['label' => 'Numéro SIRET'])
            ->add('website', TextType::class, ['label' => 'Site Web', 'required' => false])

            // HQ Address
            ->add('addressHq', TextType::class, ['label' => 'Adresse (Siège)'])
            ->add('postalCodeHq', TextType::class, ['label' => 'Code Postal'])
            ->add('cityHq', TextType::class, ['label' => 'Ville'])
            ->add('countryHq', TextType::class, ['label' => 'Pays'])

            // Responsible
            ->add('respName', TextType::class, ['label' => 'Nom du responsable'])
            ->add('respFunction', TextType::class, ['label' => 'Fonction'])
            ->add('respEmail', EmailType::class, ['label' => 'Email'])
            ->add('respPhone', TextType::class, ['label' => 'Téléphone'])

            // Insurance
            ->add('insuranceName', TextType::class, ['label' => 'Nom de l\'assurance RC'])
            ->add('insuranceContract', TextType::class, ['label' => 'Numéro de contrat'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Organisation::class]);
    }
}
