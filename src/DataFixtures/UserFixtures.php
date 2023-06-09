<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $user->setPseudo('Testuser1234');
        $user->setMail('florianpohu49@gmail.com');
        // generated with security:hash-password Test1234*
        $user->setPassword('$2y$13$kNkGx.MPdLzt1R7QCI/1YuMW5XDgoc8f2h7H6WeL4SlI1yKdnfSUC');

        $manager->persist($user);
        $manager->flush();
    }
}
