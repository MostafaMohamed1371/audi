<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['directory_cities', 'directory_projects', 'directory_organizations', 'directory_publications'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->json('detail_ar')->nullable();
                $table->json('detail_en')->nullable();
            });
        }

        Schema::create('directory_discussions', function (Blueprint $table) {
            $table->id();
            $table->string('directory_type', 32);
            $table->string('directory_number', 10);
            $table->string('author_name_ar');
            $table->string('author_name_en');
            $table->text('body_ar');
            $table->text('body_en');
            $table->boolean('is_approved')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['directory_type', 'directory_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('directory_discussions');

        foreach (['directory_cities', 'directory_projects', 'directory_organizations', 'directory_publications'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->dropColumn(['detail_ar', 'detail_en']);
            });
        }
    }
};
