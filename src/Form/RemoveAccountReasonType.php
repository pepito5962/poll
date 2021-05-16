<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RemoveAccountReasonType extends AbstractType
{
    
    /**
     * Undocumented function
     *
     * @param FormBuilderInterface $builder
     * @param array<mixed> $options
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('reason', TextareaType::class, [
            "required" => true,
            "label" => "Pourquoi vous voulez suprimez ce compte"
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}