<?php

namespace App\Controller;

use App\Entity\Client;
use App\Form\ClientType; // Ensure this use statement is present
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserController extends AbstractController
{
    /**
     * Route qui permet de se connecter
     * @Route("/connexion", name="app_connexion")
     * @return Response
     */
    #[Route('/connexion', name: 'app_connexion')]
    public function login(): Response
    {
        return $this->render('home/connexion/connexion.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }

    /**
     * Route qui permet de s'inscrire
     * @Route("/inscription", name="app_inscription")
     * @return Response
     */
    #[Route('/inscription', name: 'app_inscription')]
    public function register(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        $client = new Client();
        $form = $this->createForm(ClientType::class, $client);
        
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $client->setPassword(
                $passwordHasher->hashPassword(
                    $client,
                    $form->get('password')->getData()
                )
            );

            $entityManager->persist($client);
            $entityManager->flush();

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




