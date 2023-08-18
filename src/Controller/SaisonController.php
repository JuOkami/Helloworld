<?php

namespace App\Controller;

use App\Entity\Saison;
use App\Entity\Serie;
use App\Form\SaisonType;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SaisonController extends AbstractController
{
    #[Route('/saison/{serie}', name: 'app_saison')]
    public function index(
        EntityManagerInterface $entityManager,
        Request $requete,
        Serie $serie
    ): Response
    {
        $saison = new Saison();
        $saison->setSerie($serie);
        $formSaison = $this->createForm(SaisonType::class, $saison);
        $formSaison->handleRequest($requete);
        if ($formSaison->isSubmitted() && $formSaison->isValid()){
            $entityManager->persist($saison);
            $entityManager->flush();
            return $this->redirectToRoute('serie-detail', ['serie' => $serie->getId()]);
        }
        return $this->render('saison/index.html.twig', [
            'formSaison' => $formSaison,
            'controller_name' => 'SaisonController',
        ]);
    }
}
