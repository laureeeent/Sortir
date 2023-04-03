<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Entity\RechercheSortie;
use App\Entity\Sortie;
use App\Entity\Ville;
use App\Form\RechercheSortieType;
use App\Form\SortieType;
use App\Repository\EtatRepository;
use App\Repository\SortieRepository;
use App\Repository\VilleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/sortie')]
class SortieController extends AbstractController
{
    #[IsGranted('ROLE_USER')]
    #[Route('/list', name: 'sortie_list')]
    public function list(
        Request $request,
        SortieRepository $sortieRepository
    ): Response
    {

        $rechercheSortie = new RechercheSortie();
        $rechercheForm = $this->createForm(RechercheSortieType::class, $rechercheSortie);
        $rechercheForm->handleRequest($request);
        $rechercheSortie->setParticipant($this->getUser());
        $sorties = $sortieRepository->findSearch($rechercheSortie);
        return $this->render('sortie/list.html.twig',
            compact("rechercheForm", "sorties")
        );

    }


    #[Route('/ajouter', name: 'sortie_ajouter')]
    public function ajouter (

        Request                $request,
        EntityManagerInterface $entityManager,
        EtatRepository         $etatRepository
    ): Response
    {
        $sortie = new Sortie();

        $participant = $entityManager->find(Participant::class, $this->getUser()->getId());
        $etatCree = $etatRepository->findOneBy(['libelle' => 'Créée']);
        $sortieForm = $this->createForm(SortieType::class, $sortie);
        $sortieForm->handleRequest($request);
        if ($sortieForm->isSubmitted() && $sortieForm->isValid()) {
            try {
                $sortie->setEtat($etatCree);

                $sortie->setOrganisateur($participant);
                $sortie->setCampusOrganisateur($participant->getCampus());
                $entityManager->persist($sortie);
                $entityManager->flush();
                $this->addFlash('succes', 'La sortie a bien été insérée');
                return $this->redirectToRoute('sortie_list');
            }
            catch (\Exception $exception) {
                $this->addFlash('echec', 'La sortie n\'a pas été insérée');
                //changer la route et remettre '/'
                return $this->redirectToRoute('sortie_list');
            }
        }
        return $this->render('sortie/ajouter.html.twig',
            compact('sortieForm')
        );
    }

    #[Route('/detail/{sortie}', name: 'sortie_detail')]
    public function detail(
        Sortie $sortie,
        SortieRepository $sortieRepository
    ): Response
    {
        $participants = $sortie->getParticipants();
        $nbParticipants = $participants->count();
        return $this->render('sortie/detail.html.twig',
            compact("sortie", "participants", "nbParticipants")
        );
    }

    #[Route('/inscription/{sortie}/', name: 'sortie_inscription')]
    public function inscription(
        Sortie $sortie,
        EntityManagerInterface $entityManager
    ): Response
    {

        if ($sortie->getEtat()->getLibelle() === "Ouverte") {
            if ($this->getUser() != null && ($sortie->getParticipants()->count() < $sortie->getNbInscriptionMax())) {

                $participant = $entityManager->find(Participant::class, $this->getUser()->getId());
                $sortie->addParticipant($participant);
                $participant->addSortie($sortie);
                $entityManager->persist($sortie);
                $entityManager->persist($participant);
                $entityManager->flush();
                return $this->redirectToRoute('sortie_list');
            }
            else {
                $this->addFlash('echec', 'Vous n\'avez pas été inscrit à la sortie.');
                return $this->redirectToRoute('sortie_list');
            }
        }
        else {
            $this->addFlash('echec', 'Vous ne pouvez pas vous inscrire à une sortie dont la période d\'inscription est terminée.');
            return $this->redirectToRoute('sortie_list');
        }


    }



    #[Route('/desistement/{sortie}/', name: 'sortie_desistement')]
    public function desistement(
        Sortie $sortie,
        EntityManagerInterface $entityManager
    ): Response
    {
        if ($sortie->getEtat()->getId() === 2) {
           // if ($this->getUser()  ) {
                $participant = $entityManager->find(Participant::class, $this->getUser()->getId());
                $sortie->removeParticipant($participant);
                $participant->removeSortie($sortie);
                $entityManager->persist($sortie);
                $entityManager->persist($participant);
                $entityManager->flush();
                return $this->redirectToRoute('sortie_list');
          /*  }
            else {
                $this->addFlash('echec', 'Vous n\'avez pas pu vous désister de la sortie.');
                return $this->redirectToRoute('sortie_list');*/
         //   }
        }
        else {
            $this->addFlash('echec', 'Vous ne pouvez pas vous désister à une sortie dont la période d\'inscription est terminée.');
            return $this->redirectToRoute('sortie_list');
        }


    }


    #[Route('/get/lieux/{ville}', name: 'sortie_getville')]
    public function getLieuxVille(
        Ville           $ville,
        VilleRepository  $villeRepository,
        SortieRepository $sortieRepository
    ): Response
    {

        $lieux = $ville->getLieux();
        $lieuxArr = array();

        foreach ($lieux as $lieu) {
            $lieuxArr[] = array(
                "id" => $lieu->getId(),
                "nom" => $lieu->getNom()
            );
        }
        return new JsonResponse($lieuxArr);
    }

}
