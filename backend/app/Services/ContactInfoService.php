<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\SiteSetting;

class ContactInfoService
{
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
                ->value('value_ar') ?? '',
            'items' => is_array($items) ? $items : [],
        ];
    }
}
