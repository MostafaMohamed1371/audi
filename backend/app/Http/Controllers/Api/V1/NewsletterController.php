<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreNewsletterSubscriptionRequest;
use App\Models\NewsletterSubscription;
use Illuminate\Http\JsonResponse;

class NewsletterController extends Controller
{
    public function subscribe(StoreNewsletterSubscriptionRequest $request): JsonResponse
    {
        $locale = $request->validated('locale') ?? app()->getLocale();

        $subscription = NewsletterSubscription::query()->updateOrCreate(
            ['email' => $request->validated('email')],
            [
                'locale' => $locale,
                'is_confirmed' => true,
            ],
        );

        $wasRecent = $subscription->wasRecentlyCreated;

        $message = app()->getLocale() === 'ar'
            ? ($wasRecent
                ? 'تم الاشتراك في النشرة البريدية بنجاح.'
                : 'أنت مشترك بالفعل في النشرة البريدية.')
            : ($wasRecent
                ? 'You have successfully subscribed to the newsletter.'
                : 'You are already subscribed to the newsletter.');

        return response()->json([
            'message' => $message,
            'id' => $subscription->id,
            'isNew' => $wasRecent,
        ], $wasRecent ? 201 : 200);
    }
}
