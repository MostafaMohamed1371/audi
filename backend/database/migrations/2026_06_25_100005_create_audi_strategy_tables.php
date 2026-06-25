<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('strategy_pages', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->default('strategy-2025')->unique();
            $table->string('booklet_title_ar')->nullable();
            $table->string('booklet_title_en')->nullable();
            $table->string('booklet_pdf_url')->nullable();
            $table->text('intro_title_ar')->nullable();
            $table->text('intro_title_en')->nullable();
            $table->text('intro_subtitle_ar')->nullable();
            $table->text('intro_subtitle_en')->nullable();
            $table->timestamps();
        });

        Schema::create('strategy_pillars', function (Blueprint $table) {
            $table->id();
            $table->string('number', 4);
            $table->text('text_ar');
            $table->text('text_en');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('strategy_diagram_items', function (Blueprint $table) {
            $table->id();
            $table->string('item_key')->unique();
            $table->string('title_ar');
            $table->string('title_en');
            $table->text('content_ar')->nullable();
            $table->text('content_en')->nullable();
            $table->json('columns_ar')->nullable();
            $table->json('columns_en')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('focus_areas', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('number', 4);
            $table->string('title_ar');
            $table->string('title_en');
            $table->string('highlight_ar');
            $table->string('highlight_en');
            $table->json('tags_ar');
            $table->json('tags_en');
            $table->text('description_ar');
            $table->text('description_en');
            $table->string('list_image_url')->nullable();
            $table->string('detail_image_url')->nullable();
            $table->boolean('is_published')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('focus_areas');
        Schema::dropIfExists('strategy_diagram_items');
        Schema::dropIfExists('strategy_pillars');
        Schema::dropIfExists('strategy_pages');
    }
};
