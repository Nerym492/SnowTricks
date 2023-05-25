<?php

namespace App\DataFixtures;

use App\Entity\Trick;
use App\Entity\VideosTrick;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class VideosTrickFixtures extends Fixture implements DependentFixtureInterface
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

        $videoIndyGrab1 = new VideosTrick();
        $videoIndyGrab1->setTrick($trickIndyGrab);
        $videoIndyGrab1->setDescription('Tuto Indy Grab');
        $videoIndyGrab1->setUrl('https://www.youtube.com/watch?v=6yA3XqjTh_w&ab_channel=SnowboardProCamp');

        $manager->persist($videoIndyGrab1);
        $manager->flush();
    }
}
