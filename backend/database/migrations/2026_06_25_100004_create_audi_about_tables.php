<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('about_content', function (Blueprint $table) {
            $table->id();
            $table->string('section_key')->unique();
            $table->string('title_ar')->nullable();
            $table->string('title_en')->nullable();
            $table->json('body_ar')->nullable();
            $table->json('body_en')->nullable();
            $table->string('image_url')->nullable();
            $table->timestamps();
        });

        Schema::create('leadership_messages', function (Blueprint $table) {
            $table->id();
            $table->string('type', 20)->unique();
            $table->string('name_ar');
            $table->string('name_en');
            $table->string('position_ar');
            $table->string('position_en');
            $table->string('honorific_ar')->nullable();
            $table->string('honorific_en')->nullable();
            $table->text('quote_ar');
            $table->text('quote_en');
            $table->json('paragraphs_ar');
            $table->json('paragraphs_en');
            $table->string('image_url')->nullable();
            $table->string('image_alt_ar')->nullable();
            $table->string('image_alt_en')->nullable();
            $table->timestamps();
        });

        Schema::create('advisory_board_members', function (Blueprint $table) {
            $table->id();
            $table->string('name_ar');
            $table->string('name_en');
            $table->string('role_ar');
            $table->string('role_en');
            $table->text('bio_ar')->nullable();
            $table->text('bio_en')->nullable();
            $table->string('image_url')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('team_sections', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('title_ar');
            $table->string('title_en');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('team_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_section_id')->constrained()->cascadeOnDelete();
            $table->string('name_ar');
            $table->string('name_en');
            $table->string('role_ar');
            $table->string('role_en');
            $table->text('bio_ar')->nullable();
            $table->text('bio_en')->nullable();
            $table->string('image_url')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('partner_categories', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('title_ar');
            $table->string('title_en');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('partners', function (Blueprint $table) {
            $table->id();
            $table->foreignId('partner_category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name_ar');
            $table->string('name_en');
            $table->string('logo_url')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('partners');
        Schema::dropIfExists('partner_categories');
        Schema::dropIfExists('team_members');
        Schema::dropIfExists('team_sections');
        Schema::dropIfExists('advisory_board_members');
        Schema::dropIfExists('leadership_messages');
        Schema::dropIfExists('about_content');
    }
};
