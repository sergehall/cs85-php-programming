<?php

declare(strict_types=1);

namespace Cs85\Module2A\Domain\Order;

final class TShirtOrder
{
    public function __construct(
        private readonly string $size,
        private readonly string $color,
        private readonly bool $isCustomized,
        private readonly string $customerFirstName,
    ) {}

    public function size(): string
    {
        return $this->size;
    }

    public function color(): string
    {
        return $this->color;
    }

    public function isCustomized(): bool
    {
        return $this->isCustomized;
    }

    public function customerFirstName(): string
    {
        return $this->customerFirstName;
    }
}
