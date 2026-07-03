<?php

use App\Http\Controllers\Api\Admin\AboutContentController;
use App\Http\Controllers\Api\Admin\AdvisoryBoardMemberController;
use App\Http\Controllers\Api\Admin\AuthController;
use App\Http\Controllers\Api\Admin\ContactInfoController;
use App\Http\Controllers\Api\Admin\ContactSubmissionController;
use App\Http\Controllers\Api\Admin\HeroSlideController;
use App\Http\Controllers\Api\Admin\HomeStatController;
use App\Http\Controllers\Api\Admin\LeadershipController;
use App\Http\Controllers\Api\Admin\MediaArticleController;
use App\Http\Controllers\Api\Admin\MemberCityController;
use App\Http\Controllers\Api\Admin\MemberCityStatController;
use App\Http\Controllers\Api\Admin\MemberCountryController;
use App\Http\Controllers\Api\Admin\NewsletterSubscriptionController;
use App\Http\Controllers\Api\Admin\MembershipApplicationController;
use App\Http\Controllers\Api\Admin\PortalContributionController as AdminPortalContributionController;
use App\Http\Controllers\Api\Admin\ProgramController as AdminProgramController;
use App\Http\Controllers\Api\Admin\ProgramSectionController;
use App\Http\Controllers\Api\Admin\ProgramSectionDetailController;
use App\Http\Controllers\Api\Admin\TrainingCourseController;
use App\Http\Controllers\Api\Admin\ExpertController;
use App\Http\Controllers\Api\Admin\FocusAreaController;
use App\Http\Controllers\Api\Admin\DirectoryCityController;
use App\Http\Controllers\Api\Admin\DirectoryProjectController;
use App\Http\Controllers\Api\Admin\DirectoryOrganizationController;
use App\Http\Controllers\Api\Admin\DirectoryPublicationController;
use App\Http\Controllers\Api\Admin\KnowledgeCategoryController;
use App\Http\Controllers\Api\Admin\PartnerCategoryController;
use App\Http\Controllers\Api\Admin\PartnerController;
use App\Http\Controllers\Api\Admin\ResourceController;
use App\Http\Controllers\Api\Admin\StrategyDiagramItemController;
use App\Http\Controllers\Api\Admin\StrategyPageController;
use App\Http\Controllers\Api\Admin\StrategyPillarController;
use App\Http\Controllers\Api\Admin\TeamMemberController;
use App\Http\Controllers\Api\Admin\TeamSectionController;
use App\Http\Controllers\Api\Admin\FaqController as AdminFaqController;
use App\Http\Controllers\Api\Admin\JobApplicationController;
use App\Http\Controllers\Api\Admin\JobOpeningController;
use App\Http\Controllers\Api\Admin\LegalPageController;
use App\Http\Controllers\Api\Admin\SiteSettingController;
use App\Http\Controllers\Api\Admin\SocialLinkController;
use App\Http\Controllers\Api\Admin\UploadController;
use App\Http\Controllers\Api\V1\NewsletterController;
use App\Http\Controllers\Api\V1\SettingsController;
use App\Http\Controllers\Api\V1\AboutController;
use App\Http\Controllers\Api\V1\CareersController;
use App\Http\Controllers\Api\V1\ContactController;
use App\Http\Controllers\Api\V1\FaqController;
use App\Http\Controllers\Api\V1\HomeController;
use App\Http\Controllers\Api\V1\LegalController;
use App\Http\Controllers\Api\V1\MediaController;
use App\Http\Controllers\Api\V1\MemberCitiesController;
use App\Http\Controllers\Api\V1\MembershipController;
use App\Http\Controllers\Api\V1\PortalContributionController;
use App\Http\Controllers\Api\V1\ProgramController;
use App\Http\Controllers\Api\V1\ResourcesController;
use App\Http\Controllers\Api\V1\StrategyController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| AUDI API Routes
|--------------------------------------------------------------------------
| Public: /api/v1/*  |  Admin: /api/admin/* (Sanctum)
| Full spec: docs/backend/laravel-backend.md
*/

