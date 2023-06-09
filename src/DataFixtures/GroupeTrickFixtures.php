<?php

namespace App\DataFixtures;

use App\Entity\GroupTrick;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;

class GroupeTrickFixtures extends Fixture
{
    private EntityManagerInterface $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    public function load(ObjectManager $manager): void
    {
        $this->addGroup('Butter');
        $this->addGroup('Grabs');
        $this->addGroup('Spins');
        $this->addGroup('Flips');
        $this->addGroup('Corks');
        $this->addGroup('Rails');
        $this->addGroup('Boxes');

        $manager->flush();
    }

    private function addGroup(string $name): void
    {
        $groupTrick = new GroupTrick();
        $groupTrick->setName($name);
        $this->manager->persist($groupTrick);
    }
}
