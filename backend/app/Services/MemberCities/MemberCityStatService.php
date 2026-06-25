<?php

declare(strict_types=1);

namespace App\Services\MemberCities;

use App\Enums\MemberCityStatKey;
use App\Models\MemberCity;
use App\Models\MemberCityStat;

class MemberCityStatService
{
    public function getPublicStats(?string $locale = null): array
    {
        $locale = $locale ?? app()->getLocale();
        $isAr = $locale === 'ar';

        $activeCitiesCount = MemberCity::query()->where('is_active', true)->count();

        $order = [
            MemberCityStatKey::Countries->value => 0,
            MemberCityStatKey::Cities->value => 1,
            MemberCityStatKey::Members->value => 2,
        ];

        return MemberCityStat::query()
            ->get()
            ->sortBy(fn (MemberCityStat $stat) => $order[$stat->key] ?? 99)
            ->map(function (MemberCityStat $stat) use ($isAr, $activeCitiesCount) {
                return [
                    'key' => $stat->key,
                    'value' => $this->resolveValue($stat, $activeCitiesCount),
                    'label' => $isAr ? $stat->label_ar : $stat->label_en,
                    'unit' => $isAr ? $stat->unit_ar : $stat->unit_en,
                ];
            })
            ->values()
            ->all();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function getAdminStats(): array
    {
        $activeCitiesCount = MemberCity::query()->where('is_active', true)->count();

        $order = [
            MemberCityStatKey::Countries->value => 0,
            MemberCityStatKey::Cities->value => 1,
            MemberCityStatKey::Members->value => 2,
        ];

        return MemberCityStat::query()
            ->get()
            ->sortBy(fn (MemberCityStat $stat) => $order[$stat->key] ?? 99)
            ->map(function (MemberCityStat $stat) use ($activeCitiesCount) {
                return [
                    'key' => $stat->key,
                    'value' => $stat->auto_calculate ? null : $stat->value,
                    'resolvedValue' => $this->resolveValue($stat, $activeCitiesCount),
                    'label' => [
                        'ar' => $stat->label_ar,
                        'en' => $stat->label_en,
                    ],
                    'unit' => [
                        'ar' => $stat->unit_ar,
                        'en' => $stat->unit_en,
                    ],
                    'autoCalculate' => $stat->auto_calculate,
                ];
            })
            ->values()
            ->all();
    }

    /**
     * @param  array<int, array<string, mixed>>  $items
     */
    public function updateStats(array $items): void
    {
        foreach ($items as $item) {
            $key = $item['key'] ?? null;
            if (! $key) {
                continue;
            }

            $stat = MemberCityStat::query()->findOrFail($key);
            $autoCalculate = (bool) ($item['autoCalculate'] ?? $item['auto_calculate'] ?? false);

            $payload = [
                'auto_calculate' => $autoCalculate,
            ];

            if (array_key_exists('label', $item) && is_array($item['label'])) {
                $payload['label_ar'] = $item['label']['ar'] ?? $stat->label_ar;
                $payload['label_en'] = $item['label']['en'] ?? $stat->label_en;
            }

            if (array_key_exists('unit', $item) && is_array($item['unit'])) {
                $payload['unit_ar'] = $item['unit']['ar'] ?? $stat->unit_ar;
                $payload['unit_en'] = $item['unit']['en'] ?? $stat->unit_en;
            }

            if ($autoCalculate) {
                $payload['value'] = null;
            } elseif (array_key_exists('value', $item)) {
                $payload['value'] = $item['value'];
            }

            $stat->update($payload);
        }
    }

    private function resolveValue(MemberCityStat $stat, int $activeCitiesCount): int
    {
        if ($stat->auto_calculate && $stat->key === MemberCityStatKey::Cities->value) {
            return $activeCitiesCount;
        }

        return (int) ($stat->value ?? 0);
    }
}
