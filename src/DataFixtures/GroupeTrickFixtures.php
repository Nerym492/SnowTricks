<?php

namespace App\DataFixtures;

use App\Entity\GroupTrick;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;

/**
 * GroupTrick dataset
 */
class GroupeTrickFixtures extends Fixture
{
    private EntityManagerInterface $manager;

    /**
     * @param EntityManagerInterface $manager
     */
    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    /**
     * Create a groupTrick dataset
     *
     * @param ObjectManager $manager
     * @return void
     */
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

    /**
     * Add one groupTrick to the dataset
     *
     * @param string $name
     * @return void
     */
    private function addGroup(string $name): void
    {
        $groupTrick = new GroupTrick();
        $groupTrick->setName($name);
        $this->manager->persist($groupTrick);
    }
}
