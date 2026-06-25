<?php

namespace Database\Seeders;

use App\Models\SiteSetting;
use Illuminate\Database\Seeder;

class ContactInfoSeeder extends Seeder
{
    public function run(): void
    {
        $mapUrl = 'https://maps.google.com/maps?q=Arab+Urban+Development+Institute,+Riyadh,+Saudi+Arabia&z=15&output=embed';

        $scalarSettings = [
            [
                'key' => 'contact.title',
                'value_ar' => 'تواصل معنا',
                'value_en' => 'Contact Us',
            ],
            [
                'key' => 'contact.subtitle',
                'value_ar' => 'نسعد بالتواصل معكم والإجابة على استفساراتكم حول برامج وأنشطة المعهد.',
                'value_en' => 'We are happy to hear from you and answer your questions about AUDI programs and activities.',
            ],
            [
                'key' => 'contact.address_label',
                'value_ar' => 'الموقع على الخارطة:',
                'value_en' => 'Location on map:',
            ],
            [
                'key' => 'contact.address',
                'value_ar' => 'شارع عبدالله بن حذافة السهمي، الحي الدبلوماسي 12521-3803 الرياض، المملكة العربية السعودية',
                'value_en' => 'Abdullah bin Hudhafah Al-Sahmi St., Diplomatic Quarter, Riyadh 12521-3803, Saudi Arabia',
            ],
            [
                'key' => 'contact.map_title',
                'value_ar' => 'موقع المعهد العربي لإنماء المدن',
                'value_en' => 'Arab Urban Development Institute location',
            ],
            [
                'key' => 'contact.map_embed_url',
                'value_ar' => $mapUrl,
                'value_en' => $mapUrl,
            ],
        ];

        foreach ($scalarSettings as $row) {
            SiteSetting::query()->updateOrCreate(
                ['key' => $row['key']],
                [
                    'value_ar' => $row['value_ar'],
                    'value_en' => $row['value_en'],
                    'group' => 'contact',
                ],
            );
        }

        SiteSetting::query()->updateOrCreate(
            ['key' => 'contact.items'],
            [
                'value_ar' => json_encode([
                    ['label' => 'البريد الإلكتروني:', 'value' => 'info@araburban.org', 'type' => 'email', 'href' => 'mailto:info@araburban.org'],
                    ['label' => 'رقم التواصل:', 'value' => '+966 114802555', 'type' => 'phone', 'href' => 'tel:+966114802555'],
                    ['label' => 'رقم الفاكس:', 'value' => '+966 114802666', 'type' => 'fax'],
                ], JSON_UNESCAPED_UNICODE),
                'value_en' => json_encode([
                    ['label' => 'Email:', 'value' => 'info@araburban.org', 'type' => 'email', 'href' => 'mailto:info@araburban.org'],
                    ['label' => 'Phone:', 'value' => '+966 114802555', 'type' => 'phone', 'href' => 'tel:+966114802555'],
                    ['label' => 'Fax:', 'value' => '+966 114802666', 'type' => 'fax'],
                ], JSON_UNESCAPED_UNICODE),
                'group' => 'contact',
            ],
        );
    }
}
