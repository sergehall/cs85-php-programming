<?php

declare(strict_types=1);

namespace Tests\Unit;

use Cs85\Module2A\Application\QuoteTShirtOrder;
use Cs85\Module2A\Domain\Pricing\TShirtPriceCalculator;
use Tests\TestCase;

class Module2APricingTest extends TestCase
{
    public function test_module_2a_pricing_rules_cover_required_order_combinations(): void
    {
        $quoteOrder = new QuoteTShirtOrder(new TShirtPriceCalculator);

        $cases = [
            [
                'name' => 'XL premium customized standard-length name',
                'config' => [
                    'size' => 'XL',
                    'color' => 'Sunset Orange',
                    'is_customized' => true,
                    'customer_first_name' => 'Sergio',
                ],
                'expected_cents' => 3500,
            ],
            [
                'name' => 'L standard color customized long name',
                'config' => [
                    'size' => 'L',
                    'color' => 'Black',
                    'is_customized' => true,
                    'customer_first_name' => 'Sergioo',
                ],
                'expected_cents' => 2825,
            ],
            [
                'name' => 'M premium color no customization',
                'config' => [
                    'size' => 'M',
                    'color' => 'Ocean Blue',
                    'is_customized' => false,
                    'customer_first_name' => 'Alex',
                ],
                'expected_cents' => 2450,
            ],
            [
                'name' => 'XL standard color without customization',
                'config' => [
                    'size' => 'XL',
                    'color' => 'White',
                    'is_customized' => false,
                    'customer_first_name' => 'Alex',
                ],
                'expected_cents' => 2500,
            ],
        ];

        foreach ($cases as $case) {
            $quote = $quoteOrder->handle($case['config']);

            $this->assertSame(
                $case['expected_cents'],
                $quote->totalCents(),
                "{$case['name']} should calculate the expected total.",
            );
        }
    }
}
