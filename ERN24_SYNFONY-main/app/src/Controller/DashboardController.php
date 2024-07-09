<?php

// src/Controller/DashboardController.php

namespace App\Controller;

use Symfony\Component\Mercure\Update;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'app_dashboard')]
    public function index(): Response
    {
        // Données pour le tableau de bord
        $dashboardData = [
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

        // Données pour la gestion des tickets
        $tickets = [
            ['id' => 1, 'title' => 'Problème de connexion', 'status' => 'Ouvert', 'assignedTo' => 'John Doe'],
            ['id' => 2, 'title' => 'Erreur 404', 'status' => 'En cours', 'assignedTo' => 'Jane Smith'],
            ['id' => 3, 'title' => 'Mise à jour nécessaire', 'status' => 'Résolu', 'assignedTo' => 'Bob Johnson'],
        ];

        $technicians = [
            ['id' => 1, 'name' => 'John Doe'],
            ['id' => 2, 'name' => 'Jane Smith'],
            ['id' => 3, 'name' => 'Bob Johnson'],
        ];

        // Combinez toutes les données
        $data = array_merge($dashboardData, [
            'tickets' => $tickets,
            'technicians' => $technicians,
        ]);

        return $this->render('home/dashboard.html.twig', $data);

    }

    }
