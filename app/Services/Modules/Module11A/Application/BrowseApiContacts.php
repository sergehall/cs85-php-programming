<?php

declare(strict_types=1);

namespace App\Services\Modules\Module11A\Application;

use App\Services\Modules\Module11A\Domain\ApiContact;

final class BrowseApiContacts
{
    /**
     * @var array<string, string>
     */
    public const SORTS = [
        'name_asc' => 'Name A-Z',
        'name_desc' => 'Name Z-A',
        'city_asc' => 'City A-Z',
        'company_asc' => 'Company A-Z',
    ];

    /**
     * @return array{
     *     contacts: list<ApiContact>,
     *     total: int,
     *     returned: int,
     *     companies: int,
     *     cities: int
     * }
     */
    public function handle(ApiContactResult $result, string $search, string $sort, int $limit): array
    {
        $contacts = $result->contacts;
        $needle = mb_strtolower(trim($search));

        if ($needle !== '') {
            $contacts = array_values(array_filter(
                $contacts,
                static fn (ApiContact $contact): bool => str_contains(
                    mb_strtolower(implode(' ', [
                        $contact->name,
                        $contact->username,
                        $contact->email,
                        $contact->company,
                        $contact->city,
                    ])),
                    $needle,
                ),
            ));
        }

        usort($contacts, static function (ApiContact $left, ApiContact $right) use ($sort): int {
            return match ($sort) {
                'name_desc' => strcasecmp($right->name, $left->name),
                'city_asc' => strcasecmp($left->city, $right->city) ?: strcasecmp($left->name, $right->name),
                'company_asc' => strcasecmp($left->company, $right->company) ?: strcasecmp($left->name, $right->name),
                default => strcasecmp($left->name, $right->name),
            };
        });

        $returned = count($contacts);

        return [
            'contacts' => array_slice($contacts, 0, $limit),
            'total' => count($result->contacts),
            'returned' => min($returned, $limit),
            'companies' => count(array_unique(array_map(
                static fn (ApiContact $contact): string => $contact->company,
                $result->contacts,
            ))),
            'cities' => count(array_unique(array_map(
                static fn (ApiContact $contact): string => $contact->city,
                $result->contacts,
            ))),
        ];
    }
}
