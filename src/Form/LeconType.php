<?php

namespace App\Form;

use App\Entity\Categorie;
use App\Entity\Lecon;
use App\Entity\Moniteur;
use App\Entity\Vehicule;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LeconType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('date')
            ->add('heure')
            ->add('moniteur', EntityType::class, [
                'class' => Moniteur::class,
                'choice_label' => 'nom',
                'label' => 'Sélectionnez un moniteur',
            ])
            ->add('vehicule', EntityType::class, [
                'class' => Vehicule::class,
                'choice_label' => 'marque',
                'label' => 'Sélectionnez une marque de véhicule',
            ])

        ;
    }


    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Lecon::class,
            'date_class'=>Moniteur::class,
        ]);
    }
}
