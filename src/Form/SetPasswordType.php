<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;

class SetPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('password', PasswordType::class, [
                'label' => 'Mot de passe',
                'label_attr' => [
                    'class' => 'input-group-text w-25',
                ],
                'row_attr' => [
                    'class' => 'input-group mb-1 mt-1',
                ],
            ])
            ->add('passwordConfirm', PasswordType::class, [
                'label' => 'Confirmation',
                'label_attr' => [
                    'class' => 'input-group-text w-25',
                ],
                'row_attr' => [
                    'class' => 'input-group mb-1 mt-1',
                ],
            ])
        ;
    }
}