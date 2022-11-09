<?php

namespace App\Controller\Checkout\Cart\Action;

use App\Service\Api\Magento\Checkout\MagentoCheckoutApiService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EmptyCartController extends AbstractController
{
    public function __construct(
        protected MagentoCheckoutApiService $magentoCheckoutApiService,
    )
    {
    }

    #[Route(path: '/checkout/cart/empty', name: 'app_checkout_cart_empty', methods: ['GET'])]
    public function index(): Response
    {
        $this->magentoCheckoutApiService->getQuoteMaskId(true);

        return $this->redirectToRoute('app_checkout_cart');
    }
}