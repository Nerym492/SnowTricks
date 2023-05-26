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
        $trickBackflip = $manager->getRepository(Trick::class)->findOneBy(['nom' => 'Backflip']);

        $imageIndyGrab1 = new ImagesTrick();
        $imageIndyGrab1->setDescription('Indy Grab');
        $imageIndyGrab1->setTrick($trickIndyGrab);
        // Image avec id unique
        $imageIndyGrab1->setNomFichier('IndyGrab-646f1c86a6297.jpg');

        $imageIndyGrab2 = new ImagesTrick();
        $imageIndyGrab2->setDescription('Indy Grab');
        $imageIndyGrab2->setTrick($trickIndyGrab);
        $imageIndyGrab2->setNomFichier('IndyGrab-646f1c86a6299.jpg');

        $imageBackflip1 = new ImagesTrick();
        $imageBackflip1->setDescription('Backflip');
        $imageBackflip1->setTrick($trickBackflip);
        $imageBackflip1->setNomFichier('backflip.jpeg');

        $manager->persist($imageIndyGrab1);
        $manager->persist($imageIndyGrab2);
        $manager->persist($imageBackflip1);

        $manager->flush();
    }
}
