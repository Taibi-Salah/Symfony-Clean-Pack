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

        return $this->render('admin/index.html.twig', [
            'users' => $users,
        ]);
    }
}




