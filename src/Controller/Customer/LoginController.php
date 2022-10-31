<?php

namespace App\Controller\Customer;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LoginController extends AbstractController
{
    #[Route('/customer/account/login', name: 'app_customer_login')]
    public function index(): Response
    {
        return $this->render('customer/login/index.html.twig');
    }
}