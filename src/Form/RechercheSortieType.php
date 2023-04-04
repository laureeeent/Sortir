<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\RechercheSortie;
use App\Entity\Sortie;
use EasyCorp\Bundle\EasyAdminBundle\Form\Filter\Type\BooleanFilterType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RechercheSortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('campus', EntityType::class, [
                'class' => Campus::class,
                'choice_label' => 'nom',
                'placeholder' => "Tout les campus",
                'required' => false
            ])
            ->add("nomSortieContient")
            ->add("dateAPartirDe", DateTimeType::class, [
                'label'=>'A partir du ',
                'html5' => true,
                'widget' => 'single_text',
                'mapped' => true,
                'required'=>false,
            ])
            ->add("dateJusquA", DateTimeType::class, [
                'label'=>"Jusqu'au ",
                'html5' => true,
                'widget' => 'single_text',
                'mapped' => true,
                'required'=>false,
            ])
            ->add("isOrganisateur", BooleanFilterType::class)
            ->add("isInscrit", BooleanFilterType::class)
            ->add("isNonInscrit", BooleanFilterType::class)
            ->add("sortiePassee", BooleanFilterType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => RechercheSortie::class,
            'method' => 'GET',
            'csrf_protection' => false
        ]);
    }

    public function getBlockPrefix() : string
    {
        return '';
    }
}