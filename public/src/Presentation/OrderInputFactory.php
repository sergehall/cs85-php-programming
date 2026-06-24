<?php

declare(strict_types=1);

namespace Cs85\Module2A\Presentation;

final class OrderInputFactory
{
    private const ALLOWED_SIZES = ['S', 'M', 'L', 'XL'];

    private const ALLOWED_COLORS = ['Black', 'White', 'Sunset Orange', 'Ocean Blue'];

    /**
     * @param  array{size:string,color:string,is_customized:bool,customer_first_name:string}  $defaults
     * @param  array<string,mixed>  $query
     * @return array{size:string,color:string,is_customized:bool,customer_first_name:string}
     */
    public function fromQuery(array $defaults, array $query): array
    {
        if (! isset($query['calculate'])) {
            return $defaults;
        }

        $size = $this->stringValue($query, 'size', $defaults['size']);
        $color = $this->stringValue($query, 'color', $defaults['color']);
        $customerFirstName = trim($this->stringValue($query, 'customer_first_name', $defaults['customer_first_name']));

        if (! in_array($size, self::ALLOWED_SIZES, true)) {
            $size = $defaults['size'];
        }

        if (! in_array($color, self::ALLOWED_COLORS, true)) {
            $color = $defaults['color'];
        }

        if ($customerFirstName === '') {
            $customerFirstName = $defaults['customer_first_name'];
        }

        return [
            'size' => $size,
            'color' => $color,
            'is_customized' => isset($query['is_customized']),
            'customer_first_name' => $customerFirstName,
        ];
    }

    /**
     * @param  array<string,mixed>  $source
     */
    private function stringValue(array $source, string $key, string $fallback): string
    {
        if (! isset($source[$key]) || ! is_string($source[$key])) {
            return $fallback;
        }

        return $source[$key];
    }
}
