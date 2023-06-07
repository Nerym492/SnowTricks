<?php

namespace App\DataFixtures;

use App\Entity\GroupeTrick;
use App\Entity\Trick;
use App\Entity\Utilisateur;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;

class TrickFixtures extends Fixture implements DependentFixtureInterface
{
    private EntityManagerInterface $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

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
        $flipsGroup = $manager->getRepository(GroupeTrick::class)->findOneBy(['nom' => 'Flips']);
        $spinsGroup = $manager->getRepository(GroupeTrick::class)->findOneBy(['nom' => 'Spins']);
        $railsGroup = $manager->getRepository(GroupeTrick::class)->findOneBy(['nom' => 'Rails']);
        $butterGroup = $manager->getRepository(GroupeTrick::class)->findOneBy(['nom' => 'Butter']);
        $utilisateur1 = $manager->getRepository(Utilisateur::class)->findOneBy(['nom' => 'Testuser1234']);

        $this->addTrick($grabsGroup, 'Indy', 'Attrape le carre des orteils de ta planche, entre les '.
            'fixations, avec ta main arrière.', $utilisateur1);
        $this->addTrick($grabsGroup, 'Stalefish', 'Passe la main derrière ton genou arrière et attrape '.
            'le carre de ta planche entre les fixations, côté talon, avec ta main arrière.', $utilisateur1);
        $this->addTrick($flipsGroup, 'Backflip', 'Un Backflip fait tourner la planche '.
            'perpendiculairement à la neige, tu fais donc un Flip directement en arrière, en stabilisant la planche '.
            "lors de l'atterrissage.", $utilisateur1);
        $this->addTrick($flipsGroup, 'Frontflip', 'Tout comme le Tamedog, le Frontflip te demande de '.
            "faire un Nose-Press et un Nollie sur un bord. Tu tends ensuite les deux mains vers l'avant pour amorcer ".
            "le saut périlleux et remettre la planche en place pour l'atterrissage.", $utilisateur1);
        $this->addTrick($flipsGroup, 'Backside Rodeo', "L'inverse du Rodéo, le Backside Rodéo consiste ".
            'à amorcer un virage Backside à partir du saut, à décoller la carre du talon, puis à effectuer un '.
            "Backflip avec un Switch 180 à l'atterrissage. On peut ajouter plus de rotation pour en faire un Backside ".
            'Rodéo 540, par exemple - un favori des pros pour son incroyable style !', $utilisateur1);
        $this->addTrick($spinsGroup, 'Corked Spin', 'Un Corked Spin ajoute simplement un front ou un '.
            "Backflip dans un flat spin. Tu l'entendras généralement en compétition lorsque les pros lancent des Back ".
            "Double Corked 10s ou des Cabs Triple Cork 14s. Mais n'importe quel spin peut être \"corké\", comme les ".
            'Rodéos ci-dessus.', $utilisateur1);
        $this->addTrick($railsGroup, '50-50', "Il s'agit de chevaucher un rail ou un box ".
            'avec ta planche en ligne droite sur la structure.', $utilisateur1);
        $this->addTrick($railsGroup, 'Frontside Boardslide', "Il s'agit de glisser jusqu'au rail sur ".
            'ton côté arrière, puis de sauter dessus avec le nez de la planche au-dessus du rail. Tu atterris avec '.
            'le rail entre tes fixations, ta planche perpendiculaire à la structure.', $utilisateur1);
        $this->addTrick($railsGroup, 'Tail Press Rail', 'Un 50-50 avec un Tail Press en plus.', $utilisateur1);
        $this->addTrick($grabsGroup, 'Weddle', '(anciennement appelé Mute Grab) - Du nom de Chris '.
            "Weddle, l'inventeur, ".'attrape le carre des orteils entre les fixations avec ta main avant.',
            $utilisateur1);
        $this->addTrick($grabsGroup, 'Melon', 'Passe la main avant derrière ton genou et attrape le '.
            'bord des talons entre les fixations.', $utilisateur1);
        $this->addTrick($grabsGroup, 'Method', 'À partir de la prise du Melon, étends tes jambes de '.
            "façon à ce que ton corps ait presque la forme de la queue d'un scorpion, puis cherche à atteindre le ".
            "ciel avec ta main arrière. C'est la figure la plus stylée, et chacun a sa propre version.", $utilisateur1);
        $this->addTrick($grabsGroup, 'Nose', "Attrape l'extrémité avant de ta planche avec ta main avant.", $utilisateur1);
        $this->addTrick($flipsGroup, 'Wildcat', 'Un Wildcat est un Backflip qui garde la planche '.
            'parallèle à la trajectoire, tu fais donc une sorte de Flip "latéral" sans perte de vitesse.', $utilisateur1);
        $this->addTrick($flipsGroup, 'Tamedog', "L'exact opposé d'un Wildcat est un Tamedog. C'est ".
            'un Frontflip qui garde la planche parallèle à la trajectoire. Un hard Nollie utilise le nez comme '.
            'tremplin pour amorcer la rotation.', $utilisateur1);
        $this->addTrick($butterGroup, 'Tail Press', 'Le Tail Press est initié en déplaçant ton poids '.
            "vers l'arrière de ta planche tout en restant droit et en soulevant le Nose de la neige.", $utilisateur1);

        $manager->flush();
    }

    private function addTrick(GroupeTrick $groupeTrick, string $nom, string $description, Utilisateur $utilisateur)
    {
        $trick = new Trick();
        $trick->setGroupeTrick($groupeTrick);
        $trick->setNom($nom);
        $trick->setDescription($description);
        $trick->setUtilisateur($utilisateur);

        $this->manager->persist($trick);
    }
}
