<?php

namespace App\DataFixtures;

use App\Entity\GroupeTrick;
use App\Entity\Trick;
use App\Entity\Utilisateur;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class TrickFixtures extends Fixture implements DependentFixtureInterface
{
    public function getDependencies(): array
    {
        return [
            UtilisateurFixtures::class,
            GroupeTrickFixtures::class,
        ];
    }

    public function load(ObjectManager $manager)
    {
        $grabsGroup = $manager->getRepository(GroupeTrick::class)->findOneBy(['nom' => 'Grabs']);
        $utilisateur1 = $manager->getRepository(Utilisateur::class)->findOneBy(['nom' => 'Testuser1234']);

        $grabsTrick1 = new Trick();
        $grabsTrick1->setGroupeTrick($grabsGroup);
        $grabsTrick1->setNom('Indy');
        $grabsTrick1->setDescription(
            'Attrape le carre des orteils de ta planche, entre les fixations, avec ta main arrière.'
        );
        $grabsTrick1->setUtilisateur($utilisateur1);

        $grabsTrick2 = new Trick();
        $grabsTrick2->setGroupeTrick($grabsGroup);
        $grabsTrick2->setNom('Stalefish');
        $grabsTrick2->setDescription('Passe la main derrière ton genou arrière et attrape le carre de ta planche entre les fixations, côté talon, avec ta main arrière.');
        $grabsTrick2->setUtilisateur($utilisateur1);

        $manager->persist($grabsTrick1);
        $manager->persist($grabsTrick2);

        $manager->flush();
    }
}
