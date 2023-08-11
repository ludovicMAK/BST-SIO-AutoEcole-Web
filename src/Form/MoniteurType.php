<?php

namespace App\Form;

use App\Entity\Moniteur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MoniteurType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom',
                'label_attr' => [
                    'class' => 'input-group-text w-25',
                ],
                'row_attr' => [
                    'class' => 'input-group mb-1 mt-1',
                ],
            ])
            ->add('prenom', TextType::class, [
                'label' => 'Prenom',
                'label_attr' => [
                    'class' => 'input-group-text w-25',
                ],
                'row_attr' => [
                    'class' => 'input-group mb-1 mt-1',
                ],
            ])
            ->add('sexe', ChoiceType::class, [
                'choices' => [
                    'Homme' => "Homme",
                    'Femme' => "Femme",
                    'Autre' => "Autre",
                ],
                'label' => 'Sexe',
                'label_attr' => [
                    'class' => 'input-group-text w-25',
                ],
                'row_attr' => [
                    'class' => 'input-group mb-1 mt-1',
                ],
            ])
            ->add('dateDeNaissance', BirthdayType::class, [
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'placeholder' => [
                    'year' => 'Year',
                    'month' => 'Month',
                    'day' => 'Day',
                ],
                'label' => 'Date de naissance',
                'label_attr' => [
                    'class' => 'input-group-text w-25',
                ],
                'row_attr' => [
                    'class' => 'input-group mb-1 mt-1',
                ],
            ])
            ->add('adresse', TextType::class, [
                'label' => 'Adresse',
                'label_attr' => [
                    'class' => 'input-group-text w-25',
                ],
                'row_attr' => [
                    'class' => 'input-group mb-1 mt-1',
                ],
            ])
            ->add('codePostal', TextType::class, [
                'label' => 'Code Postal',
                'label_attr' => [
                    'class' => 'input-group-text w-25',
                ],
                'row_attr' => [
                    'class' => 'input-group mb-1 mt-1',
                ],
            ])
            ->add('ville', TextType::class, [
                'label' => 'Ville',
                'label_attr' => [
                    'class' => 'input-group-text w-25',
                ],
                'row_attr' => [
                    'class' => 'input-group mb-1 mt-1',
                ],
            ])
            ->add('telephone', TextType::class, [
                'label' => 'Téléphone',
                'label_attr' => [
                    'class' => 'input-group-text w-25',
                ],
                'row_attr' => [
                    'class' => 'input-group mb-1 mt-1',
                ],
            ])
            ->add('user', UserType::class, [
                'label' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Moniteur::class,
        ]);
    }
}
