<?php

namespace App\Controller;

use App\Entity\Serie;
use App\Form\SerieType;
use App\Repository\SerieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class SerieController extends AbstractController
{
    #[Route('/series', name: 'serie-liste')]
    public function liste(
        SerieRepository $serieRepository,
        HttpClientInterface $client
    ): Response
    {
        $series = $serieRepository->findby([], ["titre" => "ASC"]);

        $reponseAPI = $client->request('GET', 'https://chuckn.neant.be/api/rand');
        $blagues = $reponseAPI->toArray();

        return $this->render('serie/liste.html.twig', [
            'controller_name' => 'SerieController',
            'series' => $series,
            'blagues' => $blagues
        ]);
    }
//
//        #[Route(
//            '/serie/{id}',
//            name: 'serie-detail',
//            requirements:["id" => "\d+"])]
//    public function detail(
//        $id,
//        SerieRepository $serieRepository
//        ): Response
//    {
//       $serie = $serieRepository->findOneBy(["id" => $id]);
//        return $this->render('serie/detail.html.twig', [
//            'controller_name' => 'SerieController',
//            'serie' => $serie
//        ]);
//    }

    #[Route(
        '/serie/{serie}',
        name: 'serie-detail',
        requirements:["serie" => "\d+"])]
    public function detail(
        Serie $serie,
    ): Response
    {
        return $this->render('serie/detail.html.twig', [
            'controller_name' => 'SerieController',
            'serie' => $serie
        ]);
    }

    #[Route(
        '/creation',
        name: '_creation',
        )]
    public function creation(
        EntityManagerInterface $entityManager,
        Request $requete
    ): Response
    {
        $serie = new Serie();
        $serieForm = $this->createForm(SerieType::class, $serie);

        $serieForm -> handleRequest($requete);

        if($serieForm->isSubmitted() && $serieForm->isValid()){
            $entityManager->persist($serie);
            $entityManager->flush();
            $this->addFlash('success', 'Série ajoutée avec succès');
            return $this->redirect('series');
        }

        return $this->render('serie/creation.html.twig', [
            'serieForm' => $serieForm,
            'controller_name' => 'SerieController'
        ]);
    }
}
