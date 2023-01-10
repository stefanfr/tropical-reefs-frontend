<?php

namespace App\Controller\Manager;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    #[Route('/manager', name: 'app_manager_dashboard')]
    public function index(): Response
    {
        if ( ! $this->getUser()) {
            return $this->redirectToRoute('app_manager_auth_login');
        }
        return $this->render('manager/dashboard/index.html.twig', [
            'controller_name' => 'DashboardController',
        ]);
    }
}
