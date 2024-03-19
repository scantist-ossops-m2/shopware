<?php declare(strict_types=1);

namespace Shopware\Core\Checkout\Cart;

use Shopware\Core\Checkout\Cart\Exception\CartTokenNotFoundException;
use Shopware\Core\Framework\Feature;
use Shopware\Core\Framework\Log\Package;
use Shopware\Core\Profiling\Profiler;
use Shopware\Core\System\SalesChannel\SalesChannelContext;

#[Package('checkout')]
class CartCalculator
{
    public function __construct(
        private readonly Processor $processor,
        private readonly AbstractCartPersister $persister,
        private readonly CartFactory $factory,
        private readonly CartRuleLoader $cartRuleLoader
    ) {
    }

    public function calculate(Cart $cart, SalesChannelContext $context): Cart
    {
        return Profiler::trace('cart-calculation', function () use ($cart, $context) {

            if (Feature::isActive('cache_rework')) {
                $behavior = new CartBehavior($context->getPermissions());

                $cart = $this->processor->process($cart, $context, $behavior);

                $cart->markUnmodified();
                foreach ($cart->getLineItems()->getFlat() as $lineItem) {
                    $lineItem->markUnmodified();
                }

                return $cart;
            }

            // validate cart against the context rules
            $cart = $this->cartRuleLoader
                ->loadByCart($context, $cart, new CartBehavior($context->getPermissions()))
                ->getCart();

            $cart->markUnmodified();
            foreach ($cart->getLineItems()->getFlat() as $lineItem) {
                $lineItem->markUnmodified();
            }

            return $cart;
        });
    }
}
