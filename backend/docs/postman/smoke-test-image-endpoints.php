<?php
/**
 * Smoke-test public endpoints affected by image-path alignment (Phases 1–6).
 *
 * Usage:
 *   php backend/docs/postman/smoke-test-image-endpoints.php
 *   php backend/docs/postman/smoke-test-image-endpoints.php http://127.0.0.1:8000 ar
 */

declare(strict_types=1);

$baseUrl = rtrim($argv[1] ?? 'http://127.0.0.1:8000', '/');
$locale = $argv[2] ?? 'ar';

$endpoints = [
    [
        'name' => 'GET /api/v1/home',
        'path' => '/api/v1/home',
        'check' => static function (array $json): array {
            $errors = [];
            foreach ($json['slider'] ?? [] as $i => $slide) {
                if (! empty($slide['imageUrl']) && ! isFullPath((string) $slide['imageUrl'])) {
                    $errors[] = "slider[{$i}].imageUrl = {$slide['imageUrl']}";
                }
            }
            foreach (array_merge($json['mediaCenter']['featured'] ?? [], $json['mediaCenter']['items'] ?? []) as $i => $row) {
                if (! empty($row['image']) && ! isFullPath((string) $row['image'])) {
                    $errors[] = "mediaCenter[{$i}].image = {$row['image']}";
                }
            }
            foreach ($json['knowledgeCenter']['items'] ?? [] as $i => $row) {
                if (! empty($row['image']) && ! isFullPath((string) $row['image'])) {
                    $errors[] = "knowledgeCenter[{$i}].image = {$row['image']}";
                }
            }

            return $errors;
        },
    ],
    [
        'name' => 'GET /api/v1/about/advisory-board',
        'path' => '/api/v1/about/advisory-board',
        'check' => static fn (array $json) => checkListImages($json['members'] ?? [], 'image', 'members'),
    ],
    [
        'name' => 'GET /api/v1/about/team',
        'path' => '/api/v1/about/team',
        'check' => static function (array $json): array {
            $errors = [];
            foreach ($json['sections'] ?? [] as $si => $section) {
                foreach ($section['members'] ?? [] as $mi => $member) {
                    if (! empty($member['image']) && ! isFullPath((string) $member['image'])) {
                        $errors[] = "sections[{$si}].members[{$mi}].image = {$member['image']}";
                    }
                }
            }

            return $errors;
        },
    ],
    [
        'name' => 'GET /api/v1/about/partners',
        'path' => '/api/v1/about/partners',
        'check' => static function (array $json): array {
            $errors = checkListImages($json['featured'] ?? [], 'image', 'featured');
            foreach ($json['categories'] ?? [] as $ci => $cat) {
                $errors = array_merge($errors, checkListImages($cat['logos'] ?? [], 'image', "categories[{$ci}].logos"));
            }

            return $errors;
        },
    ],
    [
        'name' => 'GET /api/v1/resources',
        'path' => '/api/v1/resources?page=1',
        'check' => static fn (array $json) => checkListImages($json['data'] ?? [], 'image', 'data'),
    ],
    [
        'name' => 'GET /api/v1/media/news',
        'path' => '/api/v1/media/news?page=1',
        'check' => static fn (array $json) => checkListImages($json['data'] ?? [], 'image', 'data'),
    ],
    [
        'name' => 'GET /api/v1/about/leadership/director',
        'path' => '/api/v1/about/leadership/director',
        'check' => static function (array $json): array {
            if (! empty($json['image']) && ! isFullPath((string) $json['image'])) {
                return ["image = {$json['image']}"];
            }

            return [];
        },
    ],
    [
        'name' => 'GET /api/v1/strategy/focus-areas',
        'path' => '/api/v1/strategy/focus-areas',
        'check' => static function (array $json): array {
            $errors = [];
            foreach ($json['items'] ?? [] as $i => $item) {
                foreach (['listImage', 'detailImage'] as $field) {
                    if (! empty($item[$field]) && ! isFullPath((string) $item[$field])) {
                        $errors[] = "items[{$i}].{$field} = {$item[$field]}";
                    }
                }
            }

            return $errors;
        },
    ],
];

function isFullPath(string $value): bool
{
    return str_starts_with($value, '/')
        || str_starts_with($value, 'http://')
        || str_starts_with($value, 'https://');
}

/**
 * @param  array<int, array<string, mixed>>  $rows
 * @return list<string>
 */
function checkListImages(array $rows, string $field, string $label): array
{
    $errors = [];
    foreach ($rows as $i => $row) {
        if (! empty($row[$field]) && ! isFullPath((string) $row[$field])) {
            $errors[] = "{$label}[{$i}].{$field} = {$row[$field]}";
        }
    }

    return $errors;
}

$passed = 0;
$failed = 0;

echo "AUDI image-path smoke test\n";
echo "Base URL: {$baseUrl}\n";
echo "Locale:   {$locale}\n\n";

foreach ($endpoints as $endpoint) {
    $url = $baseUrl.$endpoint['path'];
    $context = stream_context_create([
        'http' => [
            'method' => 'GET',
            'header' => "Accept: application/json\r\nAccept-Language: {$locale}\r\n",
            'ignore_errors' => true,
            'timeout' => 10,
        ],
    ]);

    $body = @file_get_contents($url, false, $context);
    $statusLine = $http_response_header[0] ?? 'HTTP/1.1 000';
    preg_match('/\d{3}/', $statusLine, $codeMatch);
    $status = (int) ($codeMatch[0] ?? 0);

    if ($body === false || $status < 200 || $status >= 300) {
        echo "FAIL {$endpoint['name']} — HTTP {$status}\n";
        $failed++;
        continue;
    }

    /** @var array<string, mixed> $json */
    $json = json_decode($body, true) ?? [];
    $errors = ($endpoint['check'])($json);

    if ($errors === []) {
        echo "PASS {$endpoint['name']}\n";
        $passed++;
    } else {
        echo "FAIL {$endpoint['name']} — bare image paths:\n";
        foreach ($errors as $error) {
            echo "  - {$error}\n";
        }
        $failed++;
    }
}

echo "\nResult: {$passed} passed, {$failed} failed\n";

exit($failed > 0 ? 1 : 0);
