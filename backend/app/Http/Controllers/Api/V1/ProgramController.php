<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\Programs\DirectoryService;
use App\Services\Programs\ProgramService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use InvalidArgumentException;

class ProgramController extends Controller
{
    public function __construct(
        private readonly ProgramService $programs,
        private readonly DirectoryService $directory,
    ) {}

    public function show(Request $request, string $slug): JsonResponse
    {
        try {
            $locale = $request->attributes->get('locale', app()->getLocale());

            return response()->json($this->programs->getProgram($slug, $locale));
        } catch (ModelNotFoundException) {
            return response()->json(['message' => 'Program not found.'], 404);
        }
    }

    public function directory(Request $request): JsonResponse
    {
        try {
            $locale = $request->attributes->get('locale', app()->getLocale());
            $tab = (string) $request->query('tab', 'cities');

            return response()->json($this->directory->getDirectory($tab, $locale, [
                'search' => $request->query('search'),
                'country' => $request->query('country', $request->query('countryCode')),
                'citySize' => $request->query('citySize', $request->query('city_size')),
                'limit' => $request->query('limit', $request->query('pageSize')),
                'page' => $request->query('page'),
            ]));
        } catch (InvalidArgumentException) {
            return response()->json(['message' => 'Invalid directory tab.'], 422);
        }
    }

    public function directoryItem(Request $request, string $tab, string $number): JsonResponse
    {
        try {
            $locale = $request->attributes->get('locale', app()->getLocale());

            return response()->json($this->directory->getItem($tab, $number, $locale));
        } catch (ModelNotFoundException) {
            return response()->json(['message' => 'Directory item not found.'], 404);
        } catch (InvalidArgumentException) {
            return response()->json(['message' => 'Invalid directory tab.'], 422);
        }
    }

    public function storeDirectoryDiscussion(Request $request, string $tab, string $number): JsonResponse
    {
        try {
            $validated = $request->validate([
                'authorName' => ['required', 'string', 'max:255'],
                'body' => ['required', 'string', 'max:5000'],
            ]);

            $locale = $request->attributes->get('locale', app()->getLocale());
            $discussion = $this->directory->storeDiscussion(
                $tab,
                $number,
                $validated['authorName'],
                $validated['body'],
                $locale,
            );

            return response()->json([
                'message' => 'Discussion submitted for review.',
                'data' => $discussion,
            ], 201);
        } catch (ModelNotFoundException) {
            return response()->json(['message' => 'Directory item not found.'], 404);
        } catch (InvalidArgumentException) {
            return response()->json(['message' => 'Invalid directory tab.'], 422);
        }
    }
}
