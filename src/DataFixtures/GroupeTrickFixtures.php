<?php

namespace App\DataFixtures;

use App\Entity\GroupTrick;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class GroupeTrickFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $groupeTrick1 = new GroupTrick();
        $groupeTrick1->setName('Butter');

        $groupeTrick2 = new GroupTrick();
        $groupeTrick2->setName('Grabs');

        $groupeTrick3 = new GroupTrick();
        $groupeTrick3->setName('Spins');

        $groupeTrick4 = new GroupTrick();
        $groupeTrick4->setName('Flips');

        $groupeTrick5 = new GroupTrick();
        $groupeTrick5->setName('Corks');

        $groupeTrick6 = new GroupTrick();
        $groupeTrick6->setName('Rails');

        $groupeTrick7 = new GroupTrick();
        $groupeTrick7->setName('Boxes');

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
