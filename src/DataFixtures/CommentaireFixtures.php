<?php

namespace App\DataFixtures;

use App\Entity\Commentaire;
use App\Entity\Utilisateur;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class CommentaireFixtures extends Fixture implements DependentFixtureInterface
{
    public function getDependencies(): array
    {
        return [
            UtilisateurFixtures::class,
        ];
    }

    public function load(ObjectManager $manager)
    {
        $utilisateur = $manager->getRepository(Utilisateur::class)->findOneBy(['nom' => 'Testuser1234']);
        $comment = new Commentaire();
        $comment->setUtilisateur($utilisateur);
        $comment->setContenu('Mon tout premier commentaire sur ce site !!!');
        $comment->setDateCreation(new \DateTime());

        $manager->persist($comment);
        $manager->flush();
    }
}
