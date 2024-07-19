<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ReportController extends AbstractController
{
    #[Route('/reports', name: 'app_reports')]
    public function index(): Response
    {
        // Logique pour récupérer les rapports depuis la base de données
        // $reports = $reportRepository->findAll();

        return $this->render('home/report.html.twig', [
            'reports' => [], // Remplacez par $reports
        ]);
    }
}
