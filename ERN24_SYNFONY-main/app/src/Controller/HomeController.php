<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{

    /**
     * Route qui permet d'afficher la page d'accueil
     * @Route("/", name="app_home")
     * @return Response
     */
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }


    /**
     * Route qui permet de se connecter
     * @Route("/connexion", name="app_connexion")
     * @return Response
     */
    #[Route('/connexion', name: 'app_connexion')]
    public function login(): Response
    {
        return $this->render('home/connexion/connexion.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }

    /**
     * Route qui permet de s'inscrire
     * @Route("/inscription", name="app_inscription")
     * @return Response
     */
    #[Route('/inscription', name: 'app_inscription')]
    public function register(): Response
    {
        return $this->render('home/connexion/inscription.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }


}
