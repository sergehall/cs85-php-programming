<?php

declare(strict_types=1);

namespace Cs85\Module2A\Domain\Pricing;

use Cs85\Module2A\Domain\Order\TShirtOrder;

final class PriceQuote
{
    /**
     * @param  list<LineItem>  $lineItems
     */
    public function __construct(
        private readonly TShirtOrder $order,
        private readonly array $lineItems,
    ) {}

    public function order(): TShirtOrder
    {
        return $this->order;
    }

    /**
     * @return list<LineItem>
     */
    public function lineItems(): array
    {
        return $this->lineItems;
    }

    public function totalCents(): int
    {
        $total = 0;

        foreach ($this->lineItems as $lineItem) {
            $total += $lineItem->amountCents();
        }

        return $total;
    }
}
