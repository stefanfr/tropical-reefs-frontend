<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class CmsPageController extends AbstractController
{
    public function index(array $magentoMatch): Response
    {
        return new Response('');
    }
}