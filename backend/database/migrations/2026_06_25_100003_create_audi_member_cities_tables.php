<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('countries', function (Blueprint $table) {
            $table->char('code_a2', 2)->primary();
            $table->char('code_a3', 3)->nullable();
            $table->string('name_en');
            $table->string('name_ar')->nullable();
            $table->longText('geojson')->nullable();
            $table->timestamps();
        });

        Schema::create('member_cities', function (Blueprint $table) {
            $table->id();
            $table->char('country_code', 2);
            $table->string('name_ar');
            $table->string('name_en');
            $table->decimal('latitude', 9, 6);
            $table->decimal('longitude', 9, 6);
            $table->text('info_ar')->nullable();
            $table->text('info_en')->nullable();
            $table->string('image_url')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('country_code')->references('code_a2')->on('countries')->cascadeOnUpdate();
            $table->index(['country_code', 'is_active']);
        });

        Schema::create('member_city_stats', function (Blueprint $table) {
            $table->string('key', 20)->primary();
            $table->unsignedInteger('value')->nullable();
            $table->string('label_ar');
            $table->string('label_en');
            $table->string('unit_ar');
            $table->string('unit_en');
            $table->boolean('auto_calculate')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('member_city_stats');
        Schema::dropIfExists('member_cities');
        Schema::dropIfExists('countries');
    }
};
