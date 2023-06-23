<?php

namespace App\DataFixtures;

use App\Entity\ImagesTrick;
use App\Entity\Trick;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;

class ImagesTrickFixtures extends Fixture implements DependentFixtureInterface
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
        $tricks = [
            'Indy' => ['IndyGrab-646f1c86a6297.jpg', 'IndyGrab-646f1c86a6299.jpg'],
            'Backflip' => 'backflip.jpeg',
            'Frontflip' => 'frontflip-01.jpg',
            'Stalefish' => 'stalefishgrab-01.jpg',
            'Backside Rodeo' => 'Backside_Rodeo-01.jpeg',
            'Corked Spin' => 'TripleCork-01.webp',
            '50-50' => '50-50-01.jpg',
            'Frontside Boardslide' => 'Frontside_Boardslide.jpg',
            'Tail Press Rail' => 'tail-press.jpg',
            'Weddle' => 'Weddle.webp',
            'Melon' => 'MelonGrab.jpg',
            'Method' => 'snowboard-method-grab.jpg',
            'Nose' => 'Nose-grab.jpg',
            'Wildcat' => 'PUSH-Wildcat.jpg',
            'Tamedog' => 'Tamedog.webp',
            'Tail Press' => 'TailPress.jpg',
        ];

        foreach ($tricks as $trickNom => $trickImage) {
            $trickData = $manager->getRepository(Trick::class)->findOneBy(['name' => $trickNom]);
            if ('array' === gettype($trickImage)) {
                $i = 0;
                foreach ($trickImage as $image) {
                    ++$i;
                    // Only the first image is in the header by default.
                    1 === $i ? $isInTheheader = true : $isInTheheader = false;
                    $this->addTrickImage($trickData, $image, $isInTheheader);
                }
            } else {
                $this->addTrickImage($trickData, $trickImage, true);
            }
        }

        $manager->flush();
    }

    private function addTrickImage(Trick $trick, string $fileName, bool $isInTheheader): void
    {
        $trickImg = new ImagesTrick();
        $trickImg->setTrick($trick);
        $trickImg->setFileName($fileName);
        $trickImg->setIsInTheHeader($isInTheheader);

        $this->manager->persist($trickImg);
    }
}
