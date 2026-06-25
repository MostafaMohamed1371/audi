<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\SiteSetting;
use App\Models\SocialLink;

class SiteSettingsService
{
    public function __construct(private readonly ContactInfoService $contactInfo) {}

    /**
     * @return array<string, mixed>
     */
    public function getPublicPayload(?string $locale = null): array
    {
        $locale = $locale ?? app()->getLocale();
        $isAr = $locale === 'ar';

        $general = SiteSetting::query()
            ->where('group', 'general')
            ->pluck($isAr ? 'value_ar' : 'value_en', 'key');

        $socialLinks = SocialLink::query()
            ->where('is_active', true)
            ->ordered()
            ->get()
            ->map(fn (SocialLink $link) => [
                'platform' => $link->platform,
                'url' => $link->url,
                'icon' => $link->icon,
            ])
            ->values()
            ->all();

        return [
            'siteName' => $general['site.name'] ?? ($isAr ? 'المعهد العربي لإنماء المدن' : 'Arab Urban Development Institute'),
            'copyright' => $general['site.copyright'] ?? null,
            'socialLinks' => $socialLinks,
            'contact' => $this->contactInfo->getPublicPayload($locale),
        ];
    }
}
