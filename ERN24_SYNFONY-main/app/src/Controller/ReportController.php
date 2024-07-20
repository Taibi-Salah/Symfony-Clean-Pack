<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Ticket;
use App\Entity\Facturation;
use App\Repository\TicketRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class ReportController extends AbstractController
{

    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    
    #[Route('/reports', name: 'app_reports')]
    public function index(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_TECHNICIEN');
        // Logique pour récupérer les rapports depuis la base de données
        $tickets_inprogress = $this->entityManager->getRepository(Ticket::class)->findBy([
            'status' => 'resolus'
        ]);
        $techniciens = $this->entityManager->getRepository(User::class)->findAll();

        return $this->render('home/report.html.twig', [
            'tickets' => $tickets_inprogress,
            'techniciens' => $techniciens,
        ]);
    }
}
