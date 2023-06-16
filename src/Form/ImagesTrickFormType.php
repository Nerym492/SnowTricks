<?php

namespace App\Form;

use App\Entity\ImagesTrick;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class ImagesTrickFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('fileName', null, [
                'label' => false,
                'required' => false,
                'row_attr' => ['class' => 'hidden', 'aria-label' => 'File name'],
            ])
            ->add('file', FileType::class, [
                'label' => false,
                'mapped' => false,
                'required' => false,
                'attr' => ['class' => 'trick-form-file', 'aria-label' => 'File selection input'],
                'row_attr' => ['class' => 'image-file-actions'],
                'constraints' => [
                    new File([
                        'maxSize' => '600k',
                        'maxSizeMessage' => 'The file is too large. Maximum size allowed is {{ limit }}.',
                        'mimeTypes' => [
                            'image/webp',
                            'image/jpeg',
                            'image/png',
                        ],
                        'mimeTypesMessage' => 'Please upload a WEBP, JPEG or PNG file.',
                    ]),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ImagesTrick::class,
        ]);
    }
}
