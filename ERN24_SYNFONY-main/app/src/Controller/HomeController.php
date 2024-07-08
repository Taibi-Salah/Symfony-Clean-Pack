<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }
#[Route('/dashboard', name: 'app_dashboard')]
    public function tableauDeBord(): Response
    {
        // Ces données devraient venir de votre logique métier / base de données
        $data = [
            'openTickets' => 25,
            'inProgressTickets' => 15,
            'resolvedToday' => 10,
            'avgResolutionTime' => 4.5,
            'recentTickets' => [
                ['title' => 'Problème de connexion', 'date' => new \DateTime()],
                ['title' => 'Erreur 404', 'date' => new \DateTime('-1 day')],
                ['title' => 'Mise à jour nécessaire', 'date' => new \DateTime('-2 days')],
            ],
        ];

        return $this->render('home/dashboard.html.twig', $data);
    }
}
