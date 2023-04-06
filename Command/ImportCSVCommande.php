<?php

namespace Command;

use App\Entity\Campus;
use App\Entity\Participant;
use Doctrine\ORM\EntityManagerInterface;
use League\Csv\Reader;
use PHPUnit\Exception;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


#[AsCommand(
    name: 'import-participants',
    description: 'Import participants from a locally saved csv file.',
    aliases: ['import-participants'],
    hidden: false
)]
class ImportCSVCommande extends Command
{

    private $em;
    private $passwordHasher;

    public function __construct(
        EntityManagerInterface $em,
        UserPasswordHasherInterface $passwordHasher
    ) {
        parent::__construct();
        $this->em = $em;
        $this->passwordHasher = $passwordHasher;
    }

    protected function configure() {
        $this
            ->setName('csv:import:commande')
            ->setDescription('Imports CSV')
            ;
    }

    public function hash(
        string $password,
        Participant $participant
    ) : string {
        return $this->passwordHasher->hashPassword($participant, $password);
    }

    protected function execute(
        InputInterface $input,
        OutputInterface $output,
    ) : int {
        $path = 'CSV/participants.csv';
        $participantsCSV = Reader::createFromPath($path, 'r');
        $participantsCSV->setHeaderOffset(0);

        $rows = $participantsCSV->getRecords();
        foreach($rows as $row) {
            try {
                $participant = new Participant();
                $participant->setUsername($row["pseudo"]);
                $participant->setPrenom($row["nom"]);
                $participant->setNom($row["telephone"]);
                $participant->setEmail($row["email"]);
                $participant->setPassword($this->hash($row["motDePasse"], $participant));
                $campus = $this->em->find(Campus::class, $row["campus"]);
                $participant->setCampus($campus);

                $this->em->persist($participant);
                $this->em->flush();
            } catch (Exception $e) {
                return Command::FAILURE;
            }
        }

        return Command::SUCCESS;

    }

}