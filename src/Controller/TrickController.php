<?php

namespace App\Controller;

use App\Utils\ImageUtils;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TrickController extends AbstractController
{
    #[Route('/trickImage/{groupName}/{trickName}/{imageName}', name: 'get_trick_image')]
    public function getTrickImage($groupName, $trickName, $imageName): Response
    {
        $imagePath = '../assets/uploads/Trick/'.$groupName.'/'.str_replace(' ', '_', $trickName).
            '/'.$imageName;

        $imageUtils = new ImageUtils();

        return $imageUtils->serveProtectedImage($imagePath);
    }
}
