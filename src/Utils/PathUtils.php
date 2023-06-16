<?php

namespace App\Utils;

use App\Entity\Trick;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class PathUtils
{
    public static function buildTrickPath(
        ParameterBagInterface $parameterBag,
        EntityManagerInterface $manager,
        string $trickName
    ): string {
        $basePath = $parameterBag->get('trick_folder_path');
        $trick = $manager->getRepository(Trick::class)->findOneBy(['name' => $trickName]);
        $trickGroupName = $trick->getGroupTrick()->getName();

        return $basePath.'/'.$trickGroupName.'/'.str_replace(' ', '_', $trickName);
    }
}
