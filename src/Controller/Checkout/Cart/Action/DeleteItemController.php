<?php

namespace App\Controller\Checkout\Cart\Action;

use App\Service\Api\Magento\Checkout\MagentoCheckoutCartApiService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DeleteItemController extends AbstractController
{
    public function __construct(
        protected MagentoCheckoutCartApiService $magentoCheckoutCartApiService,
    )
    {
    }

    #[Route('/checkout/cart/delete/{uid}', name: 'app_checkout_cart_delete', methods: ['DELETE', 'POST'])]
    public function index(Request $request, string $uid): Response
    {
        $this->magentoCheckoutCartApiService->deleteItem($uid);

        return $this->redirectToRoute('app_checkout_cart');
    }
}