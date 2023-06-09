<?php

namespace App\DataFixtures;

use App\Entity\Comment;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class CommentFixtures extends Fixture implements DependentFixtureInterface
{
    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
        ];
    }

    public function load(ObjectManager $manager)
    {
        $user = $manager->getRepository(User::class)->findOneBy(['pseudo' => 'Testuser1234']);
        $comment = new Comment();
        $comment->setUser($user);
        $comment->setContent('Mon tout premier commentaire sur ce site !!!');
        $comment->setCreationDate(new \DateTime());

        $manager->persist($comment);
        $manager->flush();
    }
}
