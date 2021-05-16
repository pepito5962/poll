<?php

namespace App\Form;

use App\Entity\Question;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class SondageType extends AbstractType
{
     /**
      * @param FormBuilderInterface $builder
      * @param array<mixed> $options
      * @return void
      */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('question', TextareaType::class, [
                    'label' => "Votre question: ",
                    'required' => true,
                    'constraints' => [
                        new NotBlank()
                    ],
                    'attr' => [
                        'placeholder' => "Votre question",
                        'autofocus' => true
                    ]
                ])
                ->add('isMultipleChoice', CheckboxType::class,[
                    'label' => "Choix multiple possible ?",
                    'required' => false,
                ])
                ->add('endDate', DateType::class, [
                    'label' => "Date de fin pour votre sondage: ",
                    'required' => true,
                    'input'  => 'datetime_immutable',
                    'widget' => "choice",
                    'format' => "dd-MM-yyyy",
                    'html5' => false,
                    'model_timezone' => "Europe/Paris",
                    'years' => range(date('Y'), date('Y') + 20 ),
                ])
                ->add('answer0', TextType::class, [
                    'label' => "Premiere réponse possible",
                    'mapped' => false,
                    'required' => true,
                    'constraints' => [
                        new NotBlank()
                    ],
                    'attr' => [
                        'placeholder' => "Réponse 1",
                    ]
                ])
                ->add('answer1', TextType::class, [
                    'label' => "Deuxième réponse possible",
                    'required' => true,
                    'mapped' => false,
                    'constraints' => [
                        new NotBlank()
                    ],
                    'attr' => [
                        'placeholder' => "Réponse 2"
                    ]
                ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Question::class
        ]);
    }
}