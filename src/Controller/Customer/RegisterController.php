<?php

namespace App\Controller\Customer;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RegisterController extends AbstractController
{
    #[Route('/customer/account/register', name: 'app_customer_register')]
    public function index(): Response
    {
        return $this->render('customer/register/index.html.twig');
    }
}