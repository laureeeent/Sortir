<?php

namespace App\Controller;

use App\Entity\Campus;
use App\Entity\Participant;
use App\Entity\Photo;
use App\Form\ImportParticipantFormType;
use App\Form\ParticipantType;
use App\Repository\CampusRepository;
use App\Services\Hasher;
use App\Services\ParticipantService;
use Doctrine\ORM\EntityManagerInterface;
use League\Csv\Reader;
use PHPUnit\Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\VarDumper\Cloner\Data;

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
            compact('participantForm','participant')

        );
    }



    #[Route(
        '/affichage/{participant}',
        name: 'app_afficher_profil'
    )]
    public function afficherProfil(
        Participant $participant,
        Hasher $hasher,
        EntityManagerInterface $entityManager
    ): Response
    {

        return $this->render('participant/afficherprofil.html.twig',
            compact('participant')
        );
    }

    #[IsGranted("ROLE_ADMIN")]
    #[Route(
        '/import/input-csv',
        name: 'app_import_input_csv'
    )]
    public function inputCSV(
        Request     $request,
        SluggerInterface $slugger,
        ParticipantService $participantService,
    ): Response
    {
        $importParticipantsForm = $this->createForm(ImportParticipantFormType::class);
        $importParticipantsForm->handleRequest($request);

        $csvFile = $importParticipantsForm->get('fichiercsv')->getData();
        if ($importParticipantsForm->isSubmitted() && $importParticipantsForm->isValid()) {
            $originalFilename = pathinfo($csvFile->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = $slugger->slug($originalFilename);
            $newFilename = $safeFilename.'-'.uniqid().'.'.$csvFile->guessExtension();
            try {
                $csvFile->move(
                    $this->getParameter('csv_participants_directory'),
                    "participants.csv"
                );
                $participantService->importParticpants();
            } catch (FileException $e){
                $this->addFlash('echec','le format est incorrect');
            }
        }

        return $this->render('participant/import-csv.html.twig', compact("importParticipantsForm"));
    }

}
