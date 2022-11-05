<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;

class RedirectController extends AbstractController
{
    public function index(array $magentoMatch): RedirectResponse
    {
        return new RedirectResponse(
            $magentoMatch['relative_url'],
            $magentoMatch['redirectCode'],
        );
    }
}