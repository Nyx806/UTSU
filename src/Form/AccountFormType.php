<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;

class AccountFormType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add(
            'email',
            EmailType::class,
            [
                'constraints' => [
                  new NotBlank(['message' => 'Veuillez entrer un email']),
                  new Length(
                      [
                        'max' => 180,
                        'maxMessage' => 'L\'email ne peut pas dépasser {{ limit }} caractères',
                      ]
                  ),
                ],
              ]
        )
        ->add(
            'username',
            TextType::class,
            [
                'constraints' => [
                  new NotBlank(['message' => 'Veuillez entrer un nom d\'utilisateur']),
                  new Length(
                      [
                        'min' => 3,
                        'minMessage' => 'Votre nom d\'utilisateur doit contenir au moins {{ limit }} caractères',
                        'max' => 255,
                        'maxMessage' => 'Votre nom d\'utilisateur ne peut pas dépasser {{ limit }} caractères',
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
                        'mimeTypesMessage' => 'Veuillez télécharger une image valide (JPEG, PNG, GIF)',
                      ]
                  ),
                ],
                'attr' => [
                  'accept' => 'image/*',
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
