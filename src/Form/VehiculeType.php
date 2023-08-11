<?php

namespace App\Form;

use App\Entity\Categorie;
use App\Entity\Vehicule;

use App\Repository\CategorieRepository;
use Doctrine\DBAL\Types\IntegerType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VehiculeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $entitymanager = $options['entity_manager'];
        $builder
            ->add('immatriculation', TextType::class, [
                'label' => 'Immatriculation',
                'label_attr' => [
                    'class' => 'input-group-text w-25',
                ],
                'row_attr' => [
                    'class' => 'input-group mb-1',
                ],
            ])
            ->add('marque', TextType::class, [
                'label' => 'Marque',
                'label_attr' => [
                    'class' => 'input-group-text w-25',
                ],
                'row_attr' => [
                    'class' => 'input-group mb-1',
                ],
            ])
            ->add('modele', TextType::class, [
                'label' => 'Modèle',
                'label_attr' => [
                    'class' => 'input-group-text w-25',
                ],
                'row_attr' => [
                    'class' => 'input-group mb-1',
                ],
            ])
            ->add('annee', TextType::class, [
                'label' => 'Annee',
                'label_attr' => [
                    'class' => 'input-group-text w-25',
                ],
                'row_attr' => [
                    'class' => 'input-group mb-1',
                ],
            ])
            ->add('categorie', EntityType::class, [
                'class' => Categorie::class,
                'choices' => $options['entity_manager']->findBy(['statut' => 'actif']),
                'choice_label' => function(Categorie $categorie) {
                    return sprintf('%s', $categorie->getLibelle());
                },
                'label' => 'Catégorie',
                'label_attr' => [
                    'class' => 'input-group-text w-25',
                ],
                'row_attr' => [
                    'class' => 'input-group mb-1',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Vehicule::class,
        ]);
        $resolver->setRequired('entity_manager');
    }
}