Route::prefix('v1')->middleware('locale')->group(function () {
    Route::get('/home', [HomeController::class, 'show']);
    Route::prefix('home/member-cities')->group(function () {
        Route::get('/', [MemberCitiesController::class, 'show']);
        Route::get('/countries.geojson', [MemberCitiesController::class, 'countriesGeoJson']);
        Route::get('/cities.geojson', [MemberCitiesController::class, 'citiesGeoJson']);
    });

    Route::get('/settings', [SettingsController::class, 'show']);

    Route::prefix('about')->group(function () {
        Route::get('/institute', [AboutController::class, 'institute']);
        Route::get('/vision-mission', [AboutController::class, 'visionMission']);
        Route::get('/leadership/{type}', [AboutController::class, 'leadership']);
        Route::get('/advisory-board', [AboutController::class, 'advisoryBoard']);
        Route::get('/team', [AboutController::class, 'team']);
        Route::get('/structure', [AboutController::class, 'structure']);
        Route::get('/partners', [AboutController::class, 'partners']);
    });

    Route::prefix('strategy')->group(function () {
        Route::get('/strategy-2025', [StrategyController::class, 'strategy2025']);
        Route::get('/focus-areas', [StrategyController::class, 'focusAreas']);
        Route::get('/focus-areas/{slug}', [StrategyController::class, 'focusArea']);
    });

    Route::prefix('programs')->group(function () {
        Route::get('/urban-policies/directory', [ProgramController::class, 'directory']);
        Route::post('/urban-policies/contribute', [PortalContributionController::class, 'store'])
            ->middleware('throttle:10,1');
        Route::get('/{slug}', [ProgramController::class, 'show']);
    });

    Route::get('/resources', [ResourcesController::class, 'index']);
    Route::get('/media/{category}', [MediaController::class, 'index']);
    Route::get('/media/{category}/{slug}', [MediaController::class, 'show']);

    Route::get('/contact', [ContactController::class, 'show']);
    Route::post('/contact', [ContactController::class, 'store'])->middleware('throttle:10,1');
    Route::post('/membership', [MembershipController::class, 'store'])->middleware('throttle:10,1');
    Route::post('/newsletter/subscribe', [NewsletterController::class, 'subscribe'])->middleware('throttle:10,1');

    // Careers — اعمل معنا
    Route::get('/careers', [CareersController::class, 'index']);
    Route::post('/careers/apply', [CareersController::class, 'apply'])->middleware('throttle:10,1');
    Route::get('/careers/{jobOpening}', [CareersController::class, 'show']);

    // FAQ — الأسئلة الشائعة
    Route::get('/faqs', [FaqController::class, 'index']);

    // Legal pages — الشروط والأحكام / سياسة الخصوصية
    Route::get('/legal/{slug}', [LegalController::class, 'show']);
});

