<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('countries') || ! Schema::hasColumn('countries', 'geojson')) {
            return;
        }

        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE `countries` MODIFY `geojson` LONGTEXT NULL');
        } elseif ($driver === 'sqlite') {
            // SQLite TEXT is unlimited for practical GeoJSON sizes; no-op.
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('countries') || ! Schema::hasColumn('countries', 'geojson')) {
            return;
        }

        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE `countries` MODIFY `geojson` JSON NULL');
        }
    }
};
