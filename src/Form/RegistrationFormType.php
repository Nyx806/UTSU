<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\Extension\Core\Type\EmailType;

class RegistrationFormType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add(
            'email',
            EmailType::class,
            [
            'required' => true,
            'constraints' => [
              new NotBlank(['message' => 'Please enter an email']),
              new Length(
                  [
                      'max' => 180,
                      'maxMessage' => 'The email cannot exceed {{ limit }} characters',
                    ]
              ),
            ],
            ]
        )
        ->add(
            'username',
            TextType::class,
            [
            'required' => true,
            'constraints' => [
              new NotBlank(['message' => 'Please enter a username']),
              new Length(
                  [
                      'min' => 3,
                      'minMessage' => 'Your username should be at least {{ limit }} characters',
                      'max' => 255,
                      'maxMessage' => 'Your username cannot exceed {{ limit }} characters',
                    ]
              ),
            ],
            ]
        )
        ->add(
            'pp_img',
            FileType::class,
            [
            'required' => false,
            'mapped' => false,
            'constraints' => [
              new File(
                  [
                      'maxSize' => '2M',
                      'mimeTypes' => [
                        'image/jpeg',
                        'image/png',
                        'image/gif',
                      ],
                      'mimeTypesMessage' => 'Please upload a valid image (JPEG, PNG, GIF)',
                    ]
              ),
            ],
            'attr' => [
              'accept' => 'image/*',
            ],
            ]
        )
        ->add(
            'agreeTerms',
            CheckboxType::class,
            [
            'mapped' => false,
            'constraints' => [
              new IsTrue(['message' => 'You should agree to our terms.']),
            ],
            ]
        )
        ->add(
            'plainPassword',
            PasswordType::class,
            [
            'mapped' => false,
            'attr' => ['autocomplete' => 'new-password'],
            'constraints' => [
              new NotBlank(['message' => 'Please enter a password']),
              new Length(
                  [
                      'min' => 6,
                      'minMessage' => 'Your password should be at least {{ limit }} characters',
                      'max' => 4096,
                    ]
              ),
            ],
            ]
        );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
            'data_class' => User::class,
            ]
        );
    }
}
