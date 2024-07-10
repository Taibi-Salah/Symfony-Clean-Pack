<?php

namespace App\Controller;

use App\Entity\Ticket;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DashboardController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/dashboard', name: 'app_dashboard')]
    public function index(): Response
    {
        // Fetch data from the database
        $inProgressTickets = $this->entityManager->getRepository(Ticket::class)->count(['status' => 'in_progress']);
        $resolvedToday = $this->entityManager->getRepository(Ticket::class)->countResolvedToday();
        $avgResolutionTime = $this->entityManager->getRepository(Ticket::class)->calculateAverageResolutionTime();
        $recentTickets = $this->entityManager->getRepository(Ticket::class)->findRecentTickets();

        // Fetch all tickets
        $tickets = $this->entityManager->getRepository(Ticket::class)->findAll();
        // Fetch technicians
        $technicians = $this->entityManager->getRepository(User::class)->findByRole('ROLE_TECHNICIAN');

        // Combine data into an array
        $data = [
            'inProgressTickets' => $inProgressTickets,
            'resolvedToday' => $resolvedToday,
            'avgResolutionTime' => $avgResolutionTime,
            'recentTickets' => $recentTickets,
            'tickets' => $tickets,
            'technicians' => $technicians,
        ];

        return $this->render('home/dashboard.html.twig', $data);
    }
}






