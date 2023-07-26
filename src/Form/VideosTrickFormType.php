<?php

namespace App\Form;

use App\Entity\VideosTrick;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Regex;

/**
 * VideoTrick form builder
 */
class VideosTrickFormType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('url', null, [
                'required' => true,
                'label' => false,
                'row_attr' => ['class' => 'trick-video-link-group'],
                'attr' => ['class' => 'trick-video-link'],
                'constraints' => [
                    new Regex([
                        'pattern' => '/^https?:\/\/(?:www\.)?youtube\.com\/embed\/[A-Za-z0-9_-]{11}$/',
                        'message' => 'The link is not valid.<br>https://www.youtube.com/embed/code',
                    ]),
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
            'data_class' => VideosTrick::class,
        ]);
    }
}
