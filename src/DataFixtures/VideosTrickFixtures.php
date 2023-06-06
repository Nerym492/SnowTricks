<?php

namespace App\DataFixtures;

use App\Entity\Trick;
use App\Entity\VideosTrick;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;

class VideosTrickFixtures extends Fixture implements DependentFixtureInterface
{
    private EntityManagerInterface $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    public function getDependencies(): array
    {
        return [
            TrickFixtures::class,
        ];
    }

    public function load(ObjectManager $manager)
    {
        $trickIndyGrab = $manager->getRepository(Trick::class)->findOneBy(['nom' => 'Indy']);

        $this->addTrickVideo(
            $trickIndyGrab,
            'Tuto Indy Grab',
            'https://www.youtube.com/embed/6yA3XqjTh_w'
        );

        $this->addTrickVideo(
            $trickIndyGrab,
            'Indy Grab with style !',
            'https://www.youtube.com/embed/G_MEz7oJzro'
        );

        $manager->flush();
    }

    private function addTrickVideo(Trick $trick, string $description, string $link): void
    {
        $trickVideo = new VideosTrick();
        $trickVideo->setTrick($trick);
        $trickVideo->setDescription($description);
        $trickVideo->setUrl($link);
        $this->manager->persist($trickVideo);
    }
}
