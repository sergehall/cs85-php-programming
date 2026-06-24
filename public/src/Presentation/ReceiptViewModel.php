<?php

declare(strict_types=1);

namespace Cs85\Module2A\Presentation;

use Cs85\Module2A\Domain\Pricing\MoneyFormatter;
use Cs85\Module2A\Domain\Pricing\PriceQuote;

final class ReceiptViewModel
{
    public function __construct(
        private readonly PriceQuote $quote,
    ) {}

    /**
     * @return array{size:string,color:string,customized:string,customer_first_name:string}
     */
    public function orderSummary(): array
    {
        $order = $this->quote->order();

        return [
            'size' => $order->size(),
            'color' => $order->color(),
            'customized' => $order->isCustomized() ? 'Yes' : 'No',
            'customer_first_name' => $order->customerFirstName(),
        ];
    }

    /**
     * @return list<array{label:string,amount:string,is_discount:bool}>
     */
    public function lineItems(): array
    {
        $items = [];

        foreach ($this->quote->lineItems() as $index => $lineItem) {
            $items[] = [
                'label' => $lineItem->label(),
                'amount' => $index === 0
                    ? MoneyFormatter::total($lineItem->amountCents())
                    : MoneyFormatter::dollars($lineItem->amountCents()),
                'is_discount' => $lineItem->isDiscount(),
            ];
        }

        return $items;
    }

    public function total(): string
    {
        return MoneyFormatter::total($this->quote->totalCents());
    }
}
