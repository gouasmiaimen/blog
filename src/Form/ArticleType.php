<?php

namespace App\Form;

use App\Entity\Categorie;
use App\Entity\Article;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;



class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre')
            ->add('contenu')
            ->add('categorie', EntityType::class, [
                'class' => Categorie::class,
                'choice_label' => 'titre'
            ])
            ->add('imageFile', VichImageType::class, [
                'required' => false,
                 'allow_delete' => false,
                 'download_uri' => false,
                 'image_uri' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
        ]);
    }
}
