<?php

namespace App\Form\FormExtension;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class RepeatedPasswordType extends AbstractType
{
    public function getParent(): string
    {
        return RepeatedType::class;
    }

    /**
     * @param OptionsResolver $resolver
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'type'=> PasswordType::class,
            'invalid_message' => "Les mots de passe saisis ne correspondent pas",
            'required'        => true,
            'first_options'   => [
                'label' => "Mot de passe: ",
                'label_attr' => [
                    'title' => "Pour des raisons de sécurité, votre mot de passe doit contenir au minimum 12 caractères dont 1 lettre majuscule, 1 lettre minuscule, 1 chiffre et un caractère spécial (dans un ordre aléatoire)"
                ],
                'attr' => [
                    'pattern'   => "^(?=.*[a-zà-ÿ])(?=.*[A-ZÀ-Ý])(?=.*[0-9])(?=.*[^a-zà-ÿA-ZÀ-Ý0-9]).{12,}$",
                    'title'     => "Pour des raisons de sécurité, votre mot de passe doit contenir au minimum 12 caractères dont 1 lettre majuscule, 1 lettre minuscule, 1 chiffre et un caractère spécial (dans un ordre aléatoire)",
                    'maxlength' => 255
                ]
            ],
            "second_options" => [
                'label' => "Confirmer le mot de passe: ",
                'label_attr' => [
                    'title' => "Confirmez votre mot de passe"
                ],
                'attr' => [
                    'pattern'   => "^(?=.*[a-zà-ÿ])(?=.*[A-ZÀ-Ý])(?=.*[0-9])(?=.*[^a-zà-ÿA-ZÀ-Ý0-9]).{12,}$",
                    'title'     => "Confirmez votre mot de passe",
                    'maxlength' => 255
                ]
                
            ]
        ]);
    }
}