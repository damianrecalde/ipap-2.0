<?php

namespace App\DataFixtures;

use App\Entity\WorkTeam;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

namespace App\DataFixtures;

use App\Entity\WorkTeam;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $team1 = new WorkTeam();
        $team1->setName('Equipo Campus');
        $team1->setCreateat(new \DateTime());  // Establecemos la fecha de creación
        $manager->persist($team1);

        $team2 = new WorkTeam();
        $team2->setName('Equipo Administración');
        $team2->setCreateat(new \DateTime());  // Establecemos la fecha de creación
        $manager->persist($team2);

        $team3 = new WorkTeam();
        $team3->setName('Equipo Soporte');
        $team3->setCreateat(new \DateTime());  // Establecemos la fecha de creación
        $manager->persist($team3);

        $manager->flush();
    }
}
