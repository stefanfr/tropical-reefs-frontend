<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends AbstractController
{
    #[Route('/category/{categoryPath}', name: 'app_category')]
    public function index(string $categoryPath): Response
    {
        return $this->render('catalog/category/index.html.twig');
    }
}
