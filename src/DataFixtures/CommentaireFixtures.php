<?php

namespace App\DataFixtures;

use App\Entity\Comment;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class CommentaireFixtures extends Fixture implements DependentFixtureInterface
{
    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
        ];
    }

    public function load(ObjectManager $manager)
    {
        $utilisateur = $manager->getRepository(User::class)->findOneBy(['nom' => 'Testuser1234']);
        $comment = new Comment();
        $comment->setUser($utilisateur);
        $comment->setContent('Mon tout premier commentaire sur ce site !!!');
        $comment->setDateCreation(new \DateTime());

        $manager->persist($comment);
        $manager->flush();
    }
}
