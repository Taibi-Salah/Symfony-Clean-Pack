<?php

namespace App\Controller;

use App\Entity\Stock;
use App\Entity\User;
use App\Form\StockType;
use App\Form\UserType;
use App\Repository\StockRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
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
    public function index(
        UserRepository $userRepository,
        TicketRepository $ticketRepository,
        StockRepository $stockRepository,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        // Retrieve all users and tickets from the database
        $users = $userRepository->findAll();
        $tickets = $ticketRepository->findAll();
        $stocks = $stockRepository->findAll();
        $technicians = $userRepository->findByRole('ROLE_TECHNICIEN');
        $suppliers = $userRepository->findByRole('ROLE_SUPPLIER');

        // Add stock form
        $stock = new Stock();
        $form = $this->createForm(StockType::class, $stock);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($stock);
            $entityManager->flush();

            return $this->redirectToRoute('admin_dashboard');
        }

        $this->denyAccessUnlessGranted('ROLE_ADMIN'); // Only users with ROLE_ADMIN can access this page
        return $this->render('admin/index.html.twig', [
            'users' => $users,
            'tickets' => $tickets,
            'technicians' => $technicians,
            'stocks' => $stocks,
            'suppliers' => $suppliers,
            'form' => $form->createView(),
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

    #[Route('/admin/supplier-list', name: 'supplier_list')]
    public function supplierList(): Response
    {
        // Add logic to handle supplier list if necessary
        return $this->render('admin/supplier_list.html.twig');
    }


    #[Route('/admin/assign-supplier/{id}', name: 'admin_assign_supplier')]
    public function assignSupplier(Request $request, StockRepository $stockRepository, UserRepository $userRepository, EntityManagerInterface $entityManager, $id): Response
    {
        $stock = $stockRepository->find($id);
        $supplierId = $request->request->get('supplier_id');

        if ($supplierId) {
            $supplier = $userRepository->find($supplierId);
            $stock->setSupplier($supplier);
            $entityManager->persist($stock);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_dashboard');
    }

}