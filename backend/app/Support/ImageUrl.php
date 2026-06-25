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

        if (str_starts_with($url, 'http://') || str_starts_with($url, 'https://')) {
            return $url;
        }

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
     * Prefix a bare filename with a public asset directory (for seeders / migrations).
     */
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
