<?php

namespace App\Twig\Components\Core\Header;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\Attribute\PreReRender;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent(name: 'header_cart', template: 'components/core/header/cart.html.twig')]
final class HeaderCartComponent
{
    use DefaultActionTrait;

    public function __construct(
        protected RequestStack $requestStack,
    )
    {
    }

    public function getCartItemCount(): int
    {
        $session = $this->requestStack->getSession();

        return $session->get('checkout_cart_item_count') ?? 0;
    }
}
