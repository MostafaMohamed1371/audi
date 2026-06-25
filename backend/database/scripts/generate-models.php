<?php
/** @phpstan-ignore-file */
declare(strict_types=1);

$modelsDir = dirname(__DIR__, 2) . '/app/Models';

$models = [
    'SiteSetting' => [
        'fillable' => ['key', 'value_ar', 'value_en', 'group'],
    ],
    'SocialLink' => [
        'fillable' => ['platform', 'url', 'icon', 'sort_order', 'is_active'],
        'casts' => ['is_active' => 'boolean'],
        'traits' => ['HasSortOrder'],
    ],
    'Upload' => [
        'fillable' => ['disk', 'path', 'url', 'mime_type', 'size', 'original_name', 'uploaded_by'],
        'casts' => ['size' => 'integer'],
        'relations' => ['belongsTo' => ['User', 'uploaded_by']],
    ],
    'HomeHeroSlide' => [
        'fillable' => ['title_ar', 'title_en', 'image_url', 'sort_order', 'is_active'],
        'casts' => ['is_active' => 'boolean'],
        'traits' => ['HasSortOrder', 'LocalizesAttributes'],
    ],
    'HomeStat' => [
        'fillable' => ['value', 'label_ar', 'label_en', 'description_ar', 'description_en', 'sort_order'],
        'traits' => ['HasSortOrder', 'LocalizesAttributes'],
    ],
    'Country' => [
        'fillable' => ['code_a2', 'code_a3', 'name_en', 'name_ar', 'geojson'],
        'casts' => ['geojson' => 'array'],
        'incrementing' => false,
        'keyType' => 'string',
        'primaryKey' => 'code_a2',
        'relations' => ['hasMany' => ['MemberCity', 'country_code', 'code_a2']],
    ],
    'MemberCity' => [
        'fillable' => ['country_code', 'name_ar', 'name_en', 'latitude', 'longitude', 'info_ar', 'info_en', 'image_url', 'is_active'],
        'casts' => ['latitude' => 'decimal:6', 'longitude' => 'decimal:6', 'is_active' => 'boolean'],
        'traits' => ['LocalizesAttributes'],
        'relations' => ['belongsTo' => ['Country', 'country_code', 'code_a2']],
    ],
    'MemberCityStat' => [
        'fillable' => ['key', 'value', 'label_ar', 'label_en', 'unit_ar', 'unit_en', 'auto_calculate'],
        'casts' => ['auto_calculate' => 'boolean'],
        'incrementing' => false,
        'keyType' => 'string',
        'primaryKey' => 'key',
    ],
    'AboutContent' => [
        'fillable' => ['section_key', 'title_ar', 'title_en', 'body_ar', 'body_en', 'image_url'],
        'casts' => ['body_ar' => 'array', 'body_en' => 'array'],
        'traits' => ['LocalizesAttributes'],
    ],
    'LeadershipMessage' => [
        'fillable' => ['type', 'name_ar', 'name_en', 'position_ar', 'position_en', 'honorific_ar', 'honorific_en', 'quote_ar', 'quote_en', 'paragraphs_ar', 'paragraphs_en', 'image_url', 'image_alt_ar', 'image_alt_en'],
        'casts' => ['paragraphs_ar' => 'array', 'paragraphs_en' => 'array'],
        'traits' => ['LocalizesAttributes'],
    ],
    'AdvisoryBoardMember' => [
        'fillable' => ['name_ar', 'name_en', 'role_ar', 'role_en', 'bio_ar', 'bio_en', 'image_url', 'is_featured', 'sort_order'],
        'casts' => ['is_featured' => 'boolean'],
        'traits' => ['HasSortOrder', 'LocalizesAttributes'],
    ],
    'TeamSection' => [
        'fillable' => ['slug', 'title_ar', 'title_en', 'sort_order'],
        'traits' => ['HasSortOrder', 'LocalizesAttributes'],
        'relations' => ['hasManyOrdered' => ['TeamMember']],
    ],
    'TeamMember' => [
        'fillable' => ['team_section_id', 'name_ar', 'name_en', 'role_ar', 'role_en', 'bio_ar', 'bio_en', 'image_url', 'sort_order'],
        'traits' => ['HasSortOrder', 'LocalizesAttributes'],
        'relations' => ['belongsTo' => ['TeamSection', 'team_section_id']],
    ],
    'PartnerCategory' => [
        'fillable' => ['slug', 'title_ar', 'title_en', 'sort_order'],
        'traits' => ['HasSortOrder', 'LocalizesAttributes'],
        'relations' => ['hasManyOrdered' => ['Partner']],
    ],
    'Partner' => [
        'fillable' => ['partner_category_id', 'name_ar', 'name_en', 'logo_url', 'is_featured', 'sort_order'],
        'casts' => ['is_featured' => 'boolean'],
        'traits' => ['HasSortOrder', 'LocalizesAttributes'],
        'relations' => ['belongsTo' => ['PartnerCategory', 'partner_category_id']],
    ],
    'StrategyPage' => [
        'fillable' => ['slug', 'booklet_title_ar', 'booklet_title_en', 'booklet_pdf_url', 'intro_title_ar', 'intro_title_en', 'intro_subtitle_ar', 'intro_subtitle_en'],
        'traits' => ['LocalizesAttributes'],
    ],
    'StrategyPillar' => [
        'fillable' => ['number', 'text_ar', 'text_en', 'sort_order'],
        'traits' => ['HasSortOrder', 'LocalizesAttributes'],
    ],
    'StrategyDiagramItem' => [
        'fillable' => ['item_key', 'title_ar', 'title_en', 'content_ar', 'content_en', 'columns_ar', 'columns_en', 'sort_order'],
        'casts' => ['columns_ar' => 'array', 'columns_en' => 'array'],
        'traits' => ['HasSortOrder', 'LocalizesAttributes'],
    ],
    'FocusArea' => [
        'fillable' => ['slug', 'number', 'title_ar', 'title_en', 'highlight_ar', 'highlight_en', 'tags_ar', 'tags_en', 'description_ar', 'description_en', 'list_image_url', 'detail_image_url', 'is_published', 'sort_order'],
        'casts' => ['tags_ar' => 'array', 'tags_en' => 'array', 'is_published' => 'boolean'],
        'traits' => ['HasSortOrder', 'LocalizesAttributes'],
        'relations' => ['hasMany' => ['Resource']],
    ],
    'Program' => [
        'fillable' => ['slug', 'title_ar', 'title_en', 'hero_intro_ar', 'hero_intro_en'],
        'traits' => ['LocalizesAttributes'],
        'relations' => ['hasManyOrdered' => ['ProgramSection']],
    ],
    'ProgramSection' => [
        'fillable' => ['program_id', 'tab_key', 'title_ar', 'title_en', 'intro_ar', 'intro_en', 'body_ar', 'body_en', 'image_url', 'sort_order'],
        'casts' => ['body_ar' => 'array', 'body_en' => 'array'],
        'traits' => ['HasSortOrder', 'LocalizesAttributes'],
        'relations' => ['belongsTo' => ['Program', 'program_id']],
    ],
    'TrainingCourse' => [
        'fillable' => ['title_ar', 'title_en', 'count_ar', 'count_en', 'sort_order'],
        'traits' => ['HasSortOrder', 'LocalizesAttributes'],
    ],
    'Expert' => [
        'fillable' => ['name_ar', 'name_en', 'specialty_ar', 'specialty_en', 'image_url', 'sort_order'],
        'traits' => ['HasSortOrder', 'LocalizesAttributes'],
    ],
    'DirectoryCity' => [
        'table' => 'directory_cities',
        'fillable' => ['number', 'name_ar', 'name_en', 'description_ar', 'description_en', 'country_code', 'city_size', 'sort_order'],
        'traits' => ['HasSortOrder', 'LocalizesAttributes'],
    ],
    'DirectoryProject' => [
        'table' => 'directory_projects',
        'fillable' => ['number', 'city_ar', 'city_en', 'country_ar', 'country_en', 'start_date', 'end_date', 'sort_order'],
        'traits' => ['HasSortOrder', 'LocalizesAttributes'],
    ],
    'DirectoryOrganization' => [
        'table' => 'directory_organizations',
        'fillable' => ['number', 'name_ar', 'name_en', 'description_ar', 'description_en', 'sort_order'],
        'traits' => ['HasSortOrder', 'LocalizesAttributes'],
    ],
    'DirectoryPublication' => [
        'table' => 'directory_publications',
        'fillable' => ['number', 'name_ar', 'name_en', 'description_ar', 'description_en', 'sort_order'],
        'traits' => ['HasSortOrder', 'LocalizesAttributes'],
    ],
    'Resource' => [
        'fillable' => ['slug', 'title_ar', 'title_en', 'published_date', 'image_url', 'file_url', 'resource_type', 'focus_area_id', 'year', 'is_published', 'sort_order'],
        'casts' => ['published_date' => 'date', 'is_published' => 'boolean', 'year' => 'integer'],
        'traits' => ['HasSortOrder', 'LocalizesAttributes'],
        'relations' => ['belongsTo' => ['FocusArea', 'focus_area_id']],
    ],
    'MediaArticle' => [
        'fillable' => ['category', 'key', 'slug_ar', 'slug_en', 'title_ar', 'title_en', 'description_ar', 'description_en', 'body_ar', 'body_en', 'published_date', 'image_url', 'pdf_url', 'authors_ar', 'authors_en', 'event_time', 'is_published', 'sort_order'],
        'casts' => ['body_ar' => 'array', 'body_en' => 'array', 'authors_ar' => 'array', 'authors_en' => 'array', 'published_date' => 'date', 'is_published' => 'boolean'],
        'traits' => ['HasSortOrder', 'LocalizesAttributes'],
    ],
    'ContactSubmission' => [
        'fillable' => ['name', 'phone', 'email', 'message', 'status', 'ip_address', 'user_agent'],
    ],
    'MembershipApplication' => [
        'fillable' => ['organization_name', 'contact_name', 'email', 'phone', 'country_code', 'city', 'message', 'status'],
    ],
    'PortalContribution' => [
        'fillable' => ['type', 'email', 'payload', 'status'],
        'casts' => ['payload' => 'array'],
    ],
    'NewsletterSubscription' => [
        'fillable' => ['email', 'locale', 'is_confirmed'],
        'casts' => ['is_confirmed' => 'boolean'],
    ],
];

