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
            $trickData = $manager->getRepository(Trick::class)->findOneBy(['nom' => $trickNom]);
            if ('array' === gettype($trickImage)) {
                foreach ($trickImage as $image) {
                    $this->addTrickImage($trickNom, $trickData, $image);
                }
            } else {
                $this->addTrickImage($trickNom, $trickData, $trickImage);
            }

        }

        $manager->flush();
    }

    private function addTrickImage(string $description, Trick $trick, string $nomFichier): void
    {
        $trickImg = new ImagesTrick();
        $trickImg->setDescription($description);
        $trickImg->setTrick($trick);
        $trickImg->setFileName($nomFichier);

        $this->manager->persist($trickImg);
    }
}
