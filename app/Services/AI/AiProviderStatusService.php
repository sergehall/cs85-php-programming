<?php

declare(strict_types=1);

namespace App\Services\AI;

use App\Services\AI\Providers\LmStudioProvider;
use App\Services\AI\Providers\OpenAiProvider;
use Illuminate\Support\Arr;

final class AiProviderStatusService
{
    public function __construct(
        private readonly LmStudioProvider $lmStudio,
        private readonly OpenAiProvider $openAi,
    ) {}

    /**
     * @return array{
     *     checked_at:string,
     *     summary:array{connected:int,total:int,providers_reachable:int,providers_total:int},
     *     providers:list<array<string,mixed>>,
     *     models:list<array<string,mixed>>
     * }
     */
    public function inspect(): array
    {
        $modes = config('ai.modes', []);
        $providerModes = collect(is_array($modes) ? $modes : [])
            ->groupBy(
                fn (array $configuration): string => (string) $configuration['provider'],
                preserveKeys: true,
            );
        $providers = [];

        foreach ([
            'lm_studio' => $this->lmStudio,
            'openai' => $this->openAi,
        ] as $providerName => $provider) {
            $providers[] = $provider->inspectModels(
                $this->statusModes($providerModes->get($providerName, collect())->all()),
            );
        }

        $models = collect($providers)->flatMap(
            fn (array $provider): array => collect($provider['models'])
                ->map(fn (array $model): array => [
                    ...$model,
                    'provider' => $provider['provider'],
                    'provider_label' => $provider['label'],
                    'location' => $provider['location'],
                    'latency_ms' => $provider['latency_ms'],
                ])
                ->all(),
        )->values();

        return [
            'checked_at' => now()->toIso8601String(),
            'summary' => [
                'connected' => $models->where('connected', true)->count(),
                'total' => $models->count(),
                'providers_reachable' => collect($providers)->where('reachable', true)->count(),
                'providers_total' => count($providers),
            ],
            'providers' => $providers,
            'models' => $models->all(),
        ];
    }

    /**
     * @param  array<string, array<string, mixed>>  $modes
     * @return array<string, array{label:string,model:string,model_name:string}>
     */
    private function statusModes(array $modes): array
    {
        return collect($modes)->map(
            fn (array $configuration): array => Arr::only(
                $configuration,
                ['label', 'model', 'model_name'],
            ),
        )->all();
    }
}
