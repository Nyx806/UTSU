<?php

namespace App\Form;

use App\Entity\Commentaires;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ComFromType extends AbstractType {

  public function buildForm(FormBuilderInterface $builder, array $options): void {
    $builder
      ->add('contenu')
      ->add(
              'img',
              FileType::class,
              [
                'label' => 'Photo',
                'mapped' => FALSE,
                'required' => FALSE,
                'attr' => [
                  'accept' => 'image/*',
                ],
              ]
          )
      ->add(
              'video',
              FileType::class,
              [
                'label' => 'Video',
                'mapped' => FALSE,
                'required' => FALSE,
                'attr' => [
                  'accept' => 'video/*',
                ],
              ]
          );
  }

  public function configureOptions(OptionsResolver $resolver): void {
    $resolver->setDefaults(
          [
            'data_class' => Commentaires::class,
          ]
      );
  }

}
