<?php

namespace App\Form;

use App\Entity\GroupTrick;
use App\Entity\Trick;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class TrickFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', null, [
                'attr' => [
                    'placeholder' => 'Name',
                    'class' => 'form-control',
                ],
                'constraints' => [
                    new NotBlank(message: 'Please enter a trick name'),
                    new Length(
                        max: 50,
                        maxMessage: 'The trick name must not exceed 50 characters.',
                    ),
                ],
            ])
            ->add('imagesTricks', CollectionType::class, [
                'entry_type' => ImagesTrickFormType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'row_attr' => ['class' => 'hidden'],
            ])
            ->add('videosTricks', CollectionType::class, [
                'entry_type' => VideosTrickFormType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'row_attr' => ['class' => 'hidden'],
            ])
            ->add('description', null, [
                'attr' => [
                    'placeholder' => 'Description',
                    'class' => 'form-control trick-description',
                ],
                'constraints' => [
                    new NotBlank(message: 'Please enter a description'),
                ],
            ])
            ->add('group_trick', EntityType::class, [
                'class' => GroupTrick::class,
                'choice_label' => 'name',
                'placeholder' => 'Select a trick group',
                'attr' => [
                    'class' => 'form-select',
                ],
                'required' => true,
                'constraints' => [
                    new NotBlank(message: 'Please select a trick group'),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Trick::class,
            'image_choices' => [],
        ]);
    }
}
