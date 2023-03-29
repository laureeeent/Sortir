<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\ParticipantType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/participant')]
class ParticipantController extends AbstractController
{
    #[Route('/index', name: 'app_participant')]
    public function index(): Response
    {
        return $this->render('participant/index.html.twig', [
            'controller_name' => 'ParticipantController',
        ]);
    }

    #[Route('/inscription', name: 'app_participant')]
    public function inscription(): Response
    {
        return $this->render('participant/index.html.twig', [
            'controller_name' => 'ParticipantController',
        ]);
    }

    #[Route(
        '/profil/{participant}',
        name: 'app_profil'
    )]
    public function modifierProfil(
        Participant $participant,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response
    {

        $participantForm = $this->createForm(ParticipantType::class, $participant);
        $participantForm->handleRequest($request);
        if($participantForm->isSubmitted() && $participantForm->isValid()){
            $entityManager->persist($participant);
            $entityManager->flush();
            return $this->redirectToRoute('app_participant');
        }
        return $this->render('participant/profil.html.twig',
            compact('participantForm')
        );
    }

    #[Route(
        '/affichage/{id}',
        name: 'app_afficher_profil'
    )]
    public function afficherProfil(
        Participant $id,

    ): Response
    {

        return $this->render('participant/afficherprofil.html.twig',
            compact('id')
        );
    }




}