function buildRelations(array $relations): string
{
    $methods = '';
    foreach ($relations as $type => $args) {
        match ($type) {
            'belongsTo' => $methods .= relationBelongsTo(...$args),
            'hasMany' => $methods .= relationHasMany($args[0]),
            'hasManyOrdered' => $methods .= relationHasManyOrdered($args[0]),
            default => null,
        };
    }

    return $methods;
}

function relationBelongsTo(string $model, string $foreignKey, ?string $ownerKey = null): string
{
    $owner = $ownerKey ? ", '{$ownerKey}'" : '';
    $method = match ($model) {
        'User' => 'uploader',
        'TeamSection' => 'section',
        'PartnerCategory' => 'category',
        'FocusArea' => 'focusArea',
        default => lcfirst($model),
    };

    return <<<PHP

    public function {$method}(): BelongsTo
    {
        return \$this->belongsTo({$model}::class, '{$foreignKey}'{$owner});
    }

PHP;
}

function relationHasMany(string $model): string
{
    $method = lcfirst($model).'s';
    if ($model === 'Country') {
        $method = 'memberCities';
    }

    return <<<PHP

    public function {$method}(): HasMany
    {
        return \$this->hasMany({$model}::class);
    }

PHP;
}

function relationHasManyOrdered(string $model): string
{
    $method = match ($model) {
        'TeamMember' => 'members',
        'Partner' => 'partners',
        'ProgramSection' => 'sections',
        default => lcfirst($model).'s',
    };

    return <<<PHP

    public function {$method}(): HasMany
    {
        return \$this->hasMany({$model}::class)->ordered();
    }

PHP;
}

