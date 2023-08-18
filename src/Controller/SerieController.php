<?php

namespace App\Controller;

use App\Entity\Saison;
use App\Entity\Serie;
use App\Form\SerieType;
use App\Repository\SerieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;
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
    #[IsGranted('ROLE_DEV')]
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

    #[Route(
        '/creationrapide',
        name: '_creationrapide',
    )]
    #[IsGranted('ROLE_DEV')]
    public function creationrapide(
        EntityManagerInterface $entityManager,
        Request $requete
    ): Response
    {

        $reception = $requete->query->get("nom");
        var_dump($reception);

        return $this->render('serie/creationrapide.html.twig', [
            'controller_name' => 'SerieController'
        ]);
    }

    #[Route(
        '/traitementcreationrapide',
        name: '_traitementcreationrapide',
    )]
    #[IsGranted('ROLE_DEV')]
    public function traitementcreationrapide(
        SerieRepository $serieRepository,
        EntityManagerInterface $entityManager,
        Request $requete,
    ): Response
    {

        $nom = $requete->query->get("nom");
        $imageserie = $requete->query->get("image");
        $nombresaisons = $requete->query->get("saisons");
        $serie = new Serie();
        $serie->setTitre($nom);
        $serie->setImage($imageserie.".jpg");
        $entityManager->persist($serie);
        $entityManager->flush();
//        $serieavecid = $serieRepository->findBy(["titre" => $nom])[0];
//        $id = $serieavecid->getId();

        for ($i = 1; $i <= $nombresaisons; $i++){
            $saison = new Saison();
            $saison->setNbEpisodes(12);
            $saison->setSerie($serie);
            $saison->setImage($imageserie."_".$i.".jpg");
            $saison->setNumero($i);
            $entityManager->persist($saison);
            $entityManager->flush();

        }
        return $this->redirectToRoute('serie-detail', ['serie' => $serie->getId()]);
    }

//    #[Route('/api/series', name:'_api_series')]
//    function api(
//        SerieRepository $serieRepository,
//        SerializerInterface $serializer
//    ): Response {
//        $series = $serieRepository->findAll();
//
//        return new JsonResponse($serializer->serialize($series, 'json', ['groups' => 'serie:read']));
//    }
}
