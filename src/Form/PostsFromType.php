<?php

namespace App\Form;

use App\Entity\Categories;
use App\Entity\Posts;
use App\Entity\User;
use Dom\Entity;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class PostsFromType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('contenu')
            ->add(
                'photo', FileType::class, [
                'label' => 'Photo',
                'mapped' => false,
                'required' => false,
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
            'data_class' => Posts::class,
            ]
        );
    }
}
