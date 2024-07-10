<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/inscription', name: 'app_inscription')]
    public function register(Request $request, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Encode the plain password before storing
            $hashedPassword = $passwordHasher->hashPassword($user, $user->getPlainPassword());
            $user->setPassword($hashedPassword);

            $this->entityManager->persist($user);
            $this->entityManager->flush();

            // Redirect to login page after successful registration
            return $this->redirectToRoute('app_connexion');
        }

        return $this->render('home/connexion/inscription.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/mytickets', name: 'app_tickets')]
    public function tickets(): Response
    {
        return $this->render('user/ticket.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }

    #[Route('/technicien', name: 'app_ticket')]
    public function ticket(): Response
    {
    
        return $this->render('user/technicien.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }
    
   
}







