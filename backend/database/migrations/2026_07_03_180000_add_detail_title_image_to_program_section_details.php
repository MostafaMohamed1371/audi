<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('program_section_details', function (Blueprint $table) {
            $table->string('title_ar')->nullable()->after('program_section_id');
            $table->string('title_en')->nullable()->after('title_ar');
            $table->string('image_url')->nullable()->after('title_en');
        });
    }

    public function down(): void
    {
        Schema::table('program_section_details', function (Blueprint $table) {
            $table->dropColumn(['title_ar', 'title_en', 'image_url']);
        });
    }
};
