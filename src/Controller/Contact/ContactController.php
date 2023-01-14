<?php

namespace App\Controller\Contact;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends AbstractController
{
    #[Route('/contact/contact', name: 'app_contact')]
    public function index(): Response
    {
        return $this->render('contact/contact/index.html.twig', [
            'controller_name' => 'ContactController',
        ]);
    }
}
