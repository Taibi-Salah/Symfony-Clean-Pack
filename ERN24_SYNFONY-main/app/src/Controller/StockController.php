<?php

namespace App\Controller;

use App\Entity\Ticket;
use App\Entity\Stock;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class StockController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/stock/{ticketId}', name: 'stock_list')]
    public function list(int $ticketId): Response
    {
        // Fetch the ticket using the provided ticket ID
        $ticket = $this->entityManager->getRepository(Ticket::class)->find($ticketId);
        if (!$ticket) {
            throw $this->createNotFoundException('Le ticket demandé n\'existe pas.');
        }

        // Fetch available stock
        $stocks = $this->entityManager->getRepository(Stock::class)->findAll();

        return $this->render('stock/list.html.twig', [
            'stocks' => $stocks,
            'ticket' => $ticket,
        ]);
    }

    #[Route('/stock/use/{id}', name: 'stock_use')]
    public function use(int $id): Response
    {
        // Implement stock usage logic here

        return $this->redirectToRoute('stock_list');
    }
}

