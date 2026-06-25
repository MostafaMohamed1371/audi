<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $isAr = app()->getLocale() === 'ar';

        $query = Faq::query()
            ->where('is_published', true)
            ->ordered();

        if ($category = $request->query('category')) {
            $query->where('category', $category);
        }

        $items = $query->get()->map(fn (Faq $faq) => [
            'id' => $faq->id,
            'category' => $faq->category,
            'question' => $isAr ? $faq->question_ar : $faq->question_en,
            'answer' => $isAr ? $faq->answer_ar : $faq->answer_en,
        ])->values();

        return response()->json(['data' => $items]);
    }
}
