<?php

namespace App\Form;

use App\Entity\User;
use App\Form\FormExtension\RepeatedPasswordType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('lastName', TextType::class, [
                'label' => "Nom: ",
                'required' => true,
                'attr' => [
                    'autofocus' => true,
                    'palceholder' => "Votre nom"
                ]
            ])
            ->add('firstName', TextType::class, [
                'label' => "Prénom: ",
                'required' => true,
                'attr' => [
                    'placeholder' => "Votre prénom"
                ]
            ])
            ->add('email', EmailType::class, [
                'label' => "Email: ",
                'required' => true,
                'attr' => [
                    'placeholder' => "Votre email"
                ]
            ])
            ->add('password', RepeatedPasswordType::class)
            ->add('agreeTerms', CheckboxType::class, [
                'label' => "J'accepte les conditions d'utilisations: ",
                'label_attr' => [
                    'class' => 'sameLine'
                ],
                'mapped' => false,
                'required' => true,
                'attr' => [
                    'class' => "sameLine inputCheck"
                ],
                'constraints' => [
                    new IsTrue([
                        'message' => 'Vous devez acceptez les condition d\'utilisation !',
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
