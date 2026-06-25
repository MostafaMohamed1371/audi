<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Admin\UploadFileRequest;
use App\Models\Upload;
use App\Support\ImageUrl;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UploadController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $limit = min(max((int) $request->query('limit', 20), 1), 100);

        $query = Upload::query()->with('uploader')->latest();

        if ($mime = $request->query('mimeType', $request->query('mime_type'))) {
            $query->where('mime_type', 'like', "{$mime}%");
        }

        if ($search = $request->query('search')) {
            $query->where(function ($builder) use ($search) {
                $builder
                    ->where('original_name', 'like', "%{$search}%")
                    ->orWhere('url', 'like', "%{$search}%");
            });
        }

        $paginator = $query->paginate($limit);

        return response()->json([
            'data' => $paginator->getCollection()->map(fn (Upload $upload) => $this->transform($upload))->values(),
            'meta' => [
                'page' => $paginator->currentPage(),
                'limit' => $paginator->perPage(),
                'total' => $paginator->total(),
                'totalPages' => $paginator->lastPage(),
            ],
        ]);
    }

    public function store(UploadFileRequest $request): JsonResponse
    {
        $file = $request->file('file');
        $disk = 'public';
        $directory = 'uploads/'.now()->format('Y/m');
        $filename = Str::uuid()->toString().'.'.$file->getClientOriginalExtension();
        $path = $file->storeAs($directory, $filename, $disk);
        $url = ImageUrl::absolute(Storage::disk($disk)->url($path));

        $upload = Upload::query()->create([
            'disk' => $disk,
            'path' => $path,
            'url' => $url,
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
            'original_name' => $file->getClientOriginalName(),
            'uploaded_by' => $request->user()?->id,
        ]);

        return response()->json([
            'data' => $this->transform($upload),
        ], 201);
    }

    public function show(Upload $upload): JsonResponse
    {
        return response()->json(['data' => $this->transform($upload->load('uploader'))]);
    }

    public function destroy(Upload $upload): JsonResponse
    {
        if ($upload->path && Storage::disk($upload->disk)->exists($upload->path)) {
            Storage::disk($upload->disk)->delete($upload->path);
        }

        $upload->delete();

        return response()->json(['message' => 'Deleted']);
    }

    /**
     * @return array<string, mixed>
     */
    private function transform(Upload $upload): array
    {
        return [
            'id' => $upload->id,
            'url' => $upload->url,
            'mimeType' => $upload->mime_type,
            'originalName' => $upload->original_name,
            'size' => $upload->size,
            'disk' => $upload->disk,
            'path' => $upload->path,
            'uploadedBy' => $upload->uploader?->name,
            'createdAt' => $upload->created_at?->toIso8601String(),
        ];
    }
}
