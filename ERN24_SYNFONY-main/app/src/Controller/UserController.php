<?php

namespace App\Controller;

use App\Entity\Client;
use App\Form\ClientType;  // Ensure this import is correct
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

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
        // If user is already logged in, redirect them to dashboard
        if ($this->getUser()) {
            return $this->redirectToRoute('app_dashboard');
        }

        $form = $this->createForm(LoginType::class);
        $form->handleRequest($request);

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        if ($form->isSubmitted() && $form->isValid()) {
            // Retrieve form data (as an object of Client entity)
            /** @var Client $formData */
            $formData = $form->getData();

            // Get the email entered in the form
            $email = $formData->getEmail();

            // Find the client by email in the database
            $client = $this->entityManager->getRepository(Client::class)->findOneBy(['email' => $email]);

            if (!$client) {
                // Handle non-existent client (email not found)
                $this->addFlash('error', 'Email non trouvé.');
                return $this->redirectToRoute('app_connexion');
            }

            // Check if the password is correct using UserPasswordHasherInterface
            if ($passwordHasher->isPasswordValid($client, $formData->getPassword())) {
                // Password is valid, proceed with authentication
                // This part is usually handled by Symfony's security system automatically

                // Redirect to dashboard upon successful login
                return $this->redirectToRoute('app_dashboard');
            } else {
                // Handle incorrect password
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
        // You can add logic here to fetch data or perform actions needed for the dashboard
        return $this->render('dashboard.html.twig');
    }

    #[Route('/inscription', name: 'app_inscription')]
    public function register(Request $request, UserPasswordHasherInterface $passwordHasher): Response
    {
        $client = new Client();
        $form = $this->createForm(ClientType::class, $client);
        
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Encode the plain password before storing
            $hashedPassword = $passwordHasher->hashPassword($client, $client->getPassword());
            $client->setPassword($hashedPassword);

            $this->entityManager->persist($client);
            $this->entityManager->flush();

            // Redirect to login page after successful registration
            return $this->redirectToRoute('app_connexion');
        }

        return $this->render('home/connexion/inscription.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Route qui va afficher les tickets de l'utilisateur
     * @Route("/tickets", name="app_tickets")
     * @return Response
     */
    #[Route('/mytickets', name: 'app_tickets')]
    public function tickets(): Response
    {
        return $this->render('user/ticket.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }

    /**Route qui va afficher la gestion de ticket pour les techniciens */
    #[Route('/technicien', name: 'app_ticket')]
    public function ticket(): Response
    {
        return $this->render('user/technicien.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }
}






