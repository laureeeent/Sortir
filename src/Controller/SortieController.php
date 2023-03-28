<?php

namespace App\Controller;


use App\Entity\Participant;
use App\Entity\Sortie;
use App\Form\SortieType;
use App\Repository\EtatRepository;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/sortie')]
class SortieController extends AbstractController
{
    #[Route('/list', name: 'sortir_list')]
    public function list(
        SortieRepository $sortieRepository
    ): Response
    {
        $sorties = $sortieRepository->findAll();
        return $this->render('sortir/list.html.twig',
            compact("sorties")
        );
    }


    #[Route('/ajouter/{participant}', name: 'sortie_ajouter')]
    public function ajouter (
        Participant            $participant,
        Request                $request,
        EntityManagerInterface $entityManager,
        EtatRepository         $etatRepository
    ): Response
    {
        $sortie = new Sortie();

        $etatCree = $etatRepository->findOneBy(['libelle' => 'Créée']);
        $sortieForm = $this->createForm(SortieType::class, $sortie);
        $sortieForm->handleRequest($request);
        if ($sortieForm->isSubmitted() && $sortieForm->isValid()) {
            try {
                $sortie->setEtat($etatCree);
                $dateLI = $sortieForm->get('dateLimiteInscription');
                $dateDebut = $sortieForm->get('dateHeureDebut');
                if ($dateLI > $dateDebut || new \DateTime('now') > $dateLI) {
                    $this->addFlash('echec', 'La date de début de sortie doit être supérieur à la date d\'inscription');
                    return $this->redirectToRoute('sortie_ajouter');
                }

                $sortie->setOrganisateur($participant);
                $sortie->setCampusOrganisateur($participant->getCampus());
                $entityManager->persist($sortie);
                $entityManager->flush();
                $this->addFlash('succes', 'La sortie a bien été insérée');
                return $this->redirectToRoute('sortir_list');
            }
            catch (\Exception $exception) {
                $this->addFlash('echec', 'La sortie n\'a pas été insérée');
                return $this->redirectToRoute('main_home');
            }
        }
        return $this->render('sortie/ajouter.html.twig',
            compact('sortieForm')
          );
    }


    #[Route('/detail/{sortie}', name: 'sortir_detail')]
    public function detail(
        Sortie $sortie,
        SortieRepository $sortieRepository
    ): Response
    {

        return $this->render('sortir/detail.html.twig',
            compact("sortie")
        );
    }


}
