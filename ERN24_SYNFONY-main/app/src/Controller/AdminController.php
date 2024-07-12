<?php

namespace App\Controller;

use App\Entity\Stock;
use App\Form\AddStockType;
use App\Entity\Intervention;
use App\Repository\UserRepository;
use App\Repository\TicketRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\InterventionRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Validator\Validator\ValidatorInterface;

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
    { //ici on affiche le catalogue des pièces
        $stock = $this->entityManager->getRepository(Stock::class)->findAll();

        return $this->render('admin/catalogue.html.twig', [
            'stock' => $stock,
            
        ]);
    }

    #[Route('/admin/history', name: 'admin_history')]
    public function history(): Response
    {
        return $this->render('admin/history.html.twig');
    }

    #[Route('/admin/addpiece', name: 'app_addpiece')]
    public function addpiece(Request $request,ValidatorInterface $validator): Response
    { 
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
            // Implement stock addition logic here
            $stock = new Stock();
            $form = $this->createForm(AddStockType::class, $stock);
            $form->handleRequest($request);
            //ici on vas vérifie que les donénessont correctes
            
            if ($form->isSubmitted() && $form->isValid()) { // Set the default status
                $stock->setActive('True');
                $this->entityManager->persist($stock);
                $this->entityManager->flush();
    
                return $this->redirectToRoute('admin_catalogue');
            }

    
        return $this->render('admin/form/addpiece.html.twig', [
            'controller_name' => 'HomeController',
            'form' => $form->createView(),
        ]);


    }

    /**
     * méthode qui va supprimer une pièce
     * @Route("/admin/deletepiece/{id}", name="app_deletepiece")
     * @param Stock $stock
     * @return Response
     */
    #[Route('/admin/deletepiece/{id}', name: 'app_deletepiece')]
    public function deletepiece(Stock $stock): Response
    {
        $this->entityManager->remove($stock);
        $this->entityManager->flush();

        $this->addFlash('success', 'Ticket deleted successfully.');
        return $this->redirectToRoute('admin_catalogue');

    }





    #[Route('/admin/editpiece/{id}', name: 'app_editpiece')]
    public function updateticket($id): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $piece= $this->entityManager->getRepository(Stock::class)->findOneBy(
            ['id' => $id]
        );
    
        return $this->render('admin/form/editpiece.html.twig', [
            'controller_name' => 'HomeController',
            'piece' => $piece,
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
}














