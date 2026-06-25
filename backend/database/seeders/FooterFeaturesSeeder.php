<?php

namespace Database\Seeders;

use App\Enums\MediaCategory;
use App\Models\Faq;
use App\Models\JobOpening;
use App\Models\LegalPage;
use App\Models\MediaArticle;
use Illuminate\Database\Seeder;

class FooterFeaturesSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedFaqs();
        $this->seedLegalPages();
        $this->seedJobOpenings();
        $this->seedSecretarySpeaks();
    }

    private function seedFaqs(): void
    {
        $faqs = [
            [
                'category' => 'membership',
                'question_ar' => 'كيف يمكنني الانضمام إلى عضوية المعهد العربي لإنماء المدن؟',
                'question_en' => 'How can I join the Arab Urban Development Institute membership?',
                'answer_ar' => 'يمكنك التقديم على العضوية من خلال تعبئة نموذج الانضمام المتاح في صفحة "انضم إلينا"، وسيقوم فريقنا بمراجعة طلبك والتواصل معك.',
                'answer_en' => 'You can apply by filling out the membership form on the "Join Us" page. Our team will review your application and contact you.',
            ],
            [
                'category' => 'membership',
                'question_ar' => 'ما هي فئات العضوية المتاحة؟',
                'question_en' => 'What membership categories are available?',
                'answer_ar' => 'يوفر المعهد عضويات للمدن والجهات الحكومية والمؤسسات الأكاديمية والخبراء الأفراد.',
                'answer_en' => 'The institute offers memberships for cities, government entities, academic institutions, and individual experts.',
            ],
            [
                'category' => 'programs',
                'question_ar' => 'هل برامج المعهد مجانية؟',
                'question_en' => 'Are the institute programs free?',
                'answer_ar' => 'تتنوع برامج المعهد بين المجانية والمدفوعة حسب نوع البرنامج والفئة المستهدفة.',
                'answer_en' => 'Programs vary between free and paid depending on the program type and target audience.',
            ],
            [
                'category' => 'general',
                'question_ar' => 'كيف يمكنني التواصل مع المعهد؟',
                'question_en' => 'How can I contact the institute?',
                'answer_ar' => 'يمكنك التواصل معنا عبر صفحة "اتصل بنا" أو من خلال البريد الإلكتروني ووسائل التواصل الاجتماعي.',
                'answer_en' => 'You can reach us through the "Contact Us" page, email, or our social media channels.',
            ],
        ];

        foreach ($faqs as $index => $faq) {
            Faq::query()->updateOrCreate(
                ['question_en' => $faq['question_en']],
                array_merge($faq, ['is_published' => true, 'sort_order' => $index]),
            );
        }
    }

    private function seedLegalPages(): void
    {
        $pages = [
            [
                'slug' => 'terms',
                'title_ar' => 'الشروط والأحكام',
                'title_en' => 'Terms and Conditions',
                'content_ar' => "مرحباً بك في موقع المعهد العربي لإنماء المدن. باستخدامك لهذا الموقع فإنك توافق على الالتزام بالشروط والأحكام التالية.\n\nتُعد جميع المحتويات المنشورة على الموقع ملكية فكرية للمعهد، ولا يجوز إعادة نشرها دون إذن خطّي مسبق.",
                'content_en' => "Welcome to the Arab Urban Development Institute website. By using this site, you agree to comply with the following terms and conditions.\n\nAll content published on the site is the intellectual property of the institute and may not be republished without prior written permission.",
            ],
            [
                'slug' => 'privacy',
                'title_ar' => 'سياسة الخصوصية',
                'title_en' => 'Privacy Policy',
                'content_ar' => "يلتزم المعهد العربي لإنماء المدن بحماية خصوصية زواره ومستخدمي خدماته.\n\nنقوم بجمع البيانات الضرورية فقط لتقديم خدماتنا، ولا نشارك بياناتك مع أطراف ثالثة دون موافقتك.",
                'content_en' => "The Arab Urban Development Institute is committed to protecting the privacy of its visitors and service users.\n\nWe collect only the data necessary to provide our services and do not share your data with third parties without your consent.",
            ],
        ];

        foreach ($pages as $page) {
            LegalPage::query()->updateOrCreate(['slug' => $page['slug']], $page);
        }
    }

    private function seedJobOpenings(): void
    {
        $openings = [
            [
                'title_ar' => 'باحث في السياسات الحضرية',
                'title_en' => 'Urban Policy Researcher',
                'location_ar' => 'الرياض، المملكة العربية السعودية',
                'location_en' => 'Riyadh, Saudi Arabia',
                'employment_type' => 'full_time',
                'summary_ar' => 'نبحث عن باحث متخصص في السياسات الحضرية للانضمام إلى فريق برنامج السياسات الحضرية.',
                'summary_en' => 'We are looking for an urban policy researcher to join the Urban Policy Program team.',
                'description_ar' => [
                    'إجراء البحوث والدراسات في مجال التنمية الحضرية.',
                    'إعداد التقارير وأوراق السياسات.',
                    'التعاون مع الشركاء والجهات ذات العلاقة.',
                ],
                'description_en' => [
                    'Conduct research and studies in urban development.',
                    'Prepare reports and policy papers.',
                    'Collaborate with partners and stakeholders.',
                ],
            ],
            [
                'title_ar' => 'منسق برامج تدريبية',
                'title_en' => 'Training Program Coordinator',
                'location_ar' => 'عن بُعد',
                'location_en' => 'Remote',
                'employment_type' => 'contract',
                'summary_ar' => 'منسق لإدارة وتنظيم البرامج التدريبية الخاصة بمركز دعم المدن.',
                'summary_en' => 'A coordinator to manage and organize the City Support Center training programs.',
                'description_ar' => [
                    'تخطيط وتنظيم الدورات التدريبية.',
                    'التواصل مع المدربين والمشاركين.',
                ],
                'description_en' => [
                    'Plan and organize training courses.',
                    'Communicate with trainers and participants.',
                ],
            ],
        ];

        foreach ($openings as $index => $opening) {
            JobOpening::query()->updateOrCreate(
                ['title_en' => $opening['title_en']],
                array_merge($opening, ['is_published' => true, 'sort_order' => $index]),
            );
        }
    }

    private function seedSecretarySpeaks(): void
    {
        $items = [
            [
                'key' => 'secretary-speaks-1',
                'slug_ar' => 'كلمة-الأمين-العام-حول-مستقبل-المدن-العربية',
                'slug_en' => 'secretary-general-on-the-future-of-arab-cities',
                'title_ar' => 'الأمين العام يتحدث عن مستقبل المدن العربية',
                'title_en' => 'The Secretary-General on the Future of Arab Cities',
                'description_ar' => 'رؤية الأمين العام حول التحديات والفرص أمام المدن العربية.',
                'description_en' => 'The Secretary-General’s vision on the challenges and opportunities for Arab cities.',
                'body_ar' => [
                    'تشهد المدن العربية تحولات متسارعة تتطلب رؤية تنموية متكاملة.',
                    'يؤكد المعهد على أهمية التخطيط الحضري المستدام.',
                ],
                'body_en' => [
                    'Arab cities are undergoing rapid transformations that require an integrated development vision.',
                    'The institute emphasizes the importance of sustainable urban planning.',
                ],
            ],
        ];

        foreach ($items as $index => $item) {
            MediaArticle::query()->updateOrCreate(
                ['key' => $item['key']],
                array_merge($item, [
                    'category' => MediaCategory::SecretarySpeaks->value,
                    'published_date' => now()->subDays($index + 1)->toDateString(),
                    'is_published' => true,
                    'sort_order' => $index,
                ]),
            );
        }
    }
}
