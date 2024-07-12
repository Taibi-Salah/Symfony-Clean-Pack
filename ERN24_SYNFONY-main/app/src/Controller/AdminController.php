<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Stock;
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
     * Méthode pour supprimer un utilisateur (fournisseur) sans supprimer les stocks associés
     * 
     * @Route("/admin/deleteUser/{id}", name="app_deleteuser")
     */
    #[Route('/admin/deleteUser/{id}', name: 'app_deleteuser')]
    public function deleteUser(User $user): Response
    {
        try {
            // Commencer une transaction
            $this->entityManager->beginTransaction();

            // Récupérer tous les stocks associés à ce fournisseur
            $stocks = $this->entityManager->getRepository(Stock::class)->findBy(['supplier' => $user]);

            // Mettre à NULL la référence au fournisseur pour chaque stock
            foreach ($stocks as $stock) {
                $stock->setSupplier(null);
                $this->entityManager->persist($stock);
            }

            // Supprimer l'utilisateur (fournisseur)
            $this->entityManager->remove($user);

            // Appliquer les changements
            $this->entityManager->flush();

            // Valider la transaction
            $this->entityManager->commit();

            $this->addFlash('success', 'Fournisseur supprimé avec succès. Les stocks associés ont été conservés.');
        } catch (\Exception $e) {
            // En cas d'erreur, annuler la transaction
            $this->entityManager->rollback();
            $this->addFlash('error', 'Erreur lors de la suppression : ' . $e->getMessage());
        }

        return $this->redirectToRoute('admin');
    }

}







