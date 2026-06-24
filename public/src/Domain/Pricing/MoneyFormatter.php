<?php

declare(strict_types=1);

namespace Cs85\Module2A\Domain\Pricing;

final class MoneyFormatter
{
    public static function dollars(int $amountCents): string
    {
        $absoluteAmount = abs($amountCents) / 100;
        $prefix = '';

        if ($amountCents > 0) {
            $prefix = '+';
        } elseif ($amountCents < 0) {
            $prefix = '-';
        }

        return $prefix.'$'.number_format($absoluteAmount, 2);
    }

    public static function total(int $amountCents): string
    {
        return '$'.number_format($amountCents / 100, 2);
    }
}
