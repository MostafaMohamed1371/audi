<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * MariaDB implements JSON columns as LONGTEXT + an inline
 * `CHECK (json_valid(col))` constraint. When the framework tables
 * (cache/sessions/jobs) were first created from an older SQL dump that
 * typed these columns as JSON, the leftover json_valid checks reject
 * Laravel's PHP-serialized cache/session payloads (error 4025).
 *
 * Inline column checks cannot be removed with DROP CONSTRAINT, so we
 * redefine the columns to their correct text types, which clears the check.
 * Idempotent and safe to re-run. No-op on plain MySQL / SQLite.
 */
return new class extends Migration
{
    /**
     * @var array<int, array{0:string,1:string,2:string}>
     */
    private array $columns = [
        ['cache', 'value', 'MEDIUMTEXT NOT NULL'],
        ['sessions', 'payload', 'LONGTEXT NOT NULL'],
        ['jobs', 'payload', 'LONGTEXT NOT NULL'],
        ['failed_jobs', 'payload', 'LONGTEXT NOT NULL'],
        ['failed_jobs', 'exception', 'LONGTEXT NOT NULL'],
        ['job_batches', 'failed_job_ids', 'LONGTEXT NOT NULL'],
        ['job_batches', 'options', 'MEDIUMTEXT NULL'],
    ];

    public function up(): void
    {
        if (Schema::getConnection()->getDriverName() !== 'mysql') {
            return;
        }

        foreach ($this->columns as [$table, $column, $type]) {
            if (! Schema::hasTable($table) || ! Schema::hasColumn($table, $column)) {
                continue;
            }

            try {
                DB::statement(sprintf('ALTER TABLE `%s` MODIFY `%s` %s', $table, $column, $type));
            } catch (\Throwable $e) {
                // Leave as-is if the column cannot be modified on this engine.
            }
        }
    }

    public function down(): void
    {
        // Intentionally irreversible: re-adding json_valid checks would
        // re-break Laravel's serialized cache/session payloads.
    }
};
