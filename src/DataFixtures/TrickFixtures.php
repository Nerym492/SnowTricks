<?php

namespace App\DataFixtures;

use App\Entity\GroupTrick;
use App\Entity\Trick;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\String\Slugger\SluggerInterface;

/**
 * Trick dataset
 */
class TrickFixtures extends Fixture implements DependentFixtureInterface
{
    private EntityManagerInterface $manager;
    private SluggerInterface $slugger;

    /**
     * @param EntityManagerInterface $manager
     */
    public function __construct(EntityManagerInterface $manager, SluggerInterface $slugger)
    {
        $this->manager = $manager;
        $this->slugger = $slugger;
    }

    /**
     * @return string[]
     */
    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
            GroupeTrickFixtures::class,
        ];
    }

    /**
     * Create Trick dataset
     *
     * @param ObjectManager $manager
     * @return void
     */
    public function load(ObjectManager $manager)
    {
        $grabsGroup = $manager->getRepository(GroupTrick::class)->findOneBy(['name' => 'Grabs']);
        $flipsGroup = $manager->getRepository(GroupTrick::class)->findOneBy(['name' => 'Flips']);
        $spinsGroup = $manager->getRepository(GroupTrick::class)->findOneBy(['name' => 'Spins']);
        $railsGroup = $manager->getRepository(GroupTrick::class)->findOneBy(['name' => 'Rails']);
        $butterGroup = $manager->getRepository(GroupTrick::class)->findOneBy(['name' => 'Butter']);
        $user1 = $manager->getRepository(User::class)->findOneBy(['pseudo' => 'Testuser1234']);

        $this->addTrick($grabsGroup, 'Indy', 'Attrape le carre des orteils de ta planche, entre les '.
        'fixations, avec ta main arrière.', $user1);
        $this->addTrick($grabsGroup, 'Stalefish', 'Passe la main derrière ton genou arrière et attrape '.
        'le carre de ta planche entre les fixations, côté talon, avec ta main arrière.', $user1);
        $this->addTrick($flipsGroup, 'Backflip', 'Un Backflip fait tourner la planche '.
            'perpendiculairement à la neige, tu fais donc un Flip directement en arrière, en stabilisant la planche '.
        "lors de l'atterrissage.", $user1);
        $this->addTrick($flipsGroup, 'Frontflip', 'Tout comme le Tamedog, le Frontflip te demande de '.
            "faire un Nose-Press et un Nollie sur un bord. Tu tends ensuite les deux mains vers l'avant pour amorcer ".
        "le saut périlleux et remettre la planche en place pour l'atterrissage.", $user1);
        $this->addTrick($flipsGroup, 'Backside Rodeo', "L'inverse du Rodéo, le Backside Rodéo consiste ".
            'à amorcer un virage Backside à partir du saut, à décoller la carre du talon, puis à effectuer un '.
            "Backflip avec un Switch 180 à l'atterrissage. On peut ajouter plus de rotation pour en faire un Backside ".
        'Rodéo 540, par exemple - un favori des pros pour son incroyable style !', $user1);
        $this->addTrick($spinsGroup, 'Corked Spin', 'Un Corked Spin ajoute simplement un front ou un '.
            "Backflip dans un flat spin. Tu l'entendras généralement en compétition lorsque les pros lancent des Back ".
            "Double Corked 10s ou des Cabs Triple Cork 14s. Mais n'importe quel spin peut être \"corké\", comme les ".
        'Rodéos ci-dessus.', $user1);
        $this->addTrick($railsGroup, '50-50', "Il s'agit de chevaucher un rail ou un box ".
        'avec ta planche en ligne droite sur la structure.', $user1);
        $this->addTrick($railsGroup, 'Frontside Boardslide', "Il s'agit de glisser jusqu'au rail sur ".
            'ton côté arrière, puis de sauter dessus avec le nez de la planche au-dessus du rail. Tu atterris avec '.
        'le rail entre tes fixations, ta planche perpendiculaire à la structure.', $user1);
        $this->addTrick($railsGroup, 'Tail Press Rail', 'Un 50-50 avec un Tail Press en plus.', $user1);
        $this->addTrick(
            $grabsGroup,
            'Weddle',
            '(anciennement appelé Mute Grab) - Du nom de Chris '.
            "Weddle, l'inventeur, ".'attrape le carre des orteils entre les fixations avec ta main avant.',
            $user1
        );
        $this->addTrick($grabsGroup, 'Melon', 'Passe la main avant derrière ton genou et attrape le '.
        'bord des talons entre les fixations.', $user1);
        $this->addTrick($grabsGroup, 'Method', 'À partir de la prise du Melon, étends tes jambes de '.
            "façon à ce que ton corps ait presque la forme de la queue d'un scorpion, puis cherche à atteindre le ".
        "ciel avec ta main arrière. C'est la figure la plus stylée, et chacun a sa propre version.", $user1);
        $this->addTrick($grabsGroup, 'Nose', "Attrape l'extrémité avant de ta planche avec ta main avant.", $user1);
        $this->addTrick($flipsGroup, 'Wildcat', 'Un Wildcat est un Backflip qui garde la planche '.
        'parallèle à la trajectoire, tu fais donc une sorte de Flip "latéral" sans perte de vitesse.', $user1);
        $this->addTrick($flipsGroup, 'Tamedog', "L'exact opposé d'un Wildcat est un Tamedog. C'est ".
            'un Frontflip qui garde la planche parallèle à la trajectoire. Un hard Nollie utilise le nez comme '.
        'tremplin pour amorcer la rotation.', $user1);
        $this->addTrick($butterGroup, 'Tail Press', 'Le Tail Press est initié en déplaçant ton poids '.
        "vers l'arrière de ta planche tout en restant droit et en soulevant le Nose de la neige.", $user1);

        $manager->flush();
    }

    /**
     * Add one Trick to the dataset
     *
     * @param GroupTrick $groupTrick
     * @param string $name
     * @param string $description
     * @param User $user
     * @return void
     */
    private function addTrick(GroupTrick $groupTrick, string $name, string $description, User $user): void
    {
        $trick = new Trick();
        $trick->setGroupTrick($groupTrick);
        $trick->setName($name);
        $trick->setDescription($description);
        $trick->setUser($user);
        $trick->setCreationDate(new \DateTime());
        $trick->setSlug($this->slugger->slug($name, '_'));

        $this->manager->persist($trick);
    }
}
