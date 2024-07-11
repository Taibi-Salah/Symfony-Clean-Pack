<?php

namespace App\Controller;

use App\Repository\StockRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\UserRepository;
use App\Repository\TicketRepository;
use App\Repository\InterventionRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Intervention;

class AdminController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/admin', name: 'admin_dashboard')]
    public function index(UserRepository $userRepository, TicketRepository $ticketRepository, StockRepository $stockRepository): Response
    {
        // Retrieve all users and tickets from the database
        $users = $userRepository->findAll();
        $tickets = $ticketRepository->findAll();
        $stocks = $stockRepository->findAll();
        $technicians = $userRepository->findByRole('ROLE_TECHNICIEN');
        $suppliers = $userRepository->findByRole('ROLE_SUPPLIER');

        $this->denyAccessUnlessGranted('ROLE_ADMIN'); //seul l'utilisateur connecté en tant qu'admin peut accéder à cette page
        return $this->render('admin/index.html.twig', [
            'users' => $users,
            'tickets' => $tickets,
            'technicians' => $technicians,
            'stocks' => $stocks,
            'suppliers' => $suppliers,
        ]);
    }

    #[Route('/admin/catalogue', name: 'admin_catalogue')]
    public function catalogue(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        return $this->render('admin/catalogue.html.twig');
    }

    #[Route('/admin/history', name: 'admin_history')]
    public function history(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        return $this->render('admin/history.html.twig');
    }

    #[Route('/admin/addpiece', name: 'app_addpiece')]
    public function addpiece(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        return $this->render('admin/form/addpiece.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }

    #[Route('/admin/editpiece', name: 'app_editpiece')]
    public function updateticket(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        return $this->render('admin/form/editpiece.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }

    #[Route('/admin/assign-ticket/{id}', name: 'admin_assign_ticket', methods: ['POST'])]
    public function assignTicket(Request $request, $id, TicketRepository $ticketRepository, UserRepository $userRepository, InterventionRepository $interventionRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $ticket = $ticketRepository->find($id);
        if (!$ticket) {
            throw $this->createNotFoundException('Ticket not found');
        }

        $technicianId = $request->request->get('technician_id');
        if ($technicianId) {
            $technician = $userRepository->find($technicianId);
            if ($technician) {
                $ticket->setTechnicien($technician);

                // Create or update the intervention
                $intervention = $ticket->getIntervention();
                if (!$intervention) {
                    $intervention = new Intervention();
                    $ticket->setIntervention($intervention);
                }
                $intervention->setLabel('Intervention for ticket ' . $ticket->getId());

                $this->entityManager->persist($intervention);
                $this->entityManager->flush();
            }
        }

        return $this->redirectToRoute('admin_dashboard');
    }

    #[Route('/user/supplier-list', name: 'user_supplier')]
    public function supplierList(StockRepository $stockRepository, UserRepository $userRepository): Response
    {
        $stocks = $stockRepository->findAll();
        $suppliers = $userRepository->findByRole('ROLE_SUPPLIER');

        return $this->render('user/supplier.html.twig', [
            'stocks' => $stocks,
            'suppliers' => $suppliers,
        ]);
    }

    #[Route('/admin/assign-supplier/{id}', name: 'admin_assign_supplier', methods: ['POST'])]
    public function assignSupplier($id, Request $request, StockRepository $stockRepository, UserRepository $userRepository, EntityManagerInterface $entityManager): JsonResponse
    {
        $stock = $stockRepository->find($id);
        if (!$stock) {
            return new JsonResponse(['success' => false], 404);
        }

        $supplierId = $request->request->get('supplier_id');
        $supplier = $userRepository->find($supplierId);
        if (!$supplier) {
            return new JsonResponse(['success' => false], 404);
        }

        $stock->setSupplier($supplier);
        $entityManager->persist($stock);
        $entityManager->flush();

        return new JsonResponse(['success' => true]);
    }

}