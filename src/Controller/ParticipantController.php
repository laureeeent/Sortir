<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Entity\Photo;
use App\Form\ParticipantType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Validator\Constraints\File;

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
        EntityManagerInterface $entityManager,
        SluggerInterface $slugger
    ): Response
    {

        $participantForm = $this->createForm(ParticipantType::class, $participant);
        $participantForm->handleRequest($request);
        $photo = new Photo();
        if($participantForm->isSubmitted() && $participantForm->isValid()){

            $photoFile = $participantForm->get('photo')->getData();
            if ($photoFile){
                $originalFilename = pathinfo($photoFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$photoFile->guessExtension();

                try {
                    $photoFile->move(
                        $this->getParameter('photo_directory'),
                        $newFilename
                    );
                } catch (FileException $e){
                    $this->addFlash('echec','le format est incorrect');
                }

                $photo->setPhotoProfil($newFilename);
                $participant->setPhoto($photo);

            }


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
