<?php

declare(strict_types=1);

namespace App\Support;

final class ImageUrl
{
    /**
     * Normalize a stored image path for public API responses.
     * Returns an absolute URL when FRONTEND_URL / APP_URL are configured.
     *
     * - `/storage/*` → APP_URL (Laravel uploads)
     * - `/emp/*`, `/blog/*`, … → FRONTEND_URL (Next.js public assets)
     * - Already absolute → unchanged
     */
    public static function public(?string $url): ?string
    {
        if ($url === null || $url === '') {
            return $url;
        }

        if ($url === '#') {
            return $url;
        }

        if (str_starts_with($url, 'http://') || str_starts_with($url, 'https://')) {
            return $url;
        }

        $url = self::normalizeStoredPath($url);

        if (! str_starts_with($url, '/')) {
            return $url;
        }

        if (str_starts_with($url, '/storage/')) {
            return self::absolute($url);
        }

        $frontend = config('app.frontend_url');

        if ($frontend) {
            return rtrim((string) $frontend, '/').$url;
        }

        return $url;
    }

    /**
     * Normalize DB-stored paths to root-relative form before resolving.
     */
    public static function normalizeStoredPath(string $url): string
    {
        $url = trim($url);

        if (str_starts_with($url, 'storage/')) {
            return '/'.$url;
        }

        if (str_starts_with($url, 'uploads/')) {
            return '/storage/'.$url;
        }

        return $url;
    }

    /**
     * Resolve file/image paths in nested JSON (e.g. about-content bodyAr/bodyEn).
     *
     * @param  array<string, mixed>|null  $data
     * @return array<string, mixed>|null
     */
    public static function mapBodyPaths(?array $data): ?array
    {
        if ($data === null) {
            return null;
        }

        $pathKeys = [
            'imageUrl', 'visionImage', 'missionImage', 'image', 'logoUrl',
            'listImage', 'detailImage', 'listImageUrl', 'detailImageUrl',
            'heroImage', 'coursesImage', 'heroVideo',
            'fileUrl', 'pdfUrl', 'pdfHref', 'downloadHref', 'cvUrl',
        ];

        foreach ($data as $key => $value) {
            if (is_string($value) && in_array($key, $pathKeys, true)) {
                $data[$key] = self::public($value);
            } elseif (is_array($value)) {
                $data[$key] = self::mapBodyPaths($value);
            }
        }

        return $data;
    }

    /**
     * Alias for API responses (public + admin) — absolute URL when configured.
     */
    public static function api(?string $url): ?string
    {
        return self::public($url);
    }
    public static function publicAsset(?string $path, string $directory): ?string
    {
        if ($path === null || $path === '') {
            return null;
        }

        if (self::isAbsoluteOrRootRelative($path)) {
            return $path;
        }

        return '/'.trim($directory, '/').'/'.ltrim($path, '/');
    }

    /**
     * Build an absolute URL for uploaded files (cross-origin frontend consumption).
     */
    public static function absolute(?string $url): ?string
    {
        if ($url === null || $url === '') {
            return $url;
        }

        if (str_starts_with($url, 'http://') || str_starts_with($url, 'https://')) {
            return $url;
        }

        $url = self::normalizeStoredPath($url);
        $base = rtrim((string) config('app.url'), '/');

        return $base.'/'.ltrim($url, '/');
    }

    public static function isAbsoluteOrRootRelative(string $path): bool
    {
        return str_starts_with($path, '/')
            || str_starts_with($path, 'http://')
            || str_starts_with($path, 'https://');
    }
}
