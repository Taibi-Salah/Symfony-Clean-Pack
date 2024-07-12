<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Stock;
use App\Entity\Ticket;
use App\Entity\Intervention;
use App\Repository\UserRepository;
use App\Repository\TicketRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\InterventionRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/admin', name: 'admin_dashboard')]
    public function index(UserRepository $userRepository, TicketRepository $ticketRepository): Response
    {
        // Retrieve all users and tickets from the database
        $users = $userRepository->findAll();
        $tickets = $ticketRepository->findAll();
        $technicians = $userRepository->findByRole('ROLE_TECHNICIEN');

        $this->denyAccessUnlessGranted('ROLE_ADMIN'); //seul l'utilisateur connecté en tant qu'admin peut accéder à cette page
        return $this->render('admin/index.html.twig', [
            'users' => $users,
            'tickets' => $tickets,
            'technicians' => $technicians,
        ]);
    }

    #[Route('/admin/catalogue', name: 'admin_catalogue')]
    public function catalogue(): Response
    {
        return $this->render('admin/catalogue.html.twig');
    }

    #[Route('/admin/history', name: 'admin_history')]
    public function history(): Response
    {
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

        return $this->redirectToRoute('app_dashboardl');
    }

 /**
     * Méthode pour supprimer un utilisateur (fournisseur ou technicien)
     * 
     * @Route("/admin/deleteUser/{id}", name="app_deleteuser")
     */
    #[Route('/admin/deleteUser/{id}', name: 'app_deleteuser')]
    public function deleteUser(User $user): Response
    {
        try {
            $this->entityManager->beginTransaction();

            if (in_array('ROLE_SUPPLIER', $user->getRoles())) {
                $this->handleSupplierDeletion($user);
            } elseif (in_array('ROLE_TECHNICIAN', $user->getRoles())) {
                $this->handleTechnicianDeletion($user);
            }

            // Supprimer les informations de contact
            $contactInfo = $user->getContactInformation();
            if ($contactInfo) {
                $this->entityManager->remove($contactInfo);
            }

            // Supprimer l'utilisateur
            $this->entityManager->remove($user);
            $this->entityManager->flush();

            $this->entityManager->commit();

            $this->addFlash('success', 'Utilisateur et ses informations de contact supprimés avec succès.');
        } catch (\Exception $e) {
            $this->entityManager->rollback();
            $this->addFlash('error', 'Erreur lors de la suppression : ' . $e->getMessage());
        }

        return $this->redirectToRoute('app_dashboard' );
    }

    private function handleSupplierDeletion(User $supplier)
    {
        $stocks = $this->entityManager->getRepository(Stock::class)->findBy(['supplier' => $supplier]);
        foreach ($stocks as $stock) {
            $stock->setSupplier(null);
            $this->entityManager->persist($stock);
        }
    }

    private function handleTechnicianDeletion(User $technician)
    {
        $tickets = $this->entityManager->getRepository(Ticket::class)->findBy(['technician' => $technician]);
        foreach ($tickets as $ticket) {
            $ticket->setTechnician(null);
            $this->entityManager->persist($ticket);
        }
    }}







