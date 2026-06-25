<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('programs', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('title_ar');
            $table->string('title_en');
            $table->text('hero_intro_ar')->nullable();
            $table->text('hero_intro_en')->nullable();
            $table->timestamps();
        });

        Schema::create('program_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_id')->constrained()->cascadeOnDelete();
            $table->string('tab_key');
            $table->string('title_ar');
            $table->string('title_en');
            $table->text('intro_ar')->nullable();
            $table->text('intro_en')->nullable();
            $table->json('body_ar')->nullable();
            $table->json('body_en')->nullable();
            $table->string('image_url')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['program_id', 'tab_key']);
        });

        Schema::create('training_courses', function (Blueprint $table) {
            $table->id();
            $table->string('title_ar');
            $table->string('title_en');
            $table->string('count_ar');
            $table->string('count_en');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('experts', function (Blueprint $table) {
            $table->id();
            $table->string('name_ar');
            $table->string('name_en');
            $table->string('specialty_ar');
            $table->string('specialty_en');
            $table->string('image_url')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('directory_cities', function (Blueprint $table) {
            $table->id();
            $table->string('number', 10);
            $table->string('name_ar');
            $table->string('name_en');
            $table->text('description_ar')->nullable();
            $table->text('description_en')->nullable();
            $table->char('country_code', 2)->nullable();
            $table->string('city_size')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('directory_projects', function (Blueprint $table) {
            $table->id();
            $table->string('number', 10);
            $table->string('city_ar');
            $table->string('city_en');
            $table->string('country_ar');
            $table->string('country_en');
            $table->string('start_date')->nullable();
            $table->string('end_date')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('directory_organizations', function (Blueprint $table) {
            $table->id();
            $table->string('number', 10);
            $table->string('name_ar');
            $table->string('name_en');
            $table->text('description_ar')->nullable();
            $table->text('description_en')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('directory_publications', function (Blueprint $table) {
            $table->id();
            $table->string('number', 10);
            $table->string('name_ar');
            $table->string('name_en');
            $table->text('description_ar')->nullable();
            $table->text('description_en')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('directory_publications');
        Schema::dropIfExists('directory_organizations');
        Schema::dropIfExists('directory_projects');
        Schema::dropIfExists('directory_cities');
        Schema::dropIfExists('experts');
        Schema::dropIfExists('training_courses');
        Schema::dropIfExists('program_sections');
        Schema::dropIfExists('programs');
    }
};
