<?php

declare(strict_types=1);

namespace Cs85\Module2A\Application;

use Cs85\Module2A\Domain\Order\TShirtOrder;
use Cs85\Module2A\Domain\Pricing\PriceQuote;
use Cs85\Module2A\Domain\Pricing\TShirtPriceCalculator;

final class QuoteTShirtOrder
{
    public function __construct(
        private readonly TShirtPriceCalculator $calculator,
    ) {}

    /**
     * @param  array{size:string,color:string,is_customized:bool,customer_first_name:string}  $config
     */
    public function handle(array $config): PriceQuote
    {
        $order = new TShirtOrder(
            size: $config['size'],
            color: $config['color'],
            isCustomized: $config['is_customized'],
            customerFirstName: $config['customer_first_name'],
        );

        return $this->calculator->quote($order);
    }
}
