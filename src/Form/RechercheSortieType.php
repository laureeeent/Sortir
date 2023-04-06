<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\RechercheSortie;
use App\Entity\Sortie;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
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
            ->add("nomSortieContient", TextType::class, [
                'label'=> 'Le nom de la sortie contient',
                'required'=> false
            ])
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
            ->add("isOrganisateur", CheckboxType::class,[
                'label'=> 'Sorties dont je suis l\'organisateur/trice',
                'required'=> false
            ])
            ->add("isInscrit", CheckboxType::class,[
                'label'=> 'Sorties auxquelles je suis inscrit/e',
                'required'=> false
            ])
            ->add("isNonInscrit", CheckboxType::class,[
                'label'=> 'Sorties auxquelles je suis ne pas inscrit/e',
                'required'=> false
            ])
            ->add("sortiePassee", CheckboxType::class,[
                'label'=> 'Sorties passées',
                'required'=> false
            ])
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
