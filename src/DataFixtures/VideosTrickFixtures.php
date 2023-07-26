<?php

namespace App\DataFixtures;

use App\Entity\Trick;
use App\Entity\VideosTrick;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;

/**
 * VideosTrick dataset
 */
class VideosTrickFixtures extends Fixture implements DependentFixtureInterface
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
     * @return string[]
     */
    public function getDependencies(): array
    {
        return [
            TrickFixtures::class,
        ];
    }

    /**
     * Create the VideosTrick dataset
     *
     * @param ObjectManager $manager
     * @return void
     */
    public function load(ObjectManager $manager)
    {
        $trickIndyGrab = $manager->getRepository(Trick::class)->findOneBy(['name' => 'Indy']);

        $this->addTrickVideo(
            $trickIndyGrab,
            'https://www.youtube.com/embed/6yA3XqjTh_w'
        );

        $this->addTrickVideo(
            $trickIndyGrab,
            'https://www.youtube.com/embed/G_MEz7oJzro'
        );

        $manager->flush();
    }

    /**
     * Add one TrickVideo to the dataset
     *
     * @param Trick $trick
     * @param string $link
     * @return void
     */
    private function addTrickVideo(Trick $trick, string $link): void
    {
        $trickVideo = new VideosTrick();
        $trickVideo->setTrick($trick);
        $trickVideo->setUrl($link);
        $this->manager->persist($trickVideo);
    }
}
