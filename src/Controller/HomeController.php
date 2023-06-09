<?php

namespace App\Controller;

use App\Entity\Trick;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/home', name: 'app_home')]
    #[Route('/', name: 'app_base')]
    public function showHome(EntityManagerInterface $manager): Response
    {
        $tricks = $manager->getRepository(Trick::class)->findAllTricksBy(['name' => 'ASC']);

        return $this->render('home/index.html.twig', [
            'tricks' => $tricks,
        ]);
    }
}
