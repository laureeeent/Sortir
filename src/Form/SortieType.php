<?php

namespace App\Form;

use App\Entity\Lieu;
use App\Entity\Participant;
use App\Entity\Sortie;
use App\Entity\Ville;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SortieType extends AbstractType
{

    private $em;

    public function __construct(EntityManagerInterface $em) {
        $this->em = $em;
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            ->add('dateHeureDebut')
            ->add('duree')
            ->add('dateLimiteInscription')
            ->add('nbInscriptionMax')
            ->add('infosSortie')
        ;

        $builder->addEventListener(FormEvents::PRE_SET_DATA, [$this, 'onPreSetData']);
        $builder->addEventListener(FormEvents::PRE_SUBMIT, [$this, 'onPreSubmit']);
    }

    protected function addElements(FormInterface $form, Ville $ville = null) {
        $form->add('ville', EntityType::class, [
            "required" => true,
            "data" => $ville,
            'placeholder' => "Veuillez choisir une ville...",
            'choice_label' => 'nom',
            'class' => Ville::class

        ]);

        $lieux = array();

        if ($ville) {
            $lieuRepository = $this->em->getRepository(Lieu::class);

            $lieux = $lieuRepository->createQueryBuilder("l")
                ->where("l.ville = :villeid")
                ->setParameter("villeid", $ville->getId())
                ->getQuery()
                ->getResult();
        }

        $form->add('lieu', EntityType::class, [
            'required' => true,
            'placeholder' => 'Veuillez choisir un lieu ...',
            'class' => Lieu::class,
            'choices' => $lieux
        ]);
    }

    function onPreSubmit(FormEvent $event) {
        $form = $event->getForm();
        $data = $event->getData();

        $ville = $this->em->getRepository(Ville::class)->find($data['ville']);

        $this->addElements($form, $ville);
    }

    function onPreSetData(FormEvent $event) {
        $sortie = $event->getData();
        $form = $event->getForm();

        $ville = $sortie->getVille() ? $sortie->getVille() : null;

        $this->addElements($form, $ville);
    }
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
