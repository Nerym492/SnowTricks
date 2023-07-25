<?php

namespace App\Utils;

use App\Entity\Trick;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

/**
 * Path generation
 */
class PathUtils
{
    /**
     * Generate a path for a given Trick
     *
     * @param ParameterBagInterface $parameterBag
     * @param Trick $trick
     * @return string
     */
    public static function buildTrickPath(
        ParameterBagInterface $parameterBag,
        Trick $trick,
    ): string {
        $basePath = $parameterBag->get('trick_folder_path');
        $trickGroupName = $trick->getGroupTrick()->getName();

        return $basePath.'/'.$trickGroupName.'/'.str_replace(' ', '_', $trick->getName());
    }
}
