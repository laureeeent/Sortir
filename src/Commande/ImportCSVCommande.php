<?php

namespace App\Commande;

use App\Entity\Participant;
use Doctrine\ORM\EntityManagerInterface;
use League\Csv\Reader;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ImportCSVCommande extends Command
{

    private $em;

    public function __construct(EntityManagerInterface $em) {
        parent::__construct();
        $this->em = $em;
    }

    protected function configure() {
        $this
            ->setName('csv:import:commande')
            ->setDescription('Imports CSV')
            ;
    }

    public function hash(
        string $password,
        UserPasswordHasherInterface $passwordHasher
    ) : string {

    }

    protected function execute(
        InputInterface $input,
        OutputInterface $output,
    ) : int{
        $path = 'CSV/participants.csv';
        $participantsCSV = Reader::createFromPath($path, 'r');
        $participantsCSV->setHeaderOffset(0);

        $rows = $participantsCSV->getRecords();
        foreach($rows as $row) {
            $participant = new Participant();
            $participant->setUsername($row["pseudo"]);
            $participant->setPrenom($row["nom"]);
            $participant->setNom($row["telephone"]);
            $participant->setEmail($row["email"]);
            //$hashedPassword = $pass
            //$participant
        }
    }

}