Route::prefix('admin')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::post('/login', [AuthController::class, 'login']);
        Route::middleware('auth:sanctum')->group(function () {
            Route::post('/logout', [AuthController::class, 'logout']);
            Route::get('/me', [AuthController::class, 'me']);
        });
    });

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('uploads', [UploadController::class, 'index']);
        Route::post('uploads', [UploadController::class, 'store']);
        Route::get('uploads/{upload}', [UploadController::class, 'show']);
        Route::delete('uploads/{upload}', [UploadController::class, 'destroy']);

        Route::get('settings', [SiteSettingController::class, 'index']);
        Route::post('settings', [SiteSettingController::class, 'store']);
        Route::get('settings/{siteSetting}', [SiteSettingController::class, 'show']);
        Route::put('settings/{siteSetting}', [SiteSettingController::class, 'update']);
        Route::delete('settings/{siteSetting}', [SiteSettingController::class, 'destroy']);

        Route::get('social-links', [SocialLinkController::class, 'index']);
        Route::post('social-links', [SocialLinkController::class, 'store']);
        Route::post('social-links/reorder', [SocialLinkController::class, 'reorder']);
        Route::get('social-links/{socialLink}', [SocialLinkController::class, 'show']);
        Route::put('social-links/{socialLink}', [SocialLinkController::class, 'update']);
        Route::delete('social-links/{socialLink}', [SocialLinkController::class, 'destroy']);

        Route::get('hero-slides', [HeroSlideController::class, 'index']);
        Route::post('hero-slides', [HeroSlideController::class, 'store']);
        Route::post('hero-slides/reorder', [HeroSlideController::class, 'reorder']);
        Route::get('hero-slides/{heroSlide}', [HeroSlideController::class, 'show']);
        Route::put('hero-slides/{heroSlide}', [HeroSlideController::class, 'update']);
        Route::delete('hero-slides/{heroSlide}', [HeroSlideController::class, 'destroy']);

        Route::get('home-stats', [HomeStatController::class, 'index']);
        Route::post('home-stats', [HomeStatController::class, 'store']);
        Route::post('home-stats/reorder', [HomeStatController::class, 'reorder']);
        Route::get('home-stats/{homeStat}', [HomeStatController::class, 'show']);
        Route::put('home-stats/{homeStat}', [HomeStatController::class, 'update']);
        Route::delete('home-stats/{homeStat}', [HomeStatController::class, 'destroy']);

        Route::get('about-content', [AboutContentController::class, 'index']);
        Route::post('about-content', [AboutContentController::class, 'store']);
        Route::get('about-content/{aboutContent}', [AboutContentController::class, 'show']);
        Route::put('about-content/{aboutContent}', [AboutContentController::class, 'update']);
        Route::delete('about-content/{aboutContent}', [AboutContentController::class, 'destroy']);

        Route::get('leadership', [LeadershipController::class, 'index']);
        Route::post('leadership', [LeadershipController::class, 'store']);
        Route::get('leadership/{leadershipMessage}', [LeadershipController::class, 'show']);
        Route::put('leadership/{leadershipMessage}', [LeadershipController::class, 'update']);
        Route::delete('leadership/{leadershipMessage}', [LeadershipController::class, 'destroy']);

        Route::get('advisory-board', [AdvisoryBoardMemberController::class, 'index']);
        Route::post('advisory-board', [AdvisoryBoardMemberController::class, 'store']);
        Route::post('advisory-board/reorder', [AdvisoryBoardMemberController::class, 'reorder']);
        Route::get('advisory-board/{advisoryBoardMember}', [AdvisoryBoardMemberController::class, 'show']);
        Route::put('advisory-board/{advisoryBoardMember}', [AdvisoryBoardMemberController::class, 'update']);
        Route::delete('advisory-board/{advisoryBoardMember}', [AdvisoryBoardMemberController::class, 'destroy']);

        Route::get('team-sections', [TeamSectionController::class, 'index']);
        Route::post('team-sections', [TeamSectionController::class, 'store']);
        Route::post('team-sections/reorder', [TeamSectionController::class, 'reorder']);
        Route::get('team-sections/{teamSection}', [TeamSectionController::class, 'show']);
        Route::put('team-sections/{teamSection}', [TeamSectionController::class, 'update']);
        Route::delete('team-sections/{teamSection}', [TeamSectionController::class, 'destroy']);

        Route::get('team-members', [TeamMemberController::class, 'index']);
        Route::post('team-members', [TeamMemberController::class, 'store']);
        Route::post('team-members/reorder', [TeamMemberController::class, 'reorder']);
        Route::get('team-members/{teamMember}', [TeamMemberController::class, 'show']);
        Route::put('team-members/{teamMember}', [TeamMemberController::class, 'update']);
        Route::delete('team-members/{teamMember}', [TeamMemberController::class, 'destroy']);

        Route::get('partner-categories', [PartnerCategoryController::class, 'index']);
        Route::post('partner-categories', [PartnerCategoryController::class, 'store']);
        Route::post('partner-categories/reorder', [PartnerCategoryController::class, 'reorder']);
        Route::get('partner-categories/{partnerCategory}', [PartnerCategoryController::class, 'show']);
        Route::put('partner-categories/{partnerCategory}', [PartnerCategoryController::class, 'update']);
        Route::delete('partner-categories/{partnerCategory}', [PartnerCategoryController::class, 'destroy']);

        Route::get('partners', [PartnerController::class, 'index']);
        Route::post('partners', [PartnerController::class, 'store']);
        Route::post('partners/reorder', [PartnerController::class, 'reorder']);
        Route::get('partners/{partner}', [PartnerController::class, 'show']);
        Route::put('partners/{partner}', [PartnerController::class, 'update']);
        Route::delete('partners/{partner}', [PartnerController::class, 'destroy']);

        Route::get('knowledge-categories', [KnowledgeCategoryController::class, 'index']);
        Route::post('knowledge-categories', [KnowledgeCategoryController::class, 'store']);
        Route::post('knowledge-categories/reorder', [KnowledgeCategoryController::class, 'reorder']);
        Route::get('knowledge-categories/{knowledgeCategory}', [KnowledgeCategoryController::class, 'show']);
        Route::put('knowledge-categories/{knowledgeCategory}', [KnowledgeCategoryController::class, 'update']);
        Route::delete('knowledge-categories/{knowledgeCategory}', [KnowledgeCategoryController::class, 'destroy']);

        Route::get('strategy', [StrategyPageController::class, 'showDefault']);
        Route::put('strategy', [StrategyPageController::class, 'updateDefault']);

        Route::get('strategy-pillars', [StrategyPillarController::class, 'index']);
        Route::post('strategy-pillars', [StrategyPillarController::class, 'store']);
        Route::post('strategy-pillars/reorder', [StrategyPillarController::class, 'reorder']);
        Route::get('strategy-pillars/{strategyPillar}', [StrategyPillarController::class, 'show']);
        Route::put('strategy-pillars/{strategyPillar}', [StrategyPillarController::class, 'update']);
        Route::delete('strategy-pillars/{strategyPillar}', [StrategyPillarController::class, 'destroy']);

        Route::get('strategy-diagram', [StrategyDiagramItemController::class, 'index']);
        Route::post('strategy-diagram', [StrategyDiagramItemController::class, 'store']);
        Route::post('strategy-diagram/reorder', [StrategyDiagramItemController::class, 'reorder']);
        Route::get('strategy-diagram/{strategyDiagramItem}', [StrategyDiagramItemController::class, 'show']);
        Route::put('strategy-diagram/{strategyDiagramItem}', [StrategyDiagramItemController::class, 'update']);
        Route::delete('strategy-diagram/{strategyDiagramItem}', [StrategyDiagramItemController::class, 'destroy']);

        Route::get('focus-areas', [FocusAreaController::class, 'index']);
        Route::post('focus-areas', [FocusAreaController::class, 'store']);
        Route::post('focus-areas/reorder', [FocusAreaController::class, 'reorder']);
        Route::get('focus-areas/{focusArea}', [FocusAreaController::class, 'show']);
        Route::put('focus-areas/{focusArea}', [FocusAreaController::class, 'update']);
        Route::delete('focus-areas/{focusArea}', [FocusAreaController::class, 'destroy']);

        Route::get('programs', [AdminProgramController::class, 'index']);
        Route::post('programs', [AdminProgramController::class, 'store']);
        Route::get('programs/{program}', [AdminProgramController::class, 'show']);
        Route::put('programs/{program}', [AdminProgramController::class, 'update']);
        Route::delete('programs/{program}', [AdminProgramController::class, 'destroy']);

        Route::get('program-sections', [ProgramSectionController::class, 'index']);
        Route::post('program-sections', [ProgramSectionController::class, 'store']);
        Route::post('program-sections/reorder', [ProgramSectionController::class, 'reorder']);
        Route::get('program-sections/{programSection}', [ProgramSectionController::class, 'show']);
        Route::put('program-sections/{programSection}', [ProgramSectionController::class, 'update']);
        Route::delete('program-sections/{programSection}', [ProgramSectionController::class, 'destroy']);

        Route::get('program-section-details', [ProgramSectionDetailController::class, 'index']);
        Route::post('program-section-details', [ProgramSectionDetailController::class, 'store']);
        Route::get('program-section-details/{programSectionDetail}', [ProgramSectionDetailController::class, 'show']);
        Route::put('program-section-details/{programSectionDetail}', [ProgramSectionDetailController::class, 'update']);
        Route::delete('program-section-details/{programSectionDetail}', [ProgramSectionDetailController::class, 'destroy']);

        Route::get('training-courses', [TrainingCourseController::class, 'index']);
        Route::post('training-courses', [TrainingCourseController::class, 'store']);
        Route::post('training-courses/reorder', [TrainingCourseController::class, 'reorder']);
        Route::get('training-courses/{trainingCourse}', [TrainingCourseController::class, 'show']);
        Route::put('training-courses/{trainingCourse}', [TrainingCourseController::class, 'update']);
        Route::delete('training-courses/{trainingCourse}', [TrainingCourseController::class, 'destroy']);

        Route::get('experts', [ExpertController::class, 'index']);
        Route::post('experts', [ExpertController::class, 'store']);
        Route::post('experts/reorder', [ExpertController::class, 'reorder']);
        Route::get('experts/{expert}', [ExpertController::class, 'show']);
        Route::put('experts/{expert}', [ExpertController::class, 'update']);
        Route::delete('experts/{expert}', [ExpertController::class, 'destroy']);

        Route::get('directory/cities', [DirectoryCityController::class, 'index']);
        Route::post('directory/cities', [DirectoryCityController::class, 'store']);
        Route::post('directory/cities/reorder', [DirectoryCityController::class, 'reorder']);
        Route::get('directory/cities/{directoryCity}', [DirectoryCityController::class, 'show']);
        Route::put('directory/cities/{directoryCity}', [DirectoryCityController::class, 'update']);
        Route::delete('directory/cities/{directoryCity}', [DirectoryCityController::class, 'destroy']);

        Route::get('directory/projects', [DirectoryProjectController::class, 'index']);
        Route::post('directory/projects', [DirectoryProjectController::class, 'store']);
        Route::post('directory/projects/reorder', [DirectoryProjectController::class, 'reorder']);
        Route::get('directory/projects/{directoryProject}', [DirectoryProjectController::class, 'show']);
        Route::put('directory/projects/{directoryProject}', [DirectoryProjectController::class, 'update']);
        Route::delete('directory/projects/{directoryProject}', [DirectoryProjectController::class, 'destroy']);

        Route::get('directory/organizations', [DirectoryOrganizationController::class, 'index']);
        Route::post('directory/organizations', [DirectoryOrganizationController::class, 'store']);
        Route::post('directory/organizations/reorder', [DirectoryOrganizationController::class, 'reorder']);
        Route::get('directory/organizations/{directoryOrganization}', [DirectoryOrganizationController::class, 'show']);
        Route::put('directory/organizations/{directoryOrganization}', [DirectoryOrganizationController::class, 'update']);
        Route::delete('directory/organizations/{directoryOrganization}', [DirectoryOrganizationController::class, 'destroy']);

        Route::get('directory/publications', [DirectoryPublicationController::class, 'index']);
        Route::post('directory/publications', [DirectoryPublicationController::class, 'store']);
        Route::post('directory/publications/reorder', [DirectoryPublicationController::class, 'reorder']);
        Route::get('directory/publications/{directoryPublication}', [DirectoryPublicationController::class, 'show']);
        Route::put('directory/publications/{directoryPublication}', [DirectoryPublicationController::class, 'update']);
        Route::delete('directory/publications/{directoryPublication}', [DirectoryPublicationController::class, 'destroy']);

        Route::get('portal-contributions', [AdminPortalContributionController::class, 'index']);
        Route::get('portal-contributions/{portalContribution}', [AdminPortalContributionController::class, 'show']);
        Route::patch('portal-contributions/{portalContribution}', [AdminPortalContributionController::class, 'update']);
        Route::delete('portal-contributions/{portalContribution}', [AdminPortalContributionController::class, 'destroy']);

        Route::prefix('member-cities')->group(function () {
            Route::get('stats', [MemberCityStatController::class, 'index']);
            Route::put('stats', [MemberCityStatController::class, 'update']);
            Route::get('countries', [MemberCountryController::class, 'index']);
            Route::post('cities/import', [MemberCityController::class, 'import']);
            Route::post('cities/import-from-file', [MemberCityController::class, 'importFromFile']);
            Route::get('cities', [MemberCityController::class, 'index']);
            Route::post('cities', [MemberCityController::class, 'store']);
            Route::get('cities/{memberCity}', [MemberCityController::class, 'show']);
            Route::patch('cities/{memberCity}', [MemberCityController::class, 'update']);
            Route::delete('cities/{memberCity}', [MemberCityController::class, 'destroy']);
        });

        Route::get('resources', [ResourceController::class, 'index']);
        Route::post('resources', [ResourceController::class, 'store']);
        Route::post('resources/reorder', [ResourceController::class, 'reorder']);
        Route::get('resources/{resource}', [ResourceController::class, 'show']);
        Route::put('resources/{resource}', [ResourceController::class, 'update']);
        Route::delete('resources/{resource}', [ResourceController::class, 'destroy']);

        Route::get('media', [MediaArticleController::class, 'index']);
        Route::post('media', [MediaArticleController::class, 'store']);
        Route::post('media/reorder', [MediaArticleController::class, 'reorder']);
        Route::get('media/{mediaArticle}', [MediaArticleController::class, 'show']);
        Route::put('media/{mediaArticle}', [MediaArticleController::class, 'update']);
        Route::delete('media/{mediaArticle}', [MediaArticleController::class, 'destroy']);

        Route::get('contact-info', [ContactInfoController::class, 'show']);
        Route::put('contact-info', [ContactInfoController::class, 'update']);

        Route::get('contact-submissions', [ContactSubmissionController::class, 'index']);
        Route::get('contact-submissions/{contactSubmission}', [ContactSubmissionController::class, 'show']);
        Route::patch('contact-submissions/{contactSubmission}', [ContactSubmissionController::class, 'update']);
        Route::delete('contact-submissions/{contactSubmission}', [ContactSubmissionController::class, 'destroy']);

        Route::get('membership-applications', [MembershipApplicationController::class, 'index']);
        Route::get('membership-applications/{membershipApplication}', [MembershipApplicationController::class, 'show']);
        Route::patch('membership-applications/{membershipApplication}', [MembershipApplicationController::class, 'update']);
        Route::delete('membership-applications/{membershipApplication}', [MembershipApplicationController::class, 'destroy']);

        Route::get('faqs', [AdminFaqController::class, 'index']);
        Route::post('faqs', [AdminFaqController::class, 'store']);
        Route::post('faqs/reorder', [AdminFaqController::class, 'reorder']);
        Route::get('faqs/{faq}', [AdminFaqController::class, 'show']);
        Route::put('faqs/{faq}', [AdminFaqController::class, 'update']);
        Route::delete('faqs/{faq}', [AdminFaqController::class, 'destroy']);

        Route::get('job-openings', [JobOpeningController::class, 'index']);
        Route::post('job-openings', [JobOpeningController::class, 'store']);
        Route::post('job-openings/reorder', [JobOpeningController::class, 'reorder']);
        Route::get('job-openings/{jobOpening}', [JobOpeningController::class, 'show']);
        Route::put('job-openings/{jobOpening}', [JobOpeningController::class, 'update']);
        Route::delete('job-openings/{jobOpening}', [JobOpeningController::class, 'destroy']);

        Route::get('job-applications', [JobApplicationController::class, 'index']);
        Route::get('job-applications/{jobApplication}', [JobApplicationController::class, 'show']);
        Route::patch('job-applications/{jobApplication}', [JobApplicationController::class, 'update']);
        Route::delete('job-applications/{jobApplication}', [JobApplicationController::class, 'destroy']);

        Route::get('legal', [LegalPageController::class, 'index']);
        Route::post('legal', [LegalPageController::class, 'store']);
        Route::get('legal/{legalPage}', [LegalPageController::class, 'show']);
        Route::put('legal/{legalPage}', [LegalPageController::class, 'update']);
        Route::delete('legal/{legalPage}', [LegalPageController::class, 'destroy']);

        Route::get('newsletter-subscriptions', [NewsletterSubscriptionController::class, 'index']);
        Route::get('newsletter-subscriptions/{newsletterSubscription}', [NewsletterSubscriptionController::class, 'show']);
        Route::delete('newsletter-subscriptions/{newsletterSubscription}', [NewsletterSubscriptionController::class, 'destroy']);
    });
});
