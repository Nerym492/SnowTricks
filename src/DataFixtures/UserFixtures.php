<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $utilisateur1 = new User();
        $utilisateur1->setNom('Testuser1234');
        $utilisateur1->setMail('florianpohu49@gmail.com');
        $utilisateur1->setMotDePasse('Test1234*');
        $utilisateur1->setLienConfirmation('');
        $utilisateur1->setMailValide(1);
        $utilisateur1->setPhotoProfil('');

        $manager->persist($utilisateur1);
        $manager->flush();
    }
}
