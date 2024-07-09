<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\UserRepository;

class AdminController extends AbstractController
{
    #[Route('/admin', name: 'admin_dashboard')]
    public function index(UserRepository $userRepository): Response
    {
        // Retrieve all users from the database
        $users = $userRepository->findAll();
        $this->denyAccessUnlessGranted('ROLE_USER'); //seul l'utilisateur connecté en tant qu'admin peut accéder à cette page
        return $this->render('admin/index.html.twig', [
            'users' => $users,
        ]);
    }

    /**
     * Route pour afficher le catalogue des pièces
     * @Route("/admin/catalogue", name="app_catalogue")
     * @return Response
     */
    #[Route('/admin/catalogue', name: 'admin_catalogue')]
    public function catalogue(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        return $this->render('admin/catalogue.html.twig');
    }


    /**
     * Route pour afficher l'entrée et la sortie des pièces
     * @Route("/admin/history", name="app_history")
     * @return Response
     */
    #[Route('/admin/history', name: 'admin_history')]
    public function history(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        return $this->render('admin/history.html.twig');
    }

     /**
     * Route qui va afficher le formulaire d'ajout d'une pièce
     * @Route("/addpiece", name="app_addpiece")
     * @return Response
     */
    #[Route('/admin/addpiece', name: 'app_addpiece')]
    public function addpiece(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        return $this->render('admin/form/addpiece.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }

    /**
     * Route qui va afficher le formulaire de modification d'un ticket
     * @Route("/updateticket", name="app_updateticket")
     * @return Response
     */
    #[Route('/admin/editpiece', name: 'app_editpiece')]
    public function updateticket(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        return $this->render('admin/form/editpiece.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }


}




