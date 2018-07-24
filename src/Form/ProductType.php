<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Product;
use App\Entity\Region;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, array('label'=> "Titre de l'annonce"))
            ->add('description', TextType::class, array('label'=> "Description"))
            ->add('releaseOn', DateType::class, array('label' => "Date de mise en ligne"))
            ->add('autor', TextType::class, array('label' => 'Nom du vendeur'))
            ->add('pics', FileType::class, array('data_class' => null, 'label' => 'Photo de votre annonce'))
            ->add('region', EntityType::class, array(
                'class' => Region::class,
                'choice_label' => 'region'))
            ->add('category', EntityType::class, array(

                'class' => Category::class,
                'choice_label' => "categories"
            ))
            ->add('details', TextType::class,array('label' => 'Détails'))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
