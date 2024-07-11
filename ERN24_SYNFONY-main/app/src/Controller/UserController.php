<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Ticket;
use App\Form\UserType;
use App\Form\LoginType;
use App\Form\TicketType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class UserController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/connexion', name: 'app_connexion')]
    public function login(AuthenticationUtils $authenticationUtils, Request $request, UserPasswordHasherInterface $passwordHasher): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_dashboard');
        }

        $form = $this->createForm(LoginType::class);
        $form->handleRequest($request);

        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var User $formData */
            $formData = $form->getData();
            $email = $formData->getEmail();
            $client = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);

            if (!$client) {
                $this->addFlash('error', 'Email non trouvé.');
                return $this->redirectToRoute('app_connexion');
            }

            if ($passwordHasher->isPasswordValid($client, $formData->getPassword())) {
                return $this->redirectToRoute('app_dashboard');
            } else {
                $this->addFlash('error', 'Mot de passe incorrect.');
                return $this->redirectToRoute('app_connexion');
            }
        }

        return $this->render('home/connexion/connexion.html.twig', [
            'form' => $form->createView(),
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route('/dashboard', name: 'app_dashboard')]
    public function dashboard(): Response
    {
        return $this->render('home/dashboard.html.twig');
    }

    #[Route('/inscription', name: 'app_inscription')]
    public function register(Request $request, UserPasswordHasherInterface $passwordHasher, ValidatorInterface $validator): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Encode the plain password before storing
            $user->setPassword($passwordHasher->hashPassword($user, $user->getPassword()));
           
            $this->entityManager->persist($user);
            $this->entityManager->flush();
            // Redirect to login page after successful registration
            return $this->redirectToRoute('app_login');
            }
        
        return $this->render('home/connexion/inscription.html.twig', [
            'form' => $form->createView(),
        ]);
        }


    #[Route('/mytickets', name: 'app_tickets')]
    public function tickets(Request $request): Response
    {
        $ticket = new Ticket();
        $form = $this->createForm(TicketType::class, $ticket);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $ticket->setUser($this->getUser());
            $ticket->setStatus('open');

            $this->entityManager->persist($ticket);
            $this->entityManager->flush();

            return $this->redirectToRoute('app_tickets');
        }

        $tickets = $this->entityManager->getRepository(Ticket::class)->findBy([
            'user' => $this->getUser(),
            'status' => 'open'
        ]);
        $closedTickets = $this->entityManager->getRepository(Ticket::class)->findBy([
            'user' => $this->getUser(),
            'status' => 'closed'
        ]);

        return $this->render('user/ticket.html.twig', [
            'tickets' => $tickets,
            'closed_tickets' => $closedTickets,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/technicien', name: 'app_ticket')]
    public function technicien(): Response
    {
        $technicien = $this->getUser();
        $tickets = $this->entityManager->getRepository(Ticket::class)->findBy([
            'technicien' => $technicien,
            'status' => 'open'
        ]);
        $closedTickets = $this->entityManager->getRepository(Ticket::class)->findBy([
            'technicien' => $technicien,
            'status' => 'closed'
        ]);

        return $this->render('user/technicien.html.twig', [
            'tickets' => $tickets,
            'closed_tickets' => $closedTickets,
        ]);
    }

    #[Route('/ticket/edit/{id}', name: 'ticket_edit')]
    public function edit(Ticket $ticket, Request $request): Response
    {
        $form = $this->createForm(TicketType::class, $ticket);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();
            return $this->redirectToRoute('app_ticket');
        }

        return $this->render('ticket/edit.html.twig', [
            'form' => $form->createView(),
            'ticket' => $ticket,
        ]);
    }

    #[Route('/ticket/close/{id}', name: 'ticket_close')]
    public function close(Ticket $ticket): Response
    {
        $ticket->setStatus('closed');
        $ticket->setDateEnd(new \DateTime());

        $this->entityManager->flush();

        $this->addFlash('success', 'Ticket closed successfully.');
        return $this->redirectToRoute('app_ticket');
    }
}












