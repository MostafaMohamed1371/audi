<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\PortalContribution;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PortalContributionController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'type' => ['required', Rule::in(['publications', 'cities', 'organizations'])],
            'email' => ['required', 'email', 'max:255'],
            'payload' => ['required', 'array'],
        ]);

        $contribution = PortalContribution::query()->create([
            'type' => $validated['type'],
            'email' => $validated['email'],
            'payload' => $validated['payload'],
            'status' => 'new',
        ]);

        return response()->json([
            'message' => 'Contribution submitted successfully.',
            'id' => $contribution->id,
        ], 201);
    }
}
