<?php

namespace App\Services\Modules\Module2B;

use DateTimeImmutable;
use DateTimeZone;
use Throwable;

final class CosmicCalendarBuilder
{
    private const string FIRST_NAME = 'Serge';

    private const string TIMEZONE = 'America/Los_Angeles';

    private const string TIME_API_URL = 'https://timeapi.io/api/time/current/zone?timeZone=America%2FLos_Angeles';

    /**
     * @return array{
     *     firstName: string,
     *     nameLength: int,
     *     dayOfYear: int,
     *     currentMonth: int,
     *     dateTime: string,
     *     usedFallback: bool,
     *     days: array<int, array{number: int, cssClass: string, rule: string}>
     * }
     */
    public function build(?string $firstName = null): array
    {
        $firstName ??= self::FIRST_NAME;
        $nameLength = strlen($firstName);

        [$date, $currentMonth, $dateTime, $usedFallback] = $this->currentCourseDate();
        $dayOfYear = (int) $date->format('z') + 1;

        return [
            'firstName' => $firstName,
            'nameLength' => $nameLength,
            'dayOfYear' => $dayOfYear,
            'currentMonth' => $currentMonth,
            'dateTime' => $dateTime,
            'usedFallback' => $usedFallback,
            'days' => $this->buildDays($nameLength, $dayOfYear, $currentMonth),
        ];
    }

    /**
     * @return array{0: DateTimeImmutable, 1: int, 2: string, 3: bool}
     */
    private function currentCourseDate(): array
    {
        try {
            $apiData = $this->fetchTimeApiData();

            if (is_object($apiData) && isset($apiData->dateTime, $apiData->month)) {
                $date = new DateTimeImmutable((string) $apiData->dateTime, new DateTimeZone(self::TIMEZONE));

                return [
                    $date,
                    (int) $apiData->month,
                    (string) $apiData->dateTime,
                    false,
                ];
            }
        } catch (Throwable) {
            // The assignment should still render when the external API is blocked.
        }

        $date = new DateTimeImmutable('now', new DateTimeZone(self::TIMEZONE));

        return [
            $date,
            (int) $date->format('n'),
            $date->format(DATE_ATOM),
            true,
        ];
    }

    private function fetchTimeApiData(): mixed
    {
        $context = stream_context_create([
            'http' => [
                'timeout' => 3,
            ],
        ]);

        $json = @file_get_contents(self::TIME_API_URL, false, $context);

        if ($json === false) {
            return null;
        }

        return json_decode($json);
    }

    /**
     * @return array<int, array{number: int, cssClass: string, rule: string}>
     */
    private function buildDays(int $nameLength, int $dayOfYear, int $currentMonth): array
    {
        $days = [];

        for ($number = $nameLength; $number <= $dayOfYear; $number++) {
            $divisibleByName = $number % $nameLength === 0;
            $divisibleByMonth = $number % $currentMonth === 0;

            $days[] = [
                'number' => $number,
                'cssClass' => $this->dayCssClass($divisibleByName, $divisibleByMonth),
                'rule' => $this->dayRule($divisibleByName, $divisibleByMonth),
            ];
        }

        return $days;
    }

    private function dayCssClass(bool $divisibleByName, bool $divisibleByMonth): string
    {
        return match (true) {
            $divisibleByName && $divisibleByMonth => 'day-box cosmic-both',
            $divisibleByName => 'day-box cosmic-name',
            $divisibleByMonth => 'day-box cosmic-month',
            default => 'day-box',
        };
    }

    private function dayRule(bool $divisibleByName, bool $divisibleByMonth): string
    {
        return match (true) {
            $divisibleByName && $divisibleByMonth => 'Divisible by name length and month',
            $divisibleByName => 'Divisible by name length',
            $divisibleByMonth => 'Divisible by month',
            default => 'Regular day number',
        };
    }
}
