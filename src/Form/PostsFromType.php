<?php

namespace App\Form;

use App\Entity\Categories;
use App\Entity\Posts;
use App\Entity\User;
use Dom\Entity;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PostsFromType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('contenu')
            /* ->add('date', null, [
                'widget' => 'single_text',
            ]) */
            ->add('cat', EntityType::class, [
                'class' => Categories::class,
                'choice_label' => 'name',
                'label' => 'Catégorie'
            ])
            ->add('userID', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'username',
                'label' => 'Utilisateur'
            ])
            //->add('photo')

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Posts::class,
        ]);
    }
}
