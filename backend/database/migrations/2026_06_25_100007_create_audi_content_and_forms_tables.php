<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('resources', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('title_ar');
            $table->string('title_en');
            $table->date('published_date')->nullable();
            $table->string('image_url')->nullable();
            $table->string('file_url')->nullable();
            $table->string('resource_type')->nullable();
            $table->foreignId('focus_area_id')->nullable()->constrained('focus_areas')->nullOnDelete();
            $table->unsignedSmallInteger('year')->nullable();
            $table->boolean('is_published')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('media_articles', function (Blueprint $table) {
            $table->id();
            $table->string('category', 30);
            $table->string('key')->unique();
            $table->string('slug_ar');
            $table->string('slug_en');
            $table->string('title_ar');
            $table->string('title_en');
            $table->text('description_ar')->nullable();
            $table->text('description_en')->nullable();
            $table->json('body_ar');
            $table->json('body_en');
            $table->date('published_date')->nullable();
            $table->string('image_url')->nullable();
            $table->string('pdf_url')->nullable();
            $table->json('authors_ar')->nullable();
            $table->json('authors_en')->nullable();
            $table->string('event_time')->nullable();
            $table->boolean('is_published')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['category', 'slug_ar']);
            $table->unique(['category', 'slug_en']);
            $table->index(['category', 'is_published', 'published_date']);
        });

        Schema::create('contact_submissions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone');
            $table->string('email');
            $table->text('message');
            $table->string('status', 20)->default('new');
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();
        });

        Schema::create('membership_applications', function (Blueprint $table) {
            $table->id();
            $table->string('organization_name');
            $table->string('contact_name');
            $table->string('email');
            $table->string('phone');
            $table->char('country_code', 2)->nullable();
            $table->string('city')->nullable();
            $table->text('message')->nullable();
            $table->string('status', 20)->default('new');
            $table->timestamps();
        });

        Schema::create('portal_contributions', function (Blueprint $table) {
            $table->id();
            $table->string('type', 30);
            $table->string('email');
            $table->json('payload');
            $table->string('status', 20)->default('new');
            $table->timestamps();
        });

        Schema::create('newsletter_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
            $table->string('locale', 5)->default('ar');
            $table->boolean('is_confirmed')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('newsletter_subscriptions');
        Schema::dropIfExists('portal_contributions');
        Schema::dropIfExists('membership_applications');
        Schema::dropIfExists('contact_submissions');
        Schema::dropIfExists('media_articles');
        Schema::dropIfExists('resources');
    }
};
