<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

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
                    new File(
                        maxSize: '600k',
                        mimeTypes: [
                            'image/webp',
                            'image/jpeg',
                            'image/png',
                        ],
                        maxSizeMessage: 'The file is too large. Maximum size allowed is {{ limit }}kb.',
                        mimeTypesMessage: 'Please upload a WEBP, JPEG or PNG file.',
                    ),
                ],
            ])
            ->add('pseudo', null, [
                'row_attr' => ['class' => 'form-floating'],
                'attr' => ['class' => 'form-control', 'placeholder' => 'Pseudo'],
                'constraints' => [
                    new NotBlank(message: 'Please enter a pseudo.'),
                    new Length(
                        min: 3,
                        max: 40,
                        minMessage: 'The pseudo must be at least 3 characters long',
                        maxMessage: 'The pseudo must not exceed 40 characters.',
                    ),
                ],
            ])
            ->add('mail', null, [
                'row_attr' => ['class' => 'form-floating'],
                'attr' => ['class' => 'form-control', 'placeholder' => 'Mail'],
                'constraints' => [
                    new NotBlank(message: 'Please enter an email.'),
                    new Email(message: 'This email is not valid'),
                ],
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'label' => 'By ticking this box, I consent to the use of my personal data. '.
                    '<span class="required-checkbox">*</span>',
                'label_html' => true,
                'mapped' => false,
                'row_attr' => ['class' => 'group-gdpr-checkbox'],
                'attr' => ['class' => 'form-check-input'],
                'constraints' => [
                    new IsTrue(message: 'You should agree to our terms.'),
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
                    new NotBlank(message: 'Please enter a password.'),
                    new Regex(
                        pattern: '/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@$!%*?&]).{8,}$/',
                        message: 'Minimum eight characters, at least one uppercase letter, one lowercase letter, '.
                        'one number and one special character.'
                    ),
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
