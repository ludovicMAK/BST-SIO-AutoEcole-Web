<?php

namespace App\Form;

use App\Entity\Categorie;
use App\Entity\Licence;
use App\Entity\Moniteur;
use App\Repository\CategorieRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LicenceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

            ->add('dateObtention')
            ->add('categorie', EntityType::class, [
                'class' => Categorie::class,
                'choices' => $options['entity_manager']->findBy(['statut' => 'actif']),
                'choice_label' => function(Categorie $categorie) {
                    return sprintf('%s', $categorie->getLibelle());
                }]
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Licence::class,
        ]);
        $resolver->setRequired('entity_manager');
    }
}
