<?php

namespace App\Services;

use App\Entity\Campus;
use App\Entity\Participant;
use App\Repository\CampusRepository;
use Doctrine\ORM\EntityManagerInterface;
use League\Csv\Reader;
use PHPUnit\Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ParticipantService
{

    private $hasher;
    private $em;

    public function __construct(
        EntityManagerInterface $entityManager,
        Hasher                 $hasher,
    ) {
        $this->hasher = $hasher;
        $this->em = $entityManager;
    }


    public function importParticpants()
    {
        $campusRepository = $this->em->getRepository(Campus::class);
        $participantRepository = $this->em->getRepository(Participant::class);

        $path = 'CSV/participants.csv';
        $participantsCSV = Reader::createFromPath($path, 'r');
        $participantsCSV->setDelimiter(';');
        $participantsCSV->setHeaderOffset(0);
        $rows = $participantsCSV->getRecords();

        //var_dump($rows);
        foreach($rows as $row) {
            $participant = $participantRepository->findOneBy(["email" => $row["email"]]);
            if (!$participant) {
                try {
                    $participant = new Participant();
                    $participant->setUsername($row["pseudo"]);

                    $participant->setPrenom($row["Prenom"]);
                    $participant->setNom($row["Nom"]);
                    $participant->setTelephone($row["telephone"]);
                    $participant->setEmail($row["email"]);
                    $participant->setPassword($this->hasher->hash($row["motDePasse"], $participant));
                    $participant->setActif(true);
                    $campus = $campusRepository->findOneBy(["nom"=> $row["campus"]]);
                    $participant->setCampus($campus);

                    $this->em->persist($participant);
                    $this->em->flush();
                } catch (Exception $e) {
                }
            }
        }
    }
}