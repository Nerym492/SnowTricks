<?php

namespace App\Form;

use App\Entity\Comment;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Comment form builder
 */
class CommentFormType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('content', null, [
                'label' => false,
                'required' => true,
                'attr' => [
                    'class' => 'form-control',
                    'aria-label' => 'comment content',
                    'placeholder' => 'Your comment',
                    'rows' => '5',
                ],
                'constraints' => [
                    new NotBlank(message: 'The comment cannot be empty.'),
                ],
            ])
        ;
    }

    /**
     * @param OptionsResolver $resolver
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Comment::class,
        ]);
    }
}
