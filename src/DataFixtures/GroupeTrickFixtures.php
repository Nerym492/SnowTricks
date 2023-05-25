<?php

namespace App\DataFixtures;

use App\Entity\GroupeTrick;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class GroupeTrickFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $groupeTrick1 = new GroupeTrick();
        $groupeTrick1->setNom('Butter');

        $groupeTrick2 = new GroupeTrick();
        $groupeTrick2->setNom('Grabs');

        $groupeTrick3 = new GroupeTrick();
        $groupeTrick3->setNom('Spins');

        $groupeTrick4 = new GroupeTrick();
        $groupeTrick4->setNom('Flips');

        $groupeTrick5 = new GroupeTrick();
        $groupeTrick5->setNom('Corks');

        $groupeTrick6 = new GroupeTrick();
        $groupeTrick6->setNom('Rails');

        $groupeTrick7 = new GroupeTrick();
        $groupeTrick7->setNom('Boxes');

        $manager->persist($groupeTrick1);
        $manager->persist($groupeTrick2);
        $manager->persist($groupeTrick3);
        $manager->persist($groupeTrick4);
        $manager->persist($groupeTrick5);
        $manager->persist($groupeTrick6);
        $manager->persist($groupeTrick7);

        $manager->flush();
    }
}
