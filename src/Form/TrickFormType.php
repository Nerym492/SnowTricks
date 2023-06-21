<?php

namespace App\Form;

use App\Entity\GroupTrick;
use App\Entity\Trick;
use App\Entity\VideosTrick;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

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
            ])
            ->add('imagesTricks', CollectionType::class, [
                'entry_type' => ImagesTrickFormType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
            ])
            ->add('videosTricks', CollectionType::class, [
                'entry_type' => VideosTrickFormType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
            ])
            ->add('description', null, [
                'attr' => [
                    'placeholder' => 'Description',
                    'class' => 'form-control trick-description',
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
