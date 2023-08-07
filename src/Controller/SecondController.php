<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SecondController extends AbstractController
{
    #[Route('/second', name: 'app_second')]
    public function index(): Response
    {
        return $this->render('second/index.html.twig', [
            'nombre' => rand(0,1),
            'controller_name' => 'SecondController',
        ]);
    }
}
