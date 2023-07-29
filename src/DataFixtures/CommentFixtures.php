<?php

namespace App\DataFixtures;

use App\Entity\Comment;
use App\Entity\Trick;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;

/**
 * Comments dataset
 */
class CommentFixtures extends Fixture implements DependentFixtureInterface
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
        $this->addComment('Testuser1234', 'Indy', 'Mon tout premier commentaire sur ce site !!!');
        $this->addComment('Testuser1234', 'Backflip', 'Another one !');

        $manager->flush();
    }

    /**
     * @param string $userPseudo
     * @param string $trickName
     * @param string $content
     * @return void
     */
    private function addComment(string $userPseudo, string $trickName, string $content): void
    {
        $user = $this->manager->getRepository(User::class)->findOneBy(['pseudo' => $userPseudo]);
        $trick = $this->manager->getRepository(Trick::class)->findOneBy(['name' => $trickName]);
        $comment = new Comment();
        $comment->setUser($user);
        $comment->setContent($content);
        $comment->setCreationDate(new \DateTime());
        $comment->setTrick($trick);

        $this->manager->persist($comment);
    }
}
