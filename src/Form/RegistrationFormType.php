<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('profilePictureFile', FileType::class, [
                'label' => false,
                'mapped' => false,
                'required' => false,
                'attr' => ['aria-label' => 'profile picture file'],
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
            ])
            ->add('profilePhoto', HiddenType::class, [
                'label' => false,
                'constraints' => [
                    new NotBlank(message: 'Profile picture cannot be empty'),
                    new Length(max: 255, maxMessage: 'Profile photo link cannot exceed 255 characters'),
                ],
            ])
            ->add('pseudo', null, [
                'row_attr' => ['class' => 'form-floating'],
                'attr' => ['class' => 'form-control', 'placeholder' => 'Pseudo'],
            ])
            ->add('mail', null, [
                'row_attr' => ['class' => 'form-floating'],
                'attr' => ['class' => 'form-control', 'placeholder' => 'Mail'],
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'label' => 'By ticking this box, I consent to the use of my personal data.',
                'mapped' => false,
                'row_attr' => ['class' => 'group-gdpr-checkbox'],
                'attr' => ['class' => 'form-check-input'],
                'constraints' => [
                    new IsTrue([
                        'message' => 'You should agree to our terms.',
                    ]),
                ],
            ])
            ->add('plainPassword', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'label' => 'Password',
                'row_attr' => ['class' => 'form-floating'],
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Password',
                    'autocomplete' => 'new-password',
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a password',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Your password should be at least {{ limit }} characters',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
