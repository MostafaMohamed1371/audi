<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\SiteSetting;

class ContactInfoService
{
    /** @var array<string, string> */
    private const ADMIN_FIELD_MAP = [
        'titleAr' => 'contact.title',
        'titleEn' => 'contact.title',
        'subtitleAr' => 'contact.subtitle',
        'subtitleEn' => 'contact.subtitle',
        'addressLabelAr' => 'contact.address_label',
        'addressLabelEn' => 'contact.address_label',
        'addressAr' => 'contact.address',
        'addressEn' => 'contact.address',
        'mapTitleAr' => 'contact.map_title',
        'mapTitleEn' => 'contact.map_title',
        'mapEmbedUrlAr' => 'contact.map_embed_url',
        'mapEmbedUrlEn' => 'contact.map_embed_url',
    ];

    public function getPublicPayload(?string $locale = null): array
    {
        $locale = $locale ?? app()->getLocale();
        $isAr = $locale === 'ar';

        $settings = SiteSetting::query()
            ->where('group', 'contact')
            ->pluck($isAr ? 'value_ar' : 'value_en', 'key');

        $itemsJson = SiteSetting::query()
            ->where('key', 'contact.items')
            ->value($isAr ? 'value_ar' : 'value_en');

        $items = $itemsJson ? json_decode($itemsJson, true) : [];

        return [
            'title' => $settings['contact.title'] ?? '',
            'subtitle' => $settings['contact.subtitle'] ?? '',
            'addressLabel' => $settings['contact.address_label'] ?? '',
            'address' => $settings['contact.address'] ?? '',
            'mapTitle' => $settings['contact.map_title'] ?? '',
            'mapEmbedUrl' => SiteSetting::query()
                ->where('key', 'contact.map_embed_url')
                ->value($isAr ? 'value_ar' : 'value_en')
                ?? SiteSetting::query()
                    ->where('key', 'contact.map_embed_url')
                    ->value('value_ar')
                ?? '',
            'items' => is_array($items) ? $items : [],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function getAdminPayload(): array
    {
        $settings = SiteSetting::query()
            ->where('group', 'contact')
            ->get()
            ->keyBy('key');

        $itemsSetting = $settings->get('contact.items');

        return [
            'titleAr' => $settings->get('contact.title')?->value_ar ?? '',
            'titleEn' => $settings->get('contact.title')?->value_en ?? '',
            'subtitleAr' => $settings->get('contact.subtitle')?->value_ar ?? '',
            'subtitleEn' => $settings->get('contact.subtitle')?->value_en ?? '',
            'addressLabelAr' => $settings->get('contact.address_label')?->value_ar ?? '',
            'addressLabelEn' => $settings->get('contact.address_label')?->value_en ?? '',
            'addressAr' => $settings->get('contact.address')?->value_ar ?? '',
            'addressEn' => $settings->get('contact.address')?->value_en ?? '',
            'mapTitleAr' => $settings->get('contact.map_title')?->value_ar ?? '',
            'mapTitleEn' => $settings->get('contact.map_title')?->value_en ?? '',
            'mapEmbedUrlAr' => $settings->get('contact.map_embed_url')?->value_ar ?? '',
            'mapEmbedUrlEn' => $settings->get('contact.map_embed_url')?->value_en ?? '',
            'itemsAr' => $this->decodeItems($itemsSetting?->value_ar),
            'itemsEn' => $this->decodeItems($itemsSetting?->value_en),
        ];
    }

    /**
     * @param  array<string, mixed>  $validated
     * @return array<string, mixed>
     */
    public function updateFromAdmin(array $validated): array
    {
        $grouped = [];

        foreach ($validated as $field => $value) {
            if ($field === 'itemsAr' || $field === 'itemsEn') {
                continue;
            }

            $key = self::ADMIN_FIELD_MAP[$field] ?? null;

            if (! $key) {
                continue;
            }

            $column = str_ends_with($field, 'Ar') ? 'value_ar' : 'value_en';
            $grouped[$key][$column] = $value;
        }

        foreach ($grouped as $key => $values) {
            SiteSetting::query()->updateOrCreate(
                ['key' => $key],
                array_merge($values, ['group' => 'contact']),
            );
        }

        if (array_key_exists('itemsAr', $validated) || array_key_exists('itemsEn', $validated)) {
            $itemsPayload = [];

            if (array_key_exists('itemsAr', $validated)) {
                $itemsPayload['value_ar'] = json_encode(
                    $validated['itemsAr'] ?? [],
                    JSON_UNESCAPED_UNICODE,
                );
            }

            if (array_key_exists('itemsEn', $validated)) {
                $itemsPayload['value_en'] = json_encode(
                    $validated['itemsEn'] ?? [],
                    JSON_UNESCAPED_UNICODE,
                );
            }

            SiteSetting::query()->updateOrCreate(
                ['key' => 'contact.items'],
                array_merge($itemsPayload, ['group' => 'contact']),
            );
        }

        return $this->getAdminPayload();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function decodeItems(?string $json): array
    {
        if (! $json) {
            return [];
        }

        $decoded = json_decode($json, true);

        return is_array($decoded) ? $decoded : [];
    }
}
