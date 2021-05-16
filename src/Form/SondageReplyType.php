<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SondageReplyType extends AbstractType
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
        $builder->add('answer0', CheckboxType::class, [
                    "required" => false
                ])
                ->add('answer1', CheckboxType::class, [
                    "required" => false
                ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}