<?php

declare(strict_types=1);

namespace App\Support;

final class ProgramContentKey
{
    /** @var list<string> */
    private const SLUGS = ['urban-policies', 'training', 'partnerships'];

    public static function metaKey(string $slug): string
    {
        return 'program_'.$slug;
    }

    public static function sectionKey(string $slug, string $tabKey): string
    {
        return 'program_'.$slug.'_'.$tabKey;
    }

    /**
     * @return array{type: 'meta', slug: string}|array{type: 'section', slug: string, tabKey: string}|null
     */
    public static function parse(string $sectionKey): ?array
    {
        if (! str_starts_with($sectionKey, 'program_')) {
            return null;
        }

        $suffix = substr($sectionKey, strlen('program_'));

        foreach (self::SLUGS as $slug) {
            if ($suffix === $slug) {
                return ['type' => 'meta', 'slug' => $slug];
            }

            $prefix = $slug.'_';

            if (str_starts_with($suffix, $prefix)) {
                $tabKey = substr($suffix, strlen($prefix));

                if ($tabKey !== '') {
                    return ['type' => 'section', 'slug' => $slug, 'tabKey' => $tabKey];
                }
            }
        }

        return null;
    }
}
