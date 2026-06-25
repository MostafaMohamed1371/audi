<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\NewsletterSubscription;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NewsletterSubscriptionController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $limit = min(max((int) $request->query('limit', 20), 1), 100);

        $query = NewsletterSubscription::query()->latest();

        if ($locale = $request->query('locale')) {
            $query->where('locale', $locale);
        }

        if ($search = $request->query('search')) {
            $query->where('email', 'like', "%{$search}%");
        }

        $paginator = $query->paginate($limit);

        return response()->json([
            'data' => $paginator->getCollection()->map(fn (NewsletterSubscription $sub) => $this->transform($sub))->values(),
            'meta' => [
                'page' => $paginator->currentPage(),
                'limit' => $paginator->perPage(),
                'total' => $paginator->total(),
                'totalPages' => $paginator->lastPage(),
            ],
        ]);
    }

    public function show(NewsletterSubscription $newsletterSubscription): JsonResponse
    {
        return response()->json(['data' => $this->transform($newsletterSubscription)]);
    }

    public function destroy(NewsletterSubscription $newsletterSubscription): JsonResponse
    {
        $newsletterSubscription->delete();

        return response()->json(['message' => 'Deleted']);
    }

    /**
     * @return array<string, mixed>
     */
    private function transform(NewsletterSubscription $subscription): array
    {
        return [
            'id' => $subscription->id,
            'email' => $subscription->email,
            'locale' => $subscription->locale,
            'isConfirmed' => $subscription->is_confirmed,
            'createdAt' => $subscription->created_at?->toIso8601String(),
        ];
    }
}
