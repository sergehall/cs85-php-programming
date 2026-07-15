@props(['value'])

<span {{ $attributes->class(['block min-w-0 max-w-full truncate'])->merge(['title' => $value]) }}>{{ $value }}</span>
