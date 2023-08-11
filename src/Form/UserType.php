<?php

namespace App\Form;

use App\Entity\User;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', TextType::class, [
                'label' => 'Email',
                'label_attr' => [
                    'class' => 'input-group-text w-25',
                ],
                'row_attr' => [
                    'class' => 'input-group mb-1 mt-1',
                ],
            ])
        ;

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $user = $event->getData();
            $form = $event->getForm();
            /*dd($user);*/
            if($user) {
                /*$form->add('password', PasswordType::class, [
                    'label' => 'Mot de passe',
                    'label_attr' => [
                        'class' => 'input-group-text w-25',
                    ],
                    'row_attr' => [
                        'class' => 'input-group mt-1',
                    ],
                ]);*/
            }
            else{
                $form->add('login', TextType::class, [
                    'label' => 'Login',
                    'label_attr' => [
                        'class' => 'input-group-text w-25',
                    ],
                    'row_attr' => [
                        'class' => 'input-group mb-1 mt-1',
                    ],
                ]);
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
