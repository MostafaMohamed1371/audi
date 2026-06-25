<?php

use App\Support\ImageUrl;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Prefix legacy bare filenames with their public asset directories.
     */
    public function up(): void
    {
        $this->backfillColumn('advisory_board_members', 'image_url', 'emp');
        $this->backfillColumn('team_members', 'image_url', 'emp');
        $this->backfillColumn('experts', 'image_url', 'emp');
        $this->backfillColumn('partners', 'logo_url', 'client');
        $this->backfillColumn('media_articles', 'image_url', 'blog');
        $this->backfillColumn('resources', 'image_url', 'our-sources');
    }

    public function down(): void
    {
        // Non-destructive: leave prefixed paths in place.
    }

    private function backfillColumn(string $table, string $column, string $directory): void
    {
        DB::table($table)
            ->whereNotNull($column)
            ->where($column, '!=', '')
            ->orderBy('id')
            ->each(function (object $row) use ($table, $column, $directory) {
                $current = (string) $row->{$column};

                if (ImageUrl::isAbsoluteOrRootRelative($current)) {
                    return;
                }

                DB::table($table)
                    ->where('id', $row->id)
                    ->update([
                        $column => ImageUrl::publicAsset($current, $directory),
                    ]);
            });
    }
};