foreach ($models as $class => $config) {
    $traits = $config['traits'] ?? [];
    $imports = [
        'use Illuminate\\Database\\Eloquent\\Factories\\HasFactory;',
        'use Illuminate\\Database\\Eloquent\\Model;',
    ];

    $relations = buildRelations($config['relations'] ?? []);
    if (str_contains($relations, 'BelongsTo')) {
        $imports[] = 'use Illuminate\\Database\\Eloquent\\Relations\\BelongsTo;';
    }
    if (str_contains($relations, 'HasMany')) {
        $imports[] = 'use Illuminate\\Database\\Eloquent\\Relations\\HasMany;';
    }
    foreach ($traits as $trait) {
        $imports[] = "use App\\Models\\Concerns\\{$trait};";
    }

    $traitList = array_merge(['HasFactory'], $traits);
    $traitUse = implode(', ', $traitList);

    $fillable = str_replace(['array (', ')'], ['[', ']'], var_export($config['fillable'], true));
    $fillable = preg_replace('/\d+ => /', '', $fillable) ?? $fillable;

    $castsBlock = '';
    if (! empty($config['casts'])) {
        $casts = str_replace(['array (', ')'], ['[', ']'], var_export($config['casts'], true));
        $casts = preg_replace('/\d+ => /', '', $casts) ?? $casts;
        $castsBlock = "\n    protected function casts(): array\n    {\n        return {$casts};\n    }\n";
    }

    $table = isset($config['table']) ? "\n    protected \$table = '{$config['table']}';\n" : '';
    $pk = '';
    if (($config['incrementing'] ?? true) === false) {
        $pk = "\n    public \$incrementing = false;\n    protected \$keyType = '{$config['keyType']}';\n    protected \$primaryKey = '{$config['primaryKey']}';\n";
    }

    $importsBlock = implode("\n", array_unique($imports));

    $content = <<<PHP
<?php

declare(strict_types=1);

namespace App\Models;

{$importsBlock}

class {$class} extends Model
{
    use {$traitUse};{$table}{$pk}
    protected \$fillable = {$fillable};
{$castsBlock}{$relations}}

PHP;

    file_put_contents("{$modelsDir}/{$class}.php", $content);
    echo "Created {$class}.php\n";
}

echo "Done.\n";
