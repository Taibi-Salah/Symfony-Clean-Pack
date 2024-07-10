<?php

namespace App\Controller;

use App\Entity\Ticket;
use App\Form\TicketType;
use App\Repository\TicketRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class TicketController extends AbstractController
{
    private $repoTk;
    private $manReg;

    public function __construct(TicketRepository $ticketRepository, ManagerRegistry $managerRegistry)
    {
        $this->repoTK = $ticketRepository;
        $this->manReg = $managerRegistry;
    }

    /**
     * creation d'un ticket
     * @param string $id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    #[Route("/createOrEditTk/{id}", name: "createOrEditTk", methods: ['GET', 'POST'])]
    public function createOrEditTk(string $id, Request $request)
    {
        $ticket = $this->repoTk->find(intval($id)) ?? new Ticket(); // objet vide si il n'existe pas dans la table
        $form = $this->createForm(TicketType::class, $ticket);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid() ){
            $message = ($ticket->getId() == '') ? "Le ticket est enregistrée" : "le ticket est modifiée";
            $em = $this->manReg->getManager();
            $em->persist($form->getData());
            $em->flush();
            $this->addFlash('success', $message);
            return $this->redirectToRoute('listTk');
        }

        return $this->render('home/ticket.html.twig', [
            'form' => $form->createView(),
        ]);

    }

    /**
     * suppression d'un ticket
     * @param string $id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    #[Route("/deleteTk/{id}", name: "deleteTk", methods: ['POST'])]
    public function deleteTicket(string $id, Request $request)
    {
        if($this->isCsrfTokenValid('deleteTk'.$id, $request->get('_token'))){
            $em = $this->manReg->getManager();
            $ticket = $this->repoTk->find(intval($id));
            $em->remove($ticket);
            $em->flush();
            $this->addFlash('success', "le ticket a bien été supprimé");
        }
        return $this->redirectToRoute('listTk');

    }


}


