<?php

namespace App\DataFixtures;

use App\Entity\ImagesTrick;
use App\Entity\Trick;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ImagesTrickFixtures extends Fixture implements DependentFixtureInterface
{
    public function getDependencies(): array
    {
        return [
            TrickFixtures::class,
        ];
    }

    public function load(ObjectManager $manager)
    {
        $trickIndyGrab = $manager->getRepository(Trick::class)->findOneBy(['nom' => 'Indy']);

        $imageIndyGrab1 = new ImagesTrick();
        $imageIndyGrab1->setDescription('Indy Grab');
        $imageIndyGrab1->setTrick($trickIndyGrab);
        // Image avec id unique
        $imageIndyGrab1->setChemin('/assets/uploads/Trick/Grabs/Indy/IndyGrab-646f1c86a6297.jpg');

        $imageIndyGrab2 = new ImagesTrick();
        $imageIndyGrab2->setDescription('Indy Grab');
        $imageIndyGrab2->setTrick($trickIndyGrab);
        $imageIndyGrab2->setChemin('/assets/uploads/Trick/Grabs/Indy/IndyGrab-646f1c86a6299.jpg');

        $manager->persist($imageIndyGrab1);
        $manager->persist($imageIndyGrab2);

        $manager->flush();
    }
}
