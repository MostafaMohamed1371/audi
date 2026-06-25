<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\Home\HomeService;
use Illuminate\Http\JsonResponse;

class HomeController extends Controller
{
    public function __construct(private readonly HomeService $home) {}

    public function show(): JsonResponse
    {
        return response()->json($this->home->getHome());
    }
}
