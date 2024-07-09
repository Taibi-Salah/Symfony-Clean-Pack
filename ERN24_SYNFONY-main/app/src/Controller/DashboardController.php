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


        private $hub;
    
        public function __construct(HubInterface $hub)
        {
            $this->hub = $hub;
        }
    
        private function publishUpdate($type, $message)
        {
            $update = new Update(
                'https://example.com/notifications', // Utilisez une URL plus spécifique ici
                json_encode(['type' => $type, 'message' => $message])
            );
            $this->hub->publish($update);
        }
    
        #[Route('/update-ticket', name: 'update_ticket', methods: ['POST'])]
        public function updateTicket(Request $request): JsonResponse
        {
            // Récupérer l'ID du ticket depuis la requête
            $ticketId = $request->request->get('ticketId');  
            // Logique de mise à jour du ticket
            // ...
            $this->publishUpdate('ticket_update', 'Le ticket #' . $ticketId . ' a été mis à jour.');
    
            return $this->json(['success' => true]);
        }
    
        #[Route('/stock-alert', name: 'stock_alert', methods: ['POST'])]
        public function stockAlert(Request $request): JsonResponse
        {
            // Récupérer le nom du produit depuis la requête
            $productName = $request->request->get('productName');
            // Logique de vérification du stock
            // ...
            $this->publishUpdate('stock_alert', 'Le stock de ' . $productName . ' est bas.');
    
            return $this->json(['success' => true]);
        }
    }
