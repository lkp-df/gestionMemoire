<?php

namespace App\DataFixtures;

use App\Entity\Filiere;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class FiliereFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        for ($i = 0; $i < 100; $i++) {
            $filiere = new Filiere();
            $filiere->setCodeFiliere("FIL". $i);
            $filiere->setDesignation("Designation filiere " . " " . $i);
            $manager->persist($filiere);
            $manager->flush();
        }
    }
}
