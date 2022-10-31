<?php

namespace App\Controller\Customer;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AccountController extends AbstractController
{
    #[Route('/customer/account', name: 'app_customer')]
    public function index(): Response
    {
        return $this->render('customer/index.html.twig');
    }
}
