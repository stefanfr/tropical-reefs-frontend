<?php

namespace App\Controller\Checkout;

use App\Service\Api\Magento\Checkout\MagentoCheckoutCartApiService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

abstract class AbstractCheckoutController extends AbstractController
{
    public function __construct(
        protected MagentoCheckoutCartApiService $magentoCheckoutCartApiService,
    )
    {
    }
}
