<?php

namespace App\DataFixtures;

use App\Entity\Comment;
use App\Entity\Trick;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

/**
 * Comments dataset
 */
class CommentFixtures extends Fixture implements DependentFixtureInterface
{
    /**
     * @return string[]
     */
    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
            TrickFixtures::class,
        ];
    }

    /**
     * Create comments dataset
     *
     * @param ObjectManager $manager
     * @return void
     */
    public function load(ObjectManager $manager)
    {
        $user = $manager->getRepository(User::class)->findOneBy(['pseudo' => 'Testuser1234']);
        $trick = $manager->getRepository(Trick::class)->findOneBy(['name' => 'Indy']);
        $comment = new Comment();
        $comment->setUser($user);
        $comment->setContent('Mon tout premier commentaire sur ce site !!!');
        $comment->setCreationDate(new \DateTime());
        $comment->setTrick($trick);

        $manager->persist($comment);
        $manager->flush();
    }
}
