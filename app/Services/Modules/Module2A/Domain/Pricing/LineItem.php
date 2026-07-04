<?php

declare(strict_types=1);

namespace Cs85\Module2A\Domain\Pricing;

final class LineItem
{
    public function __construct(
        private readonly string $label,
        private readonly int $amountCents,
    ) {}

    public function label(): string
    {
        return $this->label;
    }

    public function amountCents(): int
    {
        return $this->amountCents;
    }

    public function isDiscount(): bool
    {
        return $this->amountCents < 0;
    }
}
