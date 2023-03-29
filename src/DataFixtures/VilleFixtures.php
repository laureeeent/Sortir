<?php

namespace App\DataFixtures;

use App\Entity\Ville;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class VilleFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        for ($count = 0; $count < 10; $count++) {

            $ville = new Ville();

            $ville->setNom("Nom" . $count);
            $ville->setCodePostal("Code postal" . $count);

            $manager->persist($ville);
        }
        $manager->flush();
    }
}
