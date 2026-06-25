<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // FAQ — الأسئلة الشائعة
        Schema::create('faqs', function (Blueprint $table) {
            $table->id();
            $table->string('category')->nullable();
            $table->string('question_ar', 500);
            $table->string('question_en', 500);
            $table->text('answer_ar');
            $table->text('answer_en');
            $table->boolean('is_published')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['is_published', 'sort_order']);
        });

        // Legal pages — الشروط والأحكام / سياسة الخصوصية
        Schema::create('legal_pages', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique(); // terms | privacy
            $table->string('title_ar');
            $table->string('title_en');
            $table->text('content_ar');
            $table->text('content_en');
            $table->date('effective_date')->nullable();
            $table->timestamps();
        });

        // Careers — اعمل معنا (job openings)
        Schema::create('job_openings', function (Blueprint $table) {
            $table->id();
            $table->string('title_ar');
            $table->string('title_en');
            $table->string('location_ar')->nullable();
            $table->string('location_en')->nullable();
            $table->string('employment_type')->default('full_time'); // full_time|part_time|contract|internship
            $table->text('summary_ar')->nullable();
            $table->text('summary_en')->nullable();
            $table->json('description_ar')->nullable(); // array of paragraphs
            $table->json('description_en')->nullable();
            $table->boolean('is_published')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['is_published', 'sort_order']);
        });

        // Careers — job applications (form submissions)
        Schema::create('job_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_opening_id')->nullable()->constrained('job_openings')->nullOnDelete();
            $table->string('full_name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->text('cover_letter')->nullable();
            $table->string('cv_url')->nullable();
            $table->string('status')->default('new'); // new|reviewing|shortlisted|rejected|hired
            $table->timestamps();

            $table->index(['status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_applications');
        Schema::dropIfExists('job_openings');
        Schema::dropIfExists('legal_pages');
        Schema::dropIfExists('faqs');
    }
};
