<?php

declare(strict_types=1);

namespace Cs85\Module2A\Domain\Pricing;

use Cs85\Module2A\Domain\Order\TShirtOrder;

final class TShirtPriceCalculator
{
    private const BASE_PRICE_CENTS = 2250;

    private const LARGE_UPCHARGE_CENTS = 175;

    private const EXTRA_LARGE_UPCHARGE_CENTS = 250;

    private const PREMIUM_COLOR_UPCHARGE_CENTS = 200;

    private const CUSTOM_TEXT_FEE_CENTS = 500;

    private const EXTRA_LARGE_HANDLING_FEE_CENTS = 300;

    private const LONG_NAME_DISCOUNT_CENTS = -100;

    public function quote(TShirtOrder $order): PriceQuote
    {
        $lineItems = [
            new LineItem('Base Price', self::BASE_PRICE_CENTS),
        ];

        if ($order->size() === 'L') {
            $lineItems[] = new LineItem('Size L Upcharge', self::LARGE_UPCHARGE_CENTS);
        } elseif ($order->size() === 'XL') {
            $lineItems[] = new LineItem('Size XL Upcharge', self::EXTRA_LARGE_UPCHARGE_CENTS);
        } else {
            $lineItems[] = new LineItem('Standard Size', 0);
        }

        if ($order->color() === 'Sunset Orange' || $order->color() === 'Ocean Blue') {
            $lineItems[] = new LineItem('Premium Color: '.$order->color(), self::PREMIUM_COLOR_UPCHARGE_CENTS);
        } else {
            $lineItems[] = new LineItem('Standard Color', 0);
        }

        if ($order->isCustomized()) {
            $lineItems[] = new LineItem('Custom Text Fee', self::CUSTOM_TEXT_FEE_CENTS);
        } else {
            $lineItems[] = new LineItem('No Custom Text', 0);
        }

        if ($order->isCustomized() && $order->size() === 'XL') {
            $lineItems[] = new LineItem('XL Custom Handling Fee', self::EXTRA_LARGE_HANDLING_FEE_CENTS);
        }

        if (strlen($order->customerFirstName()) > 6) {
            $lineItems[] = new LineItem('Long Name Discount', self::LONG_NAME_DISCOUNT_CENTS);
        } else {
            $lineItems[] = new LineItem('Name Discount', 0);
        }

        return new PriceQuote($order, $lineItems);
    }
}
