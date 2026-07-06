<?php

declare(strict_types=1);

namespace Cs85\Module6A\Models;

final class PhotographyProject
{
    private const BASE_PRICES = [
        'mini' => 18000,
        'standard' => 30000,
        'premium' => 45000,
    ];

    private const INCLUDED_HOURS = [
        'mini' => 1.0,
        'standard' => 2.0,
        'premium' => 4.0,
    ];

    private const INCLUDED_EDITED_PHOTOS = [
        'mini' => 10,
        'standard' => 20,
        'premium' => 30,
    ];

    public function __construct(
        public readonly string $clientName,
        public readonly string $serviceType,
        public readonly string $package,
        public readonly float $hours,
        public readonly int $editedPhotos,
        public readonly string $locationType,
        public readonly bool $rushDelivery,
        public readonly int $depositPaidCents,
        public readonly string $projectNote
    ) {
    }

    public function summary(): string
    {
        return sprintf(
            '%s booked a %s %s session for %.1f hours with %d edited photos at %s.',
            $this->clientName,
            $this->formatServiceType(),
            $this->formatPackage(),
            $this->hours,
            $this->editedPhotos,
            $this->formatLocationType()
        );
    }

    public function quoteTotalCents(): int
    {
        $basePrice = self::BASE_PRICES[$this->package] ?? self::BASE_PRICES['standard'];
        $includedHours = self::INCLUDED_HOURS[$this->package] ?? self::INCLUDED_HOURS['standard'];
        $includedEditedPhotos = self::INCLUDED_EDITED_PHOTOS[$this->package] ?? self::INCLUDED_EDITED_PHOTOS['standard'];

        $extraHoursCents = (int) round(max(0, $this->hours - $includedHours) * 12000);
        $extraPhotosCents = max(0, $this->editedPhotos - $includedEditedPhotos) * 500;
        $locationCents = $this->locationFeeCents();
        $rushCents = $this->rushDelivery ? 4500 : 0;

        return (int) round(
            ($basePrice + $extraHoursCents + $extraPhotosCents + $locationCents + $rushCents)
            * $this->serviceMultiplier()
        );
    }

    public function depositDueCents(): int
    {
        $requiredDeposit = (int) round($this->quoteTotalCents() * 0.3);

        return max(0, $requiredDeposit - $this->depositPaidCents);
    }

    public function workflowDecision(): string
    {
        if ($this->depositDueCents() > 0) {
            return 'Send quote and collect deposit before confirming the session.';
        }

        if ($this->rushDelivery || $this->locationType === 'out_of_city') {
            return 'Deposit is covered; confirm logistics and delivery timing with the client.';
        }

        return 'Ready to confirm the booking and prepare the client timeline.';
    }

    public function complexityLabel(): string
    {
        $score = 0;
        $score += $this->hours >= 4 ? 2 : 0;
        $score += $this->editedPhotos > 30 ? 1 : 0;
        $score += $this->locationType === 'out_of_city' ? 2 : 0;
        $score += $this->rushDelivery ? 2 : 0;
        $score += $this->serviceType === 'commercial' ? 2 : 0;

        return match (true) {
            $score >= 5 => 'High complexity',
            $score >= 2 => 'Medium complexity',
            default => 'Simple booking',
        };
    }

    public function formattedQuoteTotal(): string
    {
        return $this->formatMoney($this->quoteTotalCents());
    }

    public function formattedDepositDue(): string
    {
        return $this->formatMoney($this->depositDueCents());
    }

    public function formatServiceType(): string
    {
        return ucwords(str_replace('_', ' ', $this->serviceType));
    }

    public function formatPackage(): string
    {
        return ucfirst($this->package);
    }

    public function formatLocationType(): string
    {
        return ucwords(str_replace('_', ' ', $this->locationType));
    }

    private function serviceMultiplier(): float
    {
        return match ($this->serviceType) {
            'fashion', 'love_story', 'family', 'content' => 1.15,
            'commercial' => 1.25,
            default => 1.0,
        };
    }

    private function locationFeeCents(): int
    {
        return match ($this->locationType) {
            'studio' => 5000,
            'client_place' => 3500,
            'out_of_city' => 12000,
            default => 0,
        };
    }

    private function formatMoney(int $cents): string
    {
        return '$'.number_format($cents / 100, 2);
    }
}
