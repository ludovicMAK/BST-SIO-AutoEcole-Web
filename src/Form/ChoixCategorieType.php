<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

class ChoixCategorieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', ChoiceType::class, [
                'label' => 'Nom',
                'label_attr' => [
                    'class' => 'input-group-text w-25',
                ],
                'row_attr' => [
                    'class' => 'input-group mb-1',
                ],
            ])
        ;
    }
